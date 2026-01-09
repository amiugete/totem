<?php
//session_set_cookie_params($lifetime);
session_start();


if ($_SESSION['test']==1) {
    require_once('../conn_test.php');
} else {
    require_once('../conn.php');
}

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
        (trim(explode('-', $tappe_consuntivate[$i])[1])=='100' and trim(explode('-', $tappe_consuntivate[$i])[2])=='0') )   {
        echo '<div class="alert alert-danger" role="alert"> ERRORE: causale e num_contenitori  di qualche tappa non sono congruenti.</div>';
        die;
    }
    $i++;
}



$datalav=$_POST['datalav'];
//echo $datalav.'<br>';


$consuntivatore=$_POST['consuntivatore'];
//echo $datalav.'<br>';

#exit();

$query_upsert = "INSERT INTO raccolta.effettuati_amiu (
                /*id,*/ tappa, id_causale, datainsert,
                datalav, codice, fatto) 
                VALUES 
                (/*(select max(id) from raccolta.effettuati_amiu) + 1),*/
                $1, $2, now(),
                to_date($3, 'DD/MM/YYYY'), $4, $5 ) 
                ON CONFLICT (tappa, datalav, codice) 
                DO UPDATE SET 
                id=EXCLUDED.id,
                id_causale=EXCLUDED.id_causale, 
                datainsert=now(),  
                fatto=EXCLUDED.fatto;";


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




