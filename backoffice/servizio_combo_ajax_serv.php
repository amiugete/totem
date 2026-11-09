<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once '../carica_env.php';
require_once '../conn_ok.php';

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

$query_servizio = "SELECT  
id_tipo as id_servizio,
spe.desc_tipo as desc_servizio,
count(id_percorso)
from servizi.servizi_per_ekovision spe 
left join servizi.mail_ut mu on mu.id_ut_sit = spe.id_uo_sit 
left join servizi.mail_ut mu1 on mu1.id_ut_sit = spe.id_rimessa_sit 
 WHERE (mu.id_uo = $1::int or mu1.id_uo = $1::int) 
and to_date($2, 'DD/MM/YYYY') between spe.data_inizio_validita and spe.data_fine_validita
and tipo_servizio = $3     
group by id_tipo, spe.desc_tipo        
order by 3 desc, 2";

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
