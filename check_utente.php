<?php

function redirect($url)
{
    header("Location: $url");
    exit;
}

session_start();

// =========================
// CONFIG
// =========================
$lifetime = 86400; // cookie 1 giorno
$timeout  = 8 * 60 * 60; // 8 ore sessione logica

session_set_cookie_params($lifetime);

// =========================
// KEEP ALIVE LOGICO
// =========================
if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity']) > $timeout) {

    // sessione scaduta
    $_SESSION = [];
    session_destroy();

    redirect('./login.php?expired=1');
}

$_SESSION['last_activity'] = time();


// =========================
// JWT / COOKIE LOGIN FLOW
// =========================
$userId = null;

// JWT da GET
if (isset($_GET['jwt'])) {

    // pulizia cookie (come nel tuo codice)
    if (isset($_SERVER['HTTP_COOKIE'])) {
        $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            setcookie($name, '', time() - 1000, '/');
        }
    }

    $token0 = $_GET['jwt'];

    if ($token0) {
        setcookie("tokenCookie", $token0, time() + ($lifetime * 7), "/");
        $token = $token0;
    } else {
        $token = $_COOKIE['tokenCookie'] ?? null;
    }

    if (!isset($_SESSION['username']) && $token) {

        $decoded1 = json_decode(
            base64_decode(str_replace('_', '/', str_replace('-', '+', explode('.', $token)[1])))
        );

        foreach ($decoded1 as $key => $value) {

            if ($key == 'userId') {
                $userId = (int)$value;
            }

            if ($key == 'name') {
                $_SESSION['username'] = $value;

                setcookie("un", $value, time() + (86400 * 7), "/");

                $_SESSION['start'] = time();
                $_SESSION['expire'] = $_SESSION['start'] + $timeout;
            }

            if ($key == 'exp') {
                if (time() > (int)$value && basename($_SERVER['PHP_SELF']) != 'login.php') {
                    redirect('./login.php?expired=1');
                }
            }
        }
    }
}

// =========================
// SESSIONE GIÀ ATTIVA
// =========================
else if (isset($_SESSION['username'])) {

    setcookie("un", $_SESSION['username'], time() + (86400 * 7), "/");

    if (!isset($_SESSION['expire'])) {
        $_SESSION['expire'] = time() + $timeout;
    }

    if (time() > $_SESSION['expire'] && basename($_SERVER['PHP_SELF']) != 'login.php') {

        $_SESSION = [];
        session_destroy();

        redirect('./login.php?expired=1');
    }
}

// =========================
// COOKIE FALLBACK
// =========================
else if (isset($_COOKIE['un'])) {

    $_SESSION['username'] = $_COOKIE['un'];

    $_SESSION['start'] = time();
    $_SESSION['expire'] = $_SESSION['start'] + $timeout;

    setcookie("un", $_SESSION['username'], time() + (86400 * 7), "/");

    if (time() > $_SESSION['expire'] && basename($_SERVER['PHP_SELF']) != 'login.php') {

        session_destroy();
        redirect('./login.php?expired=1');
    }
}

// =========================
// ULTIMO CONTROLLO BLOCCANTE
// =========================
if (!isset($_SESSION['username']) &&
    basename($_SERVER['PHP_SELF']) != 'login.php') {

    redirect('./login.php');
}
?>