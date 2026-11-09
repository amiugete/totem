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
$op = $op ?? '<font color="orangered">Badge non riconosciuto</font>';


// vedo se si fosse già registrato nelle ultime 8 ore
$ore = 8;

if ($tipo_servizio_call) {
  $filter_add = " and spe.tipo_servizio = '". $tipo_servizio_call. "' ";
  if ($tipo_servizio_call == 'RACCOLTA'){
    $cc = " coalesce((SELECT distinct 1
      FROM raccolta.cons_percorsi_raccolta_amiu c
      JOIN raccolta.effettuati_amiu ea
        ON ea.tappa = c.id_tappa
       AND ea.datalav = r.datalav
      WHERE c.id_percorso = r.id_percorso
        AND r.datalav BETWEEN c.data_inizio AND c.data_fine), 0) as consuntivato ";
  } else if ($tipo_servizio_call == 'SPAZZAMENTO'){
    $cc = " coalesce((SELECT distinct 1
      FROM spazzamento.v_effettuati  c
      WHERE c.idpercorso = r.id_percorso
        AND c.datalav = r.datalav ), 0)
     as consuntivato ";
  }


} else {
  $cc= "0 as consuntivato";
  $filter_add = ' ';
}

$query_mezzo_percorso_reg="select r.id_percorso, 
datalav, sportello, 
spe.id_tipo, spe.descrizione, 
".$cc."
from servizi.registrazioni r 
left join servizi.servizi_per_ekovision spe on spe.id_percorso = r.id_percorso 
where codice = $1
and r.datainsert >= (now()- interval '".$ore."' hour)
".$filter_add."
order by 6, r.datainsert desc 
limit 1
  ";

  

  $result_operatore = pg_prepare($conn_hub, "query_mezzo_percorso_reg", $query_mezzo_percorso_reg);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

   
  $result_operatore = pg_execute($conn_hub, "query_mezzo_percorso_reg", array($matricola));


  if (pg_last_error($conn_hub)){
      echo pg_last_error($conn_hub);
      $res_ok=$res_ok+1;
  }
    
  while($r = pg_fetch_assoc($result_operatore)) {
    $id_percorso_reg = $r['id_percorso'];
    $id_servizio_reg = $r['id_tipo'];
    $datalav_reg = $r['datalav'];
    $sportello_reg = $r['sportello'];
    $desc_percorso_reg = $r['descrizione'];
    $consuntivato_reg = $r['consuntivato'];
  }


?>