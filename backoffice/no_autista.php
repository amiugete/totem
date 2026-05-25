<?php
//session_set_cookie_params($lifetime);
session_start();


require_once '../carica_env.php';
require_once '../conn_ok.php';


$id_percorso=$_POST['id'];
//echo $id_percorso.'<br>';
//exit;

$datalav=$_POST['datalav'];
//echo $datalav.'<br>';

//exit;

$consuntivatore=$_POST['consuntivatore'];
//echo $consuntivatore.'<br>';




$query_insert2 = "INSERT INTO raccolta.percorsi_no_autista_x_ekovision (
    id_percorso, datalav,
    codice, username) 
    VALUES
    ($1, to_date($2,'DD/MM/YYYY'),
    $3, $4)";
    
$result2 = pg_prepare($conn_hub, "query_insert2", $query_insert2);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

   
$result2 = pg_execute($conn_hub, "query_insert2", array(
    $id_percorso, $datalav,
    $consuntivatore, $_SESSION['username']
));

if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}





if ($res_ok==0){
    echo '<div class="alert alert-success" role="alert"> Dati salvati correttamente!</div>';
} else {
    echo '<div class="alert alert-danger" role="alert">  ERRORE - contatta assterritorio@amiu.genova.it</font>';
}



// ATTENZIONE POI OGNI 2' GIRA SCRIPT PYTHON  script_sit_amiu/EKOVISION/consuntivazioni_totem_eko.py che esegue le schede 
// VALUTARE SE MIGLIORARE 


?>




