<?php
session_start();


require_once '../carica_env.php';
require_once '../conn_ok.php';

$data = $_GET['data_percorsi'] ?? null;
$last_uo = $_GET['last_uo'] ?? null;


if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_ut = "with selezione_ut as (
SELECT spe.id_uo_sit, spe.desc_ut 
 FROM servizi.servizi_per_ekovision spe
 WHERE to_date($1, 'DD/MM/YYYY') BETWEEN spe.data_inizio_validita  AND spe.data_fine_validita 
 and id_zona in (1,2,3,5, 6)
 union 
SELECT spe.id_rimessa_sit as id_uo_sit, spe.desc_rimessa as desc_ut 
 FROM servizi.servizi_per_ekovision spe
 WHERE to_date($1, 'DD/MM/YYYY') BETWEEN spe.data_inizio_validita  AND spe.data_fine_validita 
 and id_zona in (1,2,3,5, 6) 
 and spe.id_rimessa_sit is not null
) 
select distinct mu.id_uo, desc_ut as desc_uo
from selezione_ut su
left join servizi.mail_ut mu on mu.id_ut_sit = su.id_uo_sit  
ORDER BY 2";

$result = pg_prepare($conn_hub, "combo_query", $query_ut);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
  die();
}
$result = pg_execute($conn_hub, "combo_query", array($data));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

echo "<option value='0'>Seleziona una UT</option>";
while ($row = pg_fetch_assoc($result)) {
    if ($row['id_uo']==$last_uo){
      echo "<option selected value='{$row['id_uo']}'>{$row['desc_uo']}</option>";
    } else {
      echo "<option value='{$row['id_uo']}'>{$row['desc_uo']}</option>";
    }
}

//pg_free_result($result1); 
