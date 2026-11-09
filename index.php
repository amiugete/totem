<?php
//session_set_cookie_params($lifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

setcookie("username", 'Amiu1', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);


setcookie("password", '6409', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);

setcookie("cisiamo", 'OK', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Backoffice totem</title>
<?php 
require_once('./req.php');

the_page_title();

//echo $test;

require_once 'carica_env.php';
require_once 'conn_ok.php';





?> 





</head>


<body class="pagina-menu">

<?php
require_once('./navbar_up.php');
$name = dirname(__FILE__);
?>

<style>

    html,
    body {
        height: 100%;
        margin: 0;
    }

    .pagina-menu {
        display: flex;
        flex-direction: column;
        min-height: 100vh;
    }


    /* Area centrale */

    .menu-mattonelle {
        flex: 1;

        display: flex;
        align-items: center;
        justify-content: center;

        padding: 40px;
    }


    .mattonelle-container {

        display: flex;
        flex-wrap: wrap;

        justify-content: center;
        align-items: center;

        gap: 30px;

        max-width: 1000px;
    }


    /* MATTONELLA */

    .mattonella {

        width: 300px;
        height: 230px;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        text-decoration: none;

        background: #ffffff;

        border-radius: 20px;

        box-shadow:
            0 4px 15px rgba(0, 0, 0, 0.12);

        color: #212529;

        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease;
    }


    .mattonella:hover {

        color: #212529;
        text-decoration: none;

        transform: translateY(-6px);

        box-shadow:
            0 12px 30px rgba(0, 0, 0, 0.18);
    }


    .mattonella:active {
        transform: translateY(-2px);
    }


    /* ICONA */

    .mattonella i {

        font-size: 4.5rem;

        margin-bottom: 22px;
    }


    /* TESTO */

    .mattonella-titolo {

        font-size: 1.3rem;

        font-weight: 600;

        text-align: center;

        max-width: 260px;
    }


    /* Colori */

    .mattonella-raccolta i {
        color: #0d6efd;
    }

    .mattonella-spazzamento i {
        color: #198754;
    }


</style>


<div class="menu-mattonelle">

    <div class="mattonelle-container">


        <a class="mattonella mattonella-raccolta"
           href="./backoffice_racc_ext.php">

            <i class="fa-solid fa-dumpster"></i>

            <div class="mattonella-titolo">
                Consuntivazione raccolta
            </div>

        </a>


        <a class="mattonella mattonella-spazzamento"
           href="./backoffice_spazz_ext.php">

            <i class="fa-solid fa-broom"></i>

            <div class="mattonella-titolo">
                Consuntivazione spazzamento/lavaggio
            </div>

        </a>


        <!--
        ESEMPIO TERZA MATTONELLA

        <a class="mattonella"
           href="./altra_pagina.php">

            <i class="fa-solid fa-car"></i>

            <div class="mattonella-titolo">
                Altra funzione
            </div>

        </a>
        -->


        <!--
        ESEMPIO QUARTA MATTONELLA

        <a class="mattonella"
           href="./altra_pagina.php">

            <i class="fa-solid fa-gear"></i>

            <div class="mattonella-titolo">
                Altra funzione
            </div>

        </a>
        -->

    </div>

</div>


<?php
require_once('req_bottom.php');
require('./footer.php');
?>

</body>




</html>