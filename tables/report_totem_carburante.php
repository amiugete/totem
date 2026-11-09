<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
#require('../validate_input.php');

header('Content-Type: application/json; charset=utf-8');



require_once '../carica_env.php';
require_once '../conn_ok.php';
//echo "OK";



#exit();

$datalav = $_GET['d'] ?? '';
$uos = $_GET['uos'] ?? '';

$uos_array = array_filter(
    array_map('trim', explode(',', $uos)),
    'ctype_digit'
);


$uos = implode(',', $uos_array);



if(!$conn_hub) {
    die('Connessione fallita !<br />');
} else {
 
    
$query0="select cr.sportello, 
mi.descrizione, 
mi.targa,
mi.tipo_carburante ,
cr.datalav, 
cr.litri , cr.importo,
cr.chilometri, 
cr.datainsert, 
concat (vpes.matricola, ' - ', vpes.cognome, ' ', vpes.nome) as nominativo,
mu.desc_uo as UO_totem,
mu1.desc_uo /*es.des_sede_old*/ as sede_mezzo_infopm, 
'Esterni' as origine, 
null as pompa
from servizi.carburante_rifornimenti cr 
left join totem.v_personale_ekovision_step1 vpes on vpes.matricola = lpad(cr.codice, 5, '0')
left join servizi.mail_ut mu on mu.id_uo  = cr.id_ut  
left join totem.mezzi_infopm mi on cr.sportello::int = mi.sportello::int
left join servizi.mail_ut mu1 on mu1.id_uo  = mi.id_uo 
/*left join (select distinct es.cod_sede_operativa_rif, es.cod_sede_old, es.des_sede_old  
from totem.elenco_sedi es 
where es.cod_sede_old is not null) es on es.cod_sede_old = mi.id_sede_presa_serv */
where cr.datalav >= to_date($1, 'DD/MM/YYYY')
and 
(cr.id_ut::int = ANY(string_to_array($2, ',')::int[])
or 
mi.id_uo::int = ANY(string_to_array($2, ',')::int[])
)
union all
select 
r.veicolo::int::text as sportello,
mi.descrizione, 
mi.targa,
mi.tipo_carburante,
r.datalav,
r.litri as litri, 
null as import, 
r.chilometri,
r.data_ora as datainsert,
concat (vpes.matricola, ' - ', vpes.cognome, ' ', vpes.nome) as nominativo,
null as UO_totem,
mu1.desc_uo /*es.des_sede_old*/ as sede_mezzo_infopm,
case 
	when r.cod_pompa in ('02','05') then 'AMIU Scarpino'
	else 'AMIU Volpara'
end origine,
case 
	when r.cod_pompa in ('02','05') then concat('Scarpino_',r.cod_pompa)
	else concat ('Volpara_', r.cod_pompa)
end pompa
from totem.rifornimenti_amiu r 
left join totem.mezzi_infopm mi on r.veicolo::int = mi.sportello::int
left join totem.v_personale_ekovision_step1 vpes on lpad(vpes.matricola, 5,'0') = r.matricola
left join servizi.mail_ut mu1 on mu1.id_uo  = mi.id_uo 
/*left join (select distinct es.cod_sede_operativa_rif, es.cod_sede_old, es.des_sede_old  
from totem.elenco_sedi es 
where es.cod_sede_old is not null) es on es.cod_sede_old = mi.id_sede_presa_serv */
where r.datalav >= to_date($1, 'DD/MM/YYYY')
and 
(mi.id_uo::int = ANY(string_to_array($2, ',')::int[]))
order by datalav 
            ";


//echo $query0;
//echo "Sono qua";
//exit;


$result = pg_prepare($conn_hub, "query0", $query0);

if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo pg_last_error($conn_hub);
    $res_ok= $res_ok+1;
}
//echo "Sono qua 2";
$result = pg_execute($conn_hub, "query0", array($datalav, $uos));  
if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo pg_last_error($conn_hub);
    $res_ok= $res_ok+1;
}
//echo "Sono qua 3";
//exit(0);

$rows = array();
while($r = pg_fetch_assoc($result)) {
    $rows[] = $r;
    //echo $r['piazzola'];
}
        


require_once("./json_no_paginazione.php");



exit(0);
}


?>