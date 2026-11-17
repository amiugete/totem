
<?php
/*
 * ============================================================
 * LOGIN LDAP - SIT BACKOFFICE
 * ============================================================
 *
 * Autenticazione degli utenti tramite Active Directory LDAP.
 *
 * Dopo l'autenticazione viene creata una normale sessione PHP.
 * Il SIT continua a gestire autonomamente utenti, ruoli e permessi.
 *
 * NOTA:
 * In questa versione manteniamo il sistema LDAP attuale.
 * L'integrazione con Microsoft Entra ID verrà affrontata
 * separatamente, senza modificare il resto dell'applicazione.
 * ============================================================
 * 
 * Script Name: ldapLogin.php
Author: Roberto Marzochhi modifying a file of Riontino Raffaele
Author URI: https://www.lelezapp.it/
Description: example script for ldap authentication in PHP
Version: 2.0
 */


/*
 * ------------------------------------------------------------
 * 1. AVVIO SESSIONE E CARICAMENTO CONFIGURAZIONE
 * ------------------------------------------------------------
 *
 * Manteniamo per ora il sistema di sessione già utilizzato
 * da questa installazione.
 *

 */



require_once __DIR__ . '/includes/session_check.php';




// Token CSRF: se non esiste già nella sessione, ne generiamo uno.
if (empty($_SESSION['csrf_token'])) {
	//random_bytes(32) genera 32 byte casuali, mentre bin2hex() li converte in una stringa esadecimale di 64 caratteri.	
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}



//$lifetime = 8 * 60 * 60 ; // secondi per sessione logica (8 h)


/*
 * Caricamento delle variabili di ambiente e della connessione.
 *
 * carica_env.php:
 * carica le configurazioni dell'applicazione, compreso APP_ENV.
 *
 * conn_ok.php:
 * rende disponibili i parametri di connessione LDAP.
 */

//require_once 'carica_env.php';
require_once __DIR__ . '/conn_ok.php';


# per il captcha
$turnstileSiteKey = $_ENV['TURNSTILE_SITE_KEY'] ?? '';
$turnstileSecretKey = $_ENV['TURNSTILE_SECRET_KEY'] ?? '';

if ($turnstileSiteKey === '' || $turnstileSecretKey === '') {
    error_log('Configurazione Turnstile mancante.');
}



/**
 * Verifica i tentativi falliti per username e IP
 * negli ultimi 15 minuti.
 *
 * Restituisce 0 se il login può procedere,
 * altrimenti il numero di secondi da attendere.
 */
function verificaTentativiLogin($connessione, string $username, string $ip): int
{
    

    $sql = "
    SELECT
        COUNT(*) AS tentativi,
        coalesce(EXTRACT(EPOCH FROM (
            NOW() - MAX(attempted_at)
        )),0) AS secondi_trascorsi
    FROM totem.login_attempts
    WHERE username = $1
      AND ip_address = $2::inet
      AND success = FALSE
      AND attempted_at > NOW() - INTERVAL '15 minutes'
      AND attempted_at > COALESCE(
          (
              SELECT MAX(attempted_at)
              FROM totem.login_attempts
              WHERE username = $1
                AND ip_address = $2::inet
                AND success = TRUE
          ),
          '-infinity'::timestamptz
      )
";
    

    $result = pg_query_params($connessione, $sql, [$username, $ip]);
    
    if ($result === false) {
        error_log('Errore verifica tentativi login: ' . pg_last_error($connessione));
        throw new RuntimeException('Impossibile verificare i tentativi di login.');
    }

    $row = pg_fetch_assoc($result);

    $tentativi = (int) $row['tentativi'];
    $trascorsi = (int) ($row['secondi_trascorsi'] ?? 0);
    //exit;
    $attesa = match (true) {
        $tentativi < 1  => 0,
        $tentativi < 2  => 5,
        $tentativi < 3  => 30,
        $tentativi < 4 => 1500,
        default         => 300
    };

    return max(0, $attesa - $trascorsi);
}


/**
 * Registra un tentativo di autenticazione.
 */
function registraTentativoLogin(
    $connessione,
    string $username,
    string $ip,
    bool $success
): void {
    $sql = "
        INSERT INTO totem.login_attempts
            (username, ip_address, success)
        VALUES ($1, $2::inet, $3)
    ";

    $result = pg_query_params(
        $connessione,
        $sql,
        [$username, $ip, $success ? 'true' : 'false']
    );

    if ($result === false) {
        error_log('Errore registrazione tentativo login: ' . pg_last_error($connessione));
        throw new RuntimeException('Impossibile registrare il tentativo di login.');
    }
}

/* ------------------------------------------------------------
 * FUNZIONE PER CAPTCHA
 * ------------------------------------------------------------
*/


function verificaTurnstile(
    string $token,
    string $secret,
    string $ip,
    string $hostnameAtteso
): bool {

    if ($token === '' || $secret === '' || $hostnameAtteso === '') {
        return false;
    }

    $ch = curl_init(
        'https://challenges.cloudflare.com/turnstile/v0/siteverify'
    );

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'secret'   => $secret,
            'response' => $token,
            'remoteip' => $ip
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_CONNECTTIMEOUT => 3
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $httpCode !== 200) {
        error_log('Errore comunicazione Cloudflare Turnstile: ' . curl_error($ch));
        //curl_close($ch);
        return false;
    }

    //curl_close($ch);

    $result = json_decode($response, true);

    if (!is_array($result) || ($result['success'] ?? false) !== true) {
        return false;
    }

    // Controllo hostname
    $hostnameRicevuto = strtolower($result['hostname'] ?? '');
    $hostnameAtteso = strtolower($hostnameAtteso);

    if ($hostnameRicevuto !== $hostnameAtteso) {
        error_log('Turnstile: hostname non corrispondente.');
        return false;
    }

    return true;
}




/*
 * ------------------------------------------------------------
 * 2. FUNZIONE PER VALIDARE LA DESTINAZIONE DEL REDIRECT
 * ------------------------------------------------------------
 *
 * Al termine del login vogliamo riportare l'utente alla pagina
 * dalla quale era partito.
 *
 * La destinazione può provenire dalla sessione oppure dal
 * cookie "origine".
 *
 * Non dobbiamo però consentire redirect verso siti esterni:
 * un valore manipolato potrebbe essere utilizzato per phishing.
 *
 * Accettiamo quindi esclusivamente percorsi relativi
 * all'applicazione.
 */

function getLoginRedirect(string $origine): string
{
    // Destinazione predefinita.
    $fallback = './index.php';

    $origine = trim($origine);

    if ($origine === '') {
        return $fallback;
    }

    // Analizzo la destinazione senza eseguirla.
    $parts = parse_url($origine);

    if ($parts === false) {
        return $fallback;
    }

    // Rifiuto URL assoluti e riferimenti a host esterni.
    if (
        isset($parts['scheme']) ||
        isset($parts['host']) ||
        isset($parts['user']) ||
        isset($parts['pass'])
    ) {
        return $fallback;
    }

    $path = $parts['path'] ?? '';

    // Elimino eventuali prefissi "./".
    while (str_starts_with($path, './')) {
        $path = substr($path, 2);
    }

    // Decodifico il percorso per intercettare anche traversal
    // eventualmente nascosti tramite codifica URL.
    $decodedPath = rawurldecode($path);

    // Rifiuto percorsi assoluti, backslash e directory traversal.
    if (
        $path === '' ||
        str_starts_with($path, '/') ||
        str_contains($path, '\\') ||
        preg_match('#(^|/)\.\.?(/|$)#', $decodedPath)
    ) {
        return $fallback;
    }

    // Accetto solamente caratteri ragionevoli per un percorso URL.
    if (!preg_match('#^[A-Za-z0-9._~/%-]+$#D', $path)) {
        return $fallback;
    }

    // Ricostruisco il redirect mantenendo gli eventuali parametri.
    $redirect = './' . $path;

    if (isset($parts['query'])) {

        // Evito caratteri di controllo nella parte query.
        if (preg_match('/[\x00-\x1F\x7F]/', $parts['query'])) {
            return $fallback;
        }

        $redirect .= '?' . $parts['query'];
    }

    return $redirect;
}


/*
 * ------------------------------------------------------------
 * 3. RECUPERO DELLA PAGINA DI PROVENIENZA
 * ------------------------------------------------------------
 *
 * Prima scelta: cookie "origine", se già presente.
 * Seconda scelta: variabile di sessione "origine".
 * Ultima possibilità: index.php.
 *
 * Il cookie viene conservato per sette giorni, come nel
 * comportamento precedente.
 *
 * Non cancelliamo più tutti i cookie del browser:
 * sarebbe un comportamento eccessivamente invasivo e potrebbe
 * interferire con altre applicazioni sullo stesso dominio.
 */

if (isset($_COOKIE['origine']) && $_COOKIE['origine'] !== '') {

   $origine = getLoginRedirect($_COOKIE['origine']);

} else {

    $origine = getLoginRedirect($_SESSION['origine'] ?? 'index.php');

    setcookie('origine', $origine, [
        'expires'  => time() + ($lifetime * 7),
        'path'     => session_get_cookie_params()['path'],
        'secure'   => true,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
}

/*
 * ------------------------------------------------------------
 * 4. INIZIALIZZAZIONE MESSAGGI
 * ------------------------------------------------------------
 */

$errorMessage = '';


/*
 * ------------------------------------------------------------
 * 5. AUTENTICAZIONE LDAP
 * ------------------------------------------------------------
 *
 * La connessione LDAP viene aperta solamente quando arriva
 * una richiesta POST contenente il pulsante ldapLogin.
 *
 * Non è necessario collegarsi a LDAP quando viene visualizzato
 * semplicemente il form di login.
 */

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ldapLogin'])) {


	// Verifica presenza e validità del token CSRF.
    $csrfToken = $_POST['csrf_token'] ?? '';

    if (
        !is_string($csrfToken) ||
        !isset($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrfToken)
    ) {
        http_response_code(403);
        exit('Richiesta non valida. Ricarica la pagina e riprova.');
    }

    // IP del client visto dal server NGINX.
    // Per ora potrebbe essere l'IP del reverse proxy.
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';



    $turnstileToken = $_POST['cf-turnstile-response'] ?? '';
    $turnstileHostname = $_ENV['TURNSTILE_HOSTNAME'] ?? '';


    if (
        !is_string($turnstileToken) ||
        !verificaTurnstile($turnstileToken, $turnstileSecretKey, $ip, $turnstileHostname)
    ) {
        $errorMessage = 'Verifica di sicurezza non riuscita. Riprova.';
    } else {
    


        /*
        * Recupero dei dati inseriti dall'utente.
        *
        * Sullo username utilizziamo trim(), perché eventuali spazi
        * accidentali prima o dopo il nome utente non sono utili.
        *
        * Sulla password NON utilizziamo trim():
        * deve essere verificata esattamente come inserita.
        */

        $ldapUser = $_POST['user'] ?? '';
        $ldapPassword = $_POST['password'] ?? '';

        if (!is_string($ldapUser) || !is_string($ldapPassword)) {
            http_response_code(400);
            exit('Richiesta non valida.');
        }

        $ldapUser = trim($ldapUser);

        if ($ldapUser === '') {
            $errorMessage = 'Inserisci il nome utente.';

        } elseif (strlen($ldapUser) > 50) {
            $errorMessage = 'Nome utente non valido.';

        } elseif ($ldapPassword === '') {
            $errorMessage = 'Inserisci la password.';

        } elseif (strlen($ldapPassword) > 255 || str_contains($ldapPassword, "\0")) {
            $errorMessage = 'Credenziali non valide.';

        } else {
            


            
            if (!$conn) {
                echo '<br>Non riesco a connettermi al db con le anagrafiche.';
            }
            //echo "conn= ".$conn ."<br>";
            //exit;
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                $errorMessage = 'Impossibile identificare la connessione.';
            } else {
                try {
                    
                    
                    
                    // Verifica eventuali attese prima di contattare LDAP.
                    $attesa = verificaTentativiLogin($conn_hub, $ldapUser, $ip);
                    //echo $attesa."<br>";

                    if ($attesa > 0) {
                        $errorMessage = "Troppi tentativi. Riprova tra {$attesa} secondi.";
                    } else {
                        $ldapConnection = ldap_connect($ldapHost);

                        if ($ldapConnection === false) {
                            $errorMessage = 'Servizio di autenticazione temporaneamente non disponibile.';
                        } else {
                            ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
                            ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

                            $ldapbind = @ldap_bind(
                                $ldapConnection,
                                'DSI\\' . $ldapUser,
                                $ldapPassword
                            );

                            ldap_close($ldapConnection);

                            // Registriamo sempre l'esito del bind.
                            registraTentativoLogin(
                                $conn_hub,
                                $ldapUser,
                                $ip,
                                (bool) $ldapbind
                            );

                            if ($ldapbind) {
                                if (!session_regenerate_id(true)) {
                                    throw new RuntimeException('Rigenerazione sessione fallita.');
                                }

                                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                                $_SESSION['username'] = $ldapUser;
                                $_SESSION['auth_time'] = time();
                                $_SESSION['last_activity'] = time();

                                $_SESSION['start'] = time();
                                $_SESSION['expire'] = $_SESSION['start'] + 8 * 60 * 60;

                                // Mantieni qui il tuo codice attuale
                                // per il cookie "un" e il redirect.

                                header('Location: ' . getLoginRedirect($origine), true, 303);
                                exit;
                            } else {
                                $errorMessage = 'Credenziali non valide. Controlla il nome utente e la password.';
                            }
                        }
                    }
                } catch (Throwable $e) {
                    error_log('Errore autenticazione: ' . $e->getMessage());
                    $errorMessage = 'Servizio di autenticazione temporaneamente non disponibile.';
                }
                
            }
        }

    }
}

/*
 * ------------------------------------------------------------
 * 7. CARICAMENTO COMPONENTI GRAFICI
 * ------------------------------------------------------------
 *
 * req.php viene caricato dopo la gestione del POST.
 *
 * Questo è importante perché eventuali output HTML prodotti
 * da questi componenti impedirebbero l'utilizzo di header()
 * per effettuare il redirect HTTP.
 */

require_once 'req.php';

the_page_title();

// Ambiente applicativo (produzione oppure test).
$app_env = $_ENV['APP_ENV'] ?? '';


// Stato ambiente di test, mantenuto per compatibilità
// con il comportamento precedente.
$isTest = (($_SESSION['test'] ?? 0) == 1);

?>

<!DOCTYPE html>
<html lang="it">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login</title>

    <!-- CSS specifico della pagina di login -->
    <link rel="stylesheet" href="./includes/login.css">
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

</head>

<body class="login-page">


    <!-- =====================================================
         INTESTAZIONE DEL BACKOFFICE
         ===================================================== -->

    <div class="navbar-header">

        <div id="intestazione" class="banner">

            <div id="banner-image">

                <h3>

                    <a class="navbar-brand link-light" href="./index.php">

                        <img
                            class="pull-left"
                            src="img/amiu_small_white.png"
                            alt="SIT"
                            width="85"
                        >

                        <span>
                            Backoffice consuntivazione

                            <?php if ($isTest): ?>
                                (ambiente di TEST)
                            <?php endif; ?>
                        </span>

                    </a>

                </h3>

            </div>

        </div>

    </div>


    <!-- =====================================================
         FORM DI AUTENTICAZIONE
         ===================================================== -->

    <main class="container login-container">


        <!-- Messaggio di errore, se presente -->
        <?php if ($errorMessage !== ''): ?>

            <div class="login-error" role="alert">

                <?= htmlspecialchars(
                    $errorMessage,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ) ?>

            </div>

        <?php endif; ?>


        <!-- Titolo del form -->
        <h3 class="login-title">

            Inserisci credenziali AMIU

            <?php if ($app_env === 'test'): ?>

                <span class="login-test-label">
                    <i class="bi bi-exclamation-diamond"></i>
                    Ambiente di test
                </span>

            <?php endif; ?>
            <a class="btn btn-info"
        href="https://manuale-totem.readthedocs.io/it/latest/index.html"
        target="_blank"
        rel="noopener noreferrer">
        <i class="fa-regular fa-circle-question"></i> Guida in linea
    </a>
        </h3>


        <!-- Form LDAP -->
        <form
            action=""
            method="post"
            class="login-form"
        >

			<input
				type="hidden"
				name="csrf_token"
				value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>"
			>

            <div class="row g-3 align-items-center">


                <!-- Nome utente -->
                <div class="form-group">

                    <label for="user" class="form-label">
                        Utente
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        name="user"
                        id="user"
                        maxlength="50"
                        autocomplete="username"
                        required
                    >

                    <small class="login-help">

                        Per utenti interni (AMIU) è il nome utente
                        di dominio senza @amiu.genova.it e senza DSI.

                        <br>

                        Per utenti esterni è il nome utente comunicato.

                    </small>

                </div>


                <!-- Password -->
                <div class="form-group">

                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            id="password"
                            maxlength="255"
                            autocomplete="current-password"
                            required
                        >

                        <!-- Pulsante per mostrare/nascondere la password -->
                        <button
                            type="button"
                            id="togglePassword"
                            class="password-toggle"
                            aria-label="Mostra password"
                            aria-pressed="false"
                        >
                            <i class="bi bi-eye-slash" aria-hidden="true"></i>
                        </button>

                    </div>

                    <small class="login-help">

                        Per utenti interni (AMIU) è la password
                        con cui accedi al PC.

                        <br>

                        Per utenti esterni è la password comunicata.

                    </small>

                </div>
                
                <hr>
                <!-- Turnastile Captche -->
                <div class="cf-turnstile"
                    data-sitekey="<?= htmlspecialchars($turnstileSiteKey, ENT_QUOTES, 'UTF-8') ?>"
                    data-theme="auto"
                    data-expired-callback="turnstileScaduto"
                    data-error-callback="turnstileErrore">
                </div>
                <hr>
                
                <!-- Pulsante di accesso -->
                <div class="form-group login-submit">

                    <button
                        type="submit"
                        class="btn btn-primary"
                        name="ldapLogin"
                        value="1"
                    >
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login
                    </button>

                </div>

            </div>

        </form>


    

    </main>


    <!-- Componenti grafici comuni del SIT -->
    <?php
    require_once 'req_bottom.php';
    require_once './footer.php';
    ?>


    <!-- JavaScript specifico della pagina -->
    <script src="js/login.js" defer></script>

</body>
</html>
