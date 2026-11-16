<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// =========================
// CONFIGURAZIONE SESSIONE
// =========================

require_once __DIR__ . '/includes/session_check.php';
require_once __DIR__ . '/vendor/autoload.php';

// Timeout
$timeoutInattivita = 3 * 60 * 60;
$timeoutAssoluto   = 8 * 60 * 60;

$now = time();

// Chiave segreta JWT da .env
$jwtSecret = $_ENV['JWT_SECRET'] ?? '';

// Issuer atteso: da configurare con il fornitore
//$issuerAtteso = $_ENV['JWT_ISSUER'] ?? '';


// =========================
// FUNZIONI
// =========================

function redirect($url)
{
    header("Location: $url");
    exit;
}

function terminaSessione()
{
    $_SESSION = [];

    if (session_status() === PHP_SESSION_ACTIVE) {

        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires'  => time() - 3600,
            'path'     => $params['path'],
            'domain'   => $params['domain'],
            'secure'   => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite']
        ]);

        session_destroy();
    }

    redirect('./login.php?expired=1');
}

function pulisciUrlJwt()
{
    $url = parse_url($_SERVER['REQUEST_URI']);

    $query = [];
    parse_str($url['query'] ?? '', $query);

    unset($query['jwt']);

    $nuovoUrl = $url['path'] ?? '/';

    if (!empty($query)) {
        $nuovoUrl .= '?' . http_build_query($query);
    }

    header('Location: ' . $nuovoUrl, true, 303);
    exit;
}


// =========================
// 1. SESSIONE NON AUTENTICATA
// =========================

if (empty($_SESSION['username'])) {

 

    // Controlliamo se è arrivato un JWT
    $token = $_GET['jwt'] ?? '';

    if ($token === '') {
        redirect('./login.php');
    }

    
    // Configurazione obbligatoria
    if ($jwtSecret === '') {
        error_log('Configurazione JWT mancante.');
        http_response_code(500);
        exit('Errore di configurazione autenticazione.');
    }
    
    try {
        //echo '1<br>'.$jwtSecret."<br>";
        //exit;
        
        // Ricostruzione della chiave esattamente come in C#
        $keyBytes = $jwtSecret;

        // C# usa Encoding.UTF8.GetBytes(key)
        // e Array.Resize(ref symmetricKeyBytes, 64)
        if (strlen($keyBytes) < 64) {
            $keyBytes = str_pad($keyBytes, 64, "\0");
        }

        // Verifica firma HS256 e scadenza
        $decoded = JWT::decode(
            $token,
            new Key($keyBytes, 'HS256')
        );


        //echo '2<br>';
        //exit;
        // Verifica issuer
        /*if (
            !isset($decoded->iss) ||
            !hash_equals($issuerAtteso, (string)$decoded->iss)
        ) {
            throw new UnexpectedValueException('Issuer non valido.');
        }*/

    
        // Verifica dati utente
        // =========================
        // VERIFICA CLAIM JWT
        // =========================

        // 1. Verifica presenza e validità della scadenza
        if (
            !isset($decoded->exp) ||
            !is_numeric($decoded->exp) ||
            (int)$decoded->exp <= time()
        ) {
            throw new UnexpectedValueException('Token scaduto o privo di exp.');
        }

        // 2. Verifica nome utente
        if (
            !isset($decoded->name) ||
            !is_string($decoded->name) ||
            trim($decoded->name) === ''
        ) {
            throw new UnexpectedValueException('Nome utente non valido.');
        }

        // 3. Verifica userId
        if (
            !isset($decoded->userId) ||
            !is_numeric($decoded->userId) ||
            (int)$decoded->userId <= 0
        ) {
            throw new UnexpectedValueException('userId non valido.');
        }

        // Creazione della nuova sessione autenticata
        if (!session_regenerate_id(true)) {
            throw new RuntimeException('Rigenerazione sessione fallita.');
        }

        $_SESSION['username'] = (string)$decoded->name;
        $_SESSION['userId'] = (int)$decoded->userId;

        $_SESSION['auth_time'] = time();
        $_SESSION['last_activity'] = time();

        // Cookie di compatibilità per il codice JavaScript esistente.
        // NON deve essere utilizzato per autenticare l'utente.
        setcookie('un', $_SESSION['username'], [
            'expires'  => time() + (8 * 60 * 60),
            'path'     => $sessionPath ?? '/',
            'secure'   => true,
            'httponly' => false,
            'samesite' => 'Lax'
        ]);

        // Eliminiamo il JWT dalla barra degli indirizzi
        pulisciUrlJwt();

    } catch (Throwable $e) {

        // Dettagli tecnici solo nei log del server
        error_log('Autenticazione JWT fallita: ' . $e->getMessage());
        redirect('./login.php?expired=1');
    }
}


// =========================
// 2. SESSIONE GIÀ AUTENTICATA
// =========================

// Controllo inattività
if (
    !isset($_SESSION['last_activity']) ||
    ($now - (int)$_SESSION['last_activity']) > $timeoutInattivita
) {
    terminaSessione();
}

// Controllo durata assoluta
if (
    !isset($_SESSION['auth_time']) ||
    ($now - (int)$_SESSION['auth_time']) > $timeoutAssoluto
) {
    terminaSessione();
}

// Aggiornamento ultima attività
$_SESSION['last_activity'] = $now;

?>
