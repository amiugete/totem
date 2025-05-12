<?php
//session_set_cookie_params($lifetime);
session_start();


require_once ('../conn.php');

$cons_tappe=$_POST['cons_tappe'];
//echo $cons_tappe.'<br>';

$tappe_consuntivate=explode(',',$cons_tappe);

//print_r($tappe_consuntivate);

$i=0;
while ($i < (count($tappe_consuntivate)-1)) {
    //echo $i."<br>";
    //echo $tappe_consuntivate[$i]."<br>";
    //echo trim(explode('-', $tappe_consuntivate[$i])[0]);

    if (trim(explode('-', $tappe_consuntivate[$i])[1])==''){
        echo '<h5><font color="red">ERRORE: manca la causale su alcune tappe. 
        <br>Inserirla e provare a salvare nuovamente</font></h5>';
        die;
    }
    if (
        (trim(explode('-', $tappe_consuntivate[$i])[1])=='100' and trim(explode('-', $tappe_consuntivate[$i])[2])!='100') 
        OR
        (trim(explode('-', $tappe_consuntivate[$i])[1])!='100' and trim(explode('-', $tappe_consuntivate[$i])[2])=='100')
        )   {
        echo '<h5><font color="red">ERRORE: causale e punteggio della tappa '.trim(explode('-', $tappe_consuntivate[$i])[0]).
        ' non sono congruenti. </font>
        <br> Per visualizzare il numero delle tappe puoi cliccare sul tasto più a destra qua sotto.</h5>';
        die;
    }
    $i++;
}



$datalav=$_POST['datalav'];
//echo $datalav.'<br>';


$consuntivatore=$_POST['consuntivatore'];
//echo $datalav.'<br>';


$query_upsert = "INSERT INTO spazzamento.effettuati_amiu (
                tappa, id_causale, 
                datalav, codice, punteggio) 
                VALUES 
                ($1, $2, 
                $3, $4, $5 ) ON CONFLICT (tappa, datalav) 
                DO UPDATE  SET 
                id_causale=$2, 
                datainsert=now(), 
                codice=$4, 
                punteggio=$5;";


$result1 = pg_prepare($conn_hub, "query_upsert", $query_upsert);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}








$i=0;
while ($i < (count($tappe_consuntivate)-1)) {
    
    $result1 = pg_execute($conn_hub, "query_upsert", array(
        trim(explode('-', $tappe_consuntivate[$i])[0]),
        trim(explode('-', $tappe_consuntivate[$i])[1]),
        $datalav,
        $consuntivatore,
        trim(explode('-', $tappe_consuntivate[$i])[2])
    ));
    if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

    $i++;
}


if ($res_ok==0){
    echo '<div class="alert alert-success" role="alert"> Dati salvati correttamente!</div>';
} else {
    echo '<div class="alert alert-danger" role="alert">  ERRORE - contatta assterritorio@amiu.genova.it</font>';
}


?>




