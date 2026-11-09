<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../carica_env.php';
require_once '../conn_ok.php';

$sportello = $_POST['sportello'] ?? null;

//echo $sportello;

$query_mezzo = "SELECT distinct mi.descrizione_tipologia_mezzo,
trim(mi.descrizione) as descrizione, 
trim(mi.targa) as targa, 
mi.stato_mezzo, 
mi.stato_mezzo_info, 
mi.id_sede_presa_serv, 
es.des_sede_old
FROM totem.mezzi_infopm mi 
left join (select distinct es.cod_sede_operativa_rif, es.cod_sede_old, es.des_sede_old  
from totem.elenco_sedi es 
where es.cod_sede_old is not null) es on es.cod_sede_old = mi.id_sede_presa_serv 
WHERE mi.sportello LIKE lpad($1::text, 5, '0')";

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
    echo '<div class="alert alert-danger" role="alert" style="margin-top: 1%;">
    Mezzo non trovato per lo sportello selezionato.
  </div>';
  http_response_code(404);    
  exit;
} //else {
  while ($row = pg_fetch_assoc($result)) {
    echo '<div class="alert alert-success" role="alert" style="margin-top: 1%;">Targa '.$row['targa'].'';
    echo ' - '.$row['descrizione_tipologia_mezzo'].'';
    echo ' - '.$row['descrizione'].'';
    echo ' <i class="fa-solid fa-house-flag"></i> '.$row['des_sede_old'].'';
    if($row['stato_mezzo']=='I'){
      echo '<span style="color:red;"><i class="fa-solid fa-circle-minus"></i> Mezzo non disponibile </span><br>';
    } 
    echo '</div>';
  //}
}

//pg_free_result($result1); 
