<?php
// a questo punto cerco l'utente 
  $query_op = "select concat(COGNOME, ' ', NOME) as op, matricola::int
from totem.v_personale_ekovision_step1 vpes 
where vpes.codice_badge = $1";

$result = pg_prepare($conn_hub, "query_op", $query_op);

if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo pg_last_error($conn_hub);
    #$res_ok= $res_ok+1;
}
//echo "Sono qua 2";
$result = pg_execute($conn_hub, "query_op", array($consuntivatore));  
if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo pg_last_error($conn_hub);
    #$res_ok= $res_ok+1;
}

while($r = pg_fetch_assoc($result)) {
  $op =  $r['op'] ;
  $matricola =  $r['matricola'] ;
}

$matricola = $matricola ?? '';
$op = $op ?? '<font color="coral">Badge non riconosciuto</font>';
?>