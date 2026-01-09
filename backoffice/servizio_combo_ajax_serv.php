<?php
session_start();
if ($_SESSION['test']==1) {
    require('../conn_test.php');
} else {
    require('../conn.php');
}

$ut = intval($_GET['ut']) ?? null;

$data = $_GET['data_percorsi'] ?? null;

$tipo = $_GET['tipo'] ?? null;


if (!$ut) {
    echo "<option value='0'>Ut non valida</option>";
    exit;
}


if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

if (!$tipo) {
    echo "<option value='0'>Tipo servizio non valido</option>";
    exit;
}

$query_servizio = "SELECT DISTINCT 
id_tipo as id_servizio,
spe.desc_tipo as desc_servizio  
from servizi.servizi_per_ekovision spe 
left join servizi.mail_ut mu on mu.id_ut_sit = spe.id_uo_sit  
where mu.id_uo = $1::int
and to_date($2, 'DD/MM/YYYY') between spe.data_inizio_validita and spe.data_fine_validita
and tipo_servizio = $3             
order by 2";

$result = pg_prepare($conn_hub, "query_servizio", $query_servizio);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_servizio", array($ut, $data, $tipo));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

echo "<option value='0'>Seleziona un servizio</option>";
while ($row = pg_fetch_assoc($result)) {
    echo "<option value='{$row['id_servizio']}'>{$row['desc_servizio']}</option>";
}

//pg_free_result($result1); 
