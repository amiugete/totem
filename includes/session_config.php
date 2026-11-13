
<?php

// Caricamento variabili d'ambiente
require_once __DIR__ . '/../carica_env.php';

// Durata massima del cookie e della sessione PHP
$lifetime = 8 * 60 * 60; // 8 ore

// Individuazione ambiente
$appEnv = $_ENV['APP_ENV'] ?? '';

if ($appEnv === 'test') {
    $sessionPath = $_ENV['APP_PATH_test'] ?? '';
} elseif ($appEnv === 'prod') {
    $sessionPath = $_ENV['APP_PATH'] ?? '';
} else {
    throw new RuntimeException('APP_ENV non valido.');
}

// Controllo configurazione percorso
if (
    $sessionPath === '' ||
    $sessionPath[0] !== '/' ||
    str_contains($sessionPath, ';')
) {
    throw new RuntimeException('APP_PATH non valido.');
}

// Nome sessione distinto tra produzione e test
session_name('totem_' . $appEnv);

// Configurazione PHP
ini_set('session.gc_maxlifetime', (string) $lifetime);
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');

// Configurazione cookie di sessione
session_set_cookie_params([
    'lifetime' => $lifetime,
    'path' => $sessionPath,
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
