<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../carica_env.php';
require_once '../conn_ok.php';



$cod_percorso   = $_POST['cod_percorso']   ?? '';
$datalav        = $_POST['datalav']        ?? '';
$consuntivatore  = $_POST['consuntivatore'] ?? '';
$mansione       = $_POST['mansione']       ?? '';
$guida          = $_POST['guida']          ?? '';
$sportello      = $_POST['sportello']      ?? null;

if (!$cod_percorso || !$datalav || !$consuntivatore || !$mansione || !$guida) {
    http_response_code(400);
    echo "Dati mancanti";
    exit;
}


// controllo sullo sportello da inserire 


// upsert
$upsert_query = "INSERT INTO servizi.registrazioni (
    codice, id_percorso, datalav, 
    id_qualifica, autista, sportello
    ) 
    VALUES 
    (
    $1, $2, to_date($3, 'DD/MM/YYYY'), 
    $4, $5, $6
    ) ON CONFLICT (codice, id_percorso, datalav) /* or you may use [DO NOTHING;] */ 
    DO UPDATE  SET  
    id_qualifica=EXCLUDED.id_qualifica, 
    autista=EXCLUDED.autista, 
    sportello=EXCLUDED.sportello, 
    send_ekovision=EXCLUDED.send_ekovision, 
    errori=EXCLUDED.errori;";



$result1 = pg_prepare($conn_hub, "upsert_query", $upsert_query);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

$result1 = pg_execute($conn_hub, "upsert_query", array(
    $consuntivatore,
    $cod_percorso,
    $datalav,
    $mansione,
    $guida,
    $sportello
    ));
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

if (!isset($res_ok) || $res_ok==0) {
    echo "OK";
} else {
    echo '<i class="bi bi-exclamation-triangle-fill"></i> Errore durante la registrazione del servizio.';
}
?>