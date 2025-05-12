<?php
session_start();
#require('../validate_input.php');

header('Content-Type: application/json; charset=utf-8');



require_once ('../conn.php');
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
descr_servizio, id_percorso, descr_percorso,
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
	when causali is null then 'NON CONSUNTIVATO'
end stato_consuntivazione, datalav
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
	string_agg(distinct descr_causale, ',') as causali_text, datalav
	from (
		select at2.descr_orario,
		cpra.desc_servizio as descr_servizio, cpra.id_percorso, cpra. desc_percorso as descr_percorso, 
		cpra.desc_uo, cpra.id_uo as id_uo_esec,
		--pu.id_uo,
		case 
			when (ea.punteggio='100' and trim(replace(ea.causale, ' - (no in questa giornata)', '')) = '') then 'COMPLETATO'
			else trim(replace(ea.causale, ' - (no in questa giornata)', '')) 
		end as descr_causale
		,
		case 
			when (ea.punteggio='100' and trim(replace(ea.causale, ' - (no in questa giornata)', '')) = '') then '100'
			else ct.id::text
		end as causale,
		case 
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=1 then cpra.lun
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=2 then cpra.mar
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=3 then cpra.mer
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=4 then cpra.gio
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=5 then cpra.ven
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=6 then cpra.sab
			when extract(dow from to_date($1, 'DD/MM/YYYY'))=7 then cpra.dom
		end as check_previsto, 
		coalesce(ea.datalav, to_date($1, 'DD/MM/YYYY')) as datalav
		from spazzamento.cons_percorsi_spazz_x_app cpra
		left join spazzamento.tappe_turni tt on tt.id_tappa_raggr = cpra.id_tappa_raggr
		left join raccolta.anagr_turni at2 on at2.id_turno = tt.id_turno
		--left join raccolta.tipi_rifiuto tr on tr.nome= cpra.tipo_rifiuto 
		--left join spazzamento.aste_ut pu on pu.id_asta=cpra.id_asta
		left join spazzamento.v_effettuati ea on ea.tappa::bigint = cpra.id_tappa_raggr::bigint 
											and ea.datalav = to_date($1, 'DD/MM/YYYY')
		left join spazzamento.causali_testi ct on trim(ct.descrizione) = trim(ea.causale)
		where (to_date($1, 'DD/MM/YYYY') between cpra.data_inizio and (cpra.data_fine - interval '1' day))
		) as step0
	group by descr_servizio, id_percorso, descr_percorso, descr_orario,datalav
) as step1
where /*(causali is not null or check_previsto > 0) and */ ($2 = any(id_uo_esec))
order by 8 desc, 1, 4, 2
            ";


//echo $query0;
//echo $uos;
//echo "Sono qua";



$result = pg_prepare($conn_hub, "query0", $query0);

if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    pg_last_error($conn_hub);
    $res_ok= $res_ok+1;
}
//echo "Sono qua 2";
$result = pg_execute($conn_hub, "query0", array($_GET["d"], $_GET['uos']));  
if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    pg_last_error($conn_hub);
    $res_ok= $res_ok+1;
}
//echo "Sono qua 3";


$rows = array();
while($r = pg_fetch_assoc($result)) {
    $rows[] = $r;
    //echo $r['piazzola'];
}
        


require_once("./json_no_paginazione.php");



exit(0);
}


?>