<?php

// Carica configurazione e avvia la sessione.
// Questo file deve essere incluso prima di qualsiasi utilizzo di $_SESSION.

require_once __DIR__ . '/session_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}