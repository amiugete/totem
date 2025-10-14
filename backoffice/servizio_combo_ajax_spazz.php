<?php
require_once('../conn.php');

$data = $_GET['ut'] ?? null;

if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_servizio = "SELECT DISTINCT id_servizio, desc_servizio
              FROM spazzamento.cons_percorsi_spazz_x_app
              where 
              /*to_date('29/07/2025', 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine */
              id_uo=$1
              order by 2";

$result = pg_prepare($conn_hub, "combo_query1", $query_servizio);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "combo_query1", array($data));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

echo "<option value='0'>Seleziona un Servizio</option>";
while ($row = pg_fetch_assoc($result)) {
    echo "<option value='{$row['id_servizio']}'>{$row['desc_servizio']}</option>";
}

//pg_free_result($result1); 
