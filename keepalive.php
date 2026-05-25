<?php
session_start();

if (!isset($_SESSION['username'])) {
    http_response_code(401);
    exit;
}

$_SESSION['last_activity'] = time();

echo "OK";