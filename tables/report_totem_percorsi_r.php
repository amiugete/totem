<?php
session_start();
#require('../validate_input.php');

header('Content-Type: application/json; charset=utf-8');



if ($_SESSION['test']==1) {
    require_once ('../conn_test.php');
} else {
    require_once ('../conn.php');
}
//echo "OK";

$dt= new DateTime();
$today = new DateTime();
$last_month = $dt->modify("-1 month");




/*echo $_GET["d"];
echo "<br>";
echo $_GET["uos"] ;
*/
# cerco le UT dell'utente


$filter_bis=" and trim(coalesce(descr_causale,'')) != 'COMPLETATO' ";
if($_GET["c"]=='all'){
	$filter_bis="";
}  

#exit();






if(!$conn_hub) {
    die('Connessione fallita !<br />');
} else {
 
    
$query0="select descr_orario,
descr_servizio, id_percorso, 
descr_percorso,
uo_esec,
causali, 
causali_text,
case 
	when check_previsto > 0 then 'PREVISTO'
	else 'NON PREVISTO'
end in_previsione, 
case 
	when causali='100' then 'COMPLETATO'
	when causali like '%100%' then 'NON COMPLETATO' 
	when causali is not null and causali not like '%100%' then 'NON EFFETTUATO' 
	when causali is null and no_autista = 0 then 'NON CONSUNTIVATO'
	when causali is null and no_autista = 1 then 'NON CONSUNTIVATO NO AUTISTA'
end stato_consuntivazione, datalav,
id_percorso as id_percorso1
from (
	select descr_orario,
	descr_servizio, id_percorso, descr_percorso, 
	string_agg(distinct desc_uo, ',') as uo_esec,
	array_agg(distinct id_uo_esec) as id_uo_esec,
	/*array_agg(distinct id_uo) as uo,*/
	sum(
		check_previsto
	) as check_previsto,
	string_agg(distinct causale, ',') as causali, 
	string_agg(distinct descr_causale, ',') as causali_text, datalav,
	no_autista
	from (
		select at2.descr_orario,
		cpra.desc_servizio as descr_servizio, cpra.id_percorso, cpra.desc_percorso as descr_percorso, 
		cpra.desc_uo, cpra.id_uo as id_uo_esec,
		--pu.id_uo,
		case 
			when (trim(replace(ea.causale, ' - (no in questa giornata)', '')) = '') then 'COMPLETATO'
			else trim(replace(ea.causale, ' - (no in questa giornata)', '')) 
		end as descr_causale
		,
		case 
			when (trim(replace(ea.causale, ' - (no in questa giornata)', '')) = '') then '100'
			else ct.id::text
		end as causale,
		totem.verify_daily_frequency(cod_frequenza_tratto,
		to_date($1, 'DD/MM/YYYY'),
		freq_settimane)
		as check_previsto,	
		coalesce(ea.datalav, to_date($1, 'DD/MM/YYYY')) as datalav, 
		case 
			when na.id_percorso is not null then 1
			else 0
		end as no_autista
		from raccolta.cons_percorsi_raccolta_amiu cpra
		left join raccolta.anagr_turni at2 on at2.id_turno = cpra.id_turno
		--left join raccolta.tipi_rifiuto tr on tr.nome= cpra.tipo_rifiuto 
		--left join spazzamento.aste_ut pu on pu.id_asta=cpra.id_asta
		left join raccolta.v_effettuati ea on ea.tappa::bigint = cpra.id_tappa::bigint 
											and ea.datalav = to_date($1, 'DD/MM/YYYY')
		left join raccolta.percorsi_no_autista_x_ekovision na 
			on na.id_percorso = cpra.id_percorso 
			and to_date($1, 'DD/MM/YYYY') = na.datalav
		left join raccolta.causali_testi ct on trim(ct.descrizione) = trim(ea.causale)
		where (to_date($1, 'DD/MM/YYYY') between cpra.data_inizio and (cpra.data_fine - interval '1' day))
		) as step0
	group by descr_servizio, id_percorso, descr_percorso, descr_orario,datalav, no_autista
) as step1
where /*(causali is not null or check_previsto > 0) and */ 
($2 = any(id_uo_esec))
order by 8 desc, 1, 2, 4
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
$result = pg_execute($conn_hub, "query0", array($_GET["d"], $_GET['uos']));  
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