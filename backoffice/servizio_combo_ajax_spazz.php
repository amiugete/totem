<?php
session_start();
require_once '../carica_env.php';
require_once '../conn_ok.php';

$ut = intval($_GET['ut']) ?? null;
$data = $_GET['data_percorsi'] ?? null;

if (!$ut) {
    echo "<option value='0'>Ut non valida</option>";
    exit;
}


if (!$ut) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_servizio = "SELECT DISTINCT id_servizio, desc_servizio
              FROM spazzamento.cons_percorsi_spazz_x_app cpsxa
              where cpsxa.id_uo=$1::int
              and to_date($2, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine               
              order by 2";

$result = pg_prepare($conn_hub, "query_servizio", $query_servizio);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_servizio", array($ut, $data));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

echo "<option value='0'>Seleziona un servizio</option>";
while ($row = pg_fetch_assoc($result)) {
    echo "<option value='{$row['id_servizio']}'>{$row['desc_servizio']}</option>";
}

//pg_free_result($result1); 
