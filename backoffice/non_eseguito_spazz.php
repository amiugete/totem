<?php
//session_set_cookie_params($lifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

$causale=$_POST['causale'];
//echo $causale.'<br>';




# consuntivazione singole tappe
$query_insert1 = "INSERT INTO spazzamento.effettuati_amiu (
                id,
                tappa, id_causale, 
                datalav, codice, punteggio)  
                ( select 
                    (select max(id) from spazzamento.effettuati_amiu) + row_number() over (order by cpsxa.id_tappa_raggr) as id,
                    cpsxa.id_tappa_raggr as tappa, 
                    $1 as causale,
                    to_date($2, 'DD/MM/YYYY') as datalav,
                    $3 as codice,
                    0 as punteggio 
                    from spazzamento.cons_percorsi_spazz_x_app cpsxa 
                    where cpsxa.id_percorso = $4 and 
                    to_date($5, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine 
                    and totem.verify_daily_frequency(
                    cpsxa.cod_frequenza_tratto, 
                    to_date($6, 'DD/MM/YYYY'), 
                    cpsxa.freq_settimane 
                    ) = 1
                ) ";


$result1 = pg_prepare($conn_hub, "query_insert1", $query_insert1);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

   
$result1 = pg_execute($conn_hub, "query_insert1", array(
    $causale,
    $datalav,
    $consuntivatore,
    $id_percorso,
    $datalav,
    $datalav
));


if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}
//exit();


$query_insert2 = "INSERT INTO spazzamento.percorsi_non_effettuati_x_ekovision (
    id_percorso, datalav,
     causale_sit, codice,
     username, ws_ok) VALUES
     ($1, to_date($2,'DD/MM/YYYY'),
      $3, $4,
      $5, false)";
    
$result2 = pg_prepare($conn_hub, "query_insert2", $query_insert2);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

   
$result2 = pg_execute($conn_hub, "query_insert2", array(
    $id_percorso, $datalav,
    $causale, $consuntivatore,
    $_SESSION['username']
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




