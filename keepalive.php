<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 $timeout  = 6 * 60 * 60; // 6 h * 60 min * 60 sec

if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
}

if (time() - $_SESSION['last_activity'] > $timeout) {
    session_unset();
    session_destroy();

    http_response_code(401);
    echo json_encode([
        "status" => "expired",
        "message" => "Sessione scaduta"
    ]);
    exit;
}

// aggiorna attività
$_SESSION['last_activity'] = time();

echo json_encode([
    "status" => "ok",
    "time" => time()
]);