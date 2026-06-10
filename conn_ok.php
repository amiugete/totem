<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_ENV['APP_ENV'] === 'test') {
    define('BASE_URL', '/totem_test');
} else {
    define('BASE_URL', '/totem');
}
$timeout = 6*60*60; //secondi 

if (isset($_SESSION['last_activity'])) {
    if (time() - $_SESSION['last_activity'] > $timeout) {
        session_unset();
        session_destroy();
        http_response_code(401); // Unauthorized
        echo 'Sessione scaduta. <a href="#" onclick="location.reload()">Ricarica la pagina</a>.';
        exit;
    }
}

$_SESSION['last_activity'] = time();


// legge dal file ENV
$host_an = $_ENV['DB_AN_HOST'] ?? null;
$port_an = $_ENV['DB_AN_PORT'] ?? 5432;
$db_an   = $_ENV['DB_AN_NAME'] ?? null;
$user_an = $_ENV['DB_AN_USER'] ?? null;
$pass_an = $_ENV['DB_AN_PASS'] ?? null;

if (!$host_an || !$db_an || !$user_an) {
    die("Database configuration missing in .env");
}

$conn_string = "host={$host_an} port={$port_an} dbname={$db_an} user={$user_an} password={$pass_an}";

$conn = pg_connect($conn_string);

if (!$conn) {
    die('<br>Non riesco a connettermi al db con le anagrafiche.');
}


$host_T = $_ENV['DB_T_HOST'] ?? null;
$port_T = $_ENV['DB_T_PORT'] ?? 5432;
$db_T   = $_ENV['DB_T_NAME'] ?? null;
$user_T = $_ENV['DB_T_USER'] ?? null;
$pass_T = $_ENV['DB_T_PASS'] ?? null;

if (!$host_T || !$db_T || !$user_T) {
    die("Database configuration missing in .env");
}

$conn_hub_string = "host={$host_T} port={$port_T} dbname={$db_T} user={$user_T} password={$pass_T}";

$conn_hub = pg_connect($conn_hub_string);

if (!$conn_hub) {
    die('<br>Non riesco a connettermi al db di consuntivazione.');
}


# LDAP
$ldapDomain = $_ENV['LDAP_DOMAIN'] ?? null;
//echo "LDAP Domain: " . $ldapDomain . "<br>";
#exit(); 
$ldapHost = $_ENV['LDAP_HOST'] ?? null;
$ldapPort = $_ENV['LDAP_PORT'] ?? null;