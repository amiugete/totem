<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../carica_env.php';
require_once '../conn_ok.php';

$sportello = $_POST['sportello'] ?? null;

//echo $sportello;

$query_mezzo = "SELECT mi.descrizione_tipologia_mezzo,
mi.descrizione, 
mi.targa, 
mi.stato_mezzo, 
mi.stato_mezzo_info 
FROM mezzi_infopm mi WHERE mi.sportello LIKE lpad($1::text, 5, '0')";

$result = pg_prepare($conn_hub, "query_mezzo", $query_mezzo);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_mezzo", array($sportello));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

$check = pg_num_rows($result);
if ($check==0){
    echo "<div class='alert alert-danger' role='alert'>
    Mezzo non trovato per lo sportello selezionato.
  </div>";
    http_response_code(201);
    exit;
} //else {
  while ($row = pg_fetch_assoc($result)) {
    echo 'Targa '.$row['targa'].'';
    echo ' - '.$row['descrizione_tipologia_mezzo'].'';
    echo ' - '.$row['descrizione'].'';
    
    if($row['stato_mezzo']=='I'){
      echo '<span style="color:red;"><i class="fa-solid fa-circle-minus"></i> Mezzo non disponibile </span><br>';
    } 
  //}
}

//pg_free_result($result1); 
