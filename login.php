
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

require_once __DIR__ . '/includes/session_config.php';

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
require_once 'conn_ok.php';


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

    $origine = $_COOKIE['origine'];

} else {

    $origine = $_SESSION['origine'] ?? 'index.php';

    setcookie('origine', $origine, [
        'expires'  => time() + ($lifetime * 7),
        'path'     => '/',
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


    /*
     * Recupero dei dati inseriti dall'utente.
     *
     * Sullo username utilizziamo trim(), perché eventuali spazi
     * accidentali prima o dopo il nome utente non sono utili.
     *
     * Sulla password NON utilizziamo trim():
     * deve essere verificata esattamente come inserita.
     */

    $ldapUser = trim($_POST['user'] ?? '');
    $ldapPassword = $_POST['password'] ?? '';

    if ($ldapUser === '') {

        $errorMessage = 'Inserisci il nome utente.';

    } elseif ($ldapPassword === '') {

        $errorMessage = 'Inserisci la password.';

    } else {

        /*
         * Apertura connessione al server LDAP.
         */

        $ldapConnection = ldap_connect($ldapHost);

        if ($ldapConnection === false) {

            // Non mostriamo dettagli tecnici del server all'utente.
            $errorMessage = 'Servizio di autenticazione temporaneamente non disponibile.';

        } else {

            /*
             * Configurazione LDAP versione 3.
             *
             * LDAP_OPT_REFERRALS viene disabilitato per evitare
             * problemi con i referral tipici di Active Directory.
             */

            ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ldapConnection, LDAP_OPT_REFERRALS, 0);

            /*
             * Verifica delle credenziali.
             *
             * Il dominio utilizzato attualmente è DSI.
             * Manteniamo invariato il comportamento del vecchio login.
             */

            $ldapbind = @ldap_bind(
                $ldapConnection,
                'DSI\\' . $ldapUser,
                $ldapPassword
            );

            // La connessione non serve più dopo il bind.
            ldap_close($ldapConnection);


            /*
             * ------------------------------------------------
             * 6. LOGIN RIUSCITO
             * ------------------------------------------------
             */

            if ($ldapbind) {

                /*
                 * Rigenerazione dell'ID di sessione.
                 *
                 * È una protezione contro il session fixation:
                 * dopo un'autenticazione riuscita viene generato
                 * un nuovo identificativo di sessione.
                 */

                if (!session_regenerate_id(true)) {
                    throw new RuntimeException(
                        'Impossibile rigenerare la sessione.'
                    );
                }
                

				// Genera un nuovo token per la sessione autenticata.
				$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                /*
                 * Memorizzazione dell'identità dell'utente.
                 *
                 * Manteniamo il nome della variabile username:
                 * le altre pagine del SIT possono continuare
                 * a utilizzarla senza modifiche.
                 */

                $_SESSION['username'] = $ldapUser;

				$_SESSION['auth_time'] = time();
				$_SESSION['last_activity'] = time();


				/* DA capire se utilizzate */
                $_SESSION['start'] = time();
                // Durata applicativa della sessione: 8 ore.
                $_SESSION['expire'] = $_SESSION['start'] + (8 * 60 * 60);

				


                /*
                 * Cookie "un".
                 *
                 * Lo manteniamo per compatibilità con il codice
                 * esistente, visto che potrebbe essere letto
                 * anche da JavaScript.
                 *
                 * Per questo motivo NON modifichiamo qui
                 * httponly: rimane false come nel comportamento
                 * precedente.
                 */

                setcookie('un', $ldapUser, [
                    'expires'  => time() + (86400 * 7),
                    'path'     => '/',
                    'secure'   => true,
                    'httponly' => false,
                    'samesite' => 'Lax'
                ]);


                /*
                 * Redirect HTTP verso la pagina di provenienza.
                 *
                 * Utilizziamo il codice 303 (See Other), che
                 * porta il browser a richiedere la destinazione
                 * con GET dopo il POST del login.
                 *
                 * Il redirect viene effettuato prima di produrre
                 * qualsiasi contenuto HTML.
                 */

                $redirectUrl = getLoginRedirect($origine);

                header('Location: ' . $redirectUrl, true, 303);
                exit;


            } else {

                /*
                 * Login non riuscito.
                 *
                 * Non comunichiamo al browser dettagli LDAP
                 * o informazioni tecniche sull'infrastruttura.
                 */

                $errorMessage = 'Credenziali non valide. Controlla il nome utente e la password.';

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
