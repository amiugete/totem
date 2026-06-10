<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../carica_env.php';
require_once '../conn_ok.php';

$data = $_GET['data_percorsi'] ?? null;
$last_uo = $_GET['last_uo'] ?? null;


if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_ut = "SELECT DISTINCT id_uo, desc_uo
             FROM spazzamento.cons_percorsi_spazz_x_app
             WHERE to_date($1, 'DD/MM/YYYY') BETWEEN data_inizio AND data_fine
             ORDER BY desc_uo";

$result = pg_prepare($conn_hub, "combo_query", $query_ut);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
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
