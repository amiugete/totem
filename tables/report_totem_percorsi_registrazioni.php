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






if(!$conn_hub) {
    die('Connessione fallita !<br />');
} else {
 
    
$query0="with registrazioni as (
	select at2.descr_orario, 
	spe.id_percorso, spe.descrizione,
	spe.desc_tipo as descr_servizio, 
	totem.verify_daily_frequency(
		id_frequenza,
		to_date($1, 'DD/MM/YYYY'),
		freq_settimane
	) as check_previsto, 
	concat(vpes.cognome, ' ', vpes.nome) as nominativo,
	case 
		when r.autista = true then '<i class=\"fa-solid fa-user-shield\" title=\"Autista\"></i>'
		else null
	end autista,
	r.sportello, 
	r.datainsert,
	r.datalav,
	r.send_ekovision, 
	r.errori
	from servizi.servizi_per_ekovision spe
	left join raccolta.anagr_turni at2 on at2.id_turno = spe.id_turno
	left join servizi.registrazioni r on spe.id_percorso = r.id_percorso
		and  datalav = to_date($1, 'DD/MM/YYYY')
	left join totem.v_personale_ekovision_step1 vpes on vpes.matricola = lpad(r.codice, 5,'0')
	where to_date($1, 'DD/MM/YYYY') 
		between spe.data_inizio_validita 
		and (spe.data_fine_validita - interval '1' day)
	and (
		spe.id_rimessa_sit = (select id_ut_sit from servizi.mail_ut where id_uo = $2) 
		or spe.id_uo_sit = (select id_ut_sit from servizi.mail_ut where id_uo = $2)
		)
)
select descr_orario,
descr_servizio, id_percorso, 
descrizione as descr_percorso,
case 
	when check_previsto = 1 then 'PREVISTO'
	else 'NON PREVISTO'
end in_previsione, 
string_agg(case
	when autista is not null
	then concat(nominativo, ' ', autista)
	else nullif(trim(nominativo), '')
	end, ', ') as nominativo, 
string_agg(nullif(trim(sportello), ''), ', ') as sportello,
max(datalav) as datalav,
max(datainsert) as datainsert,
bool_or(coalesce(send_ekovision, false)) da_inviare,
bool_or(coalesce(errori, false)) errori
from registrazioni
group by descr_orario,
descr_servizio, id_percorso, 
descr_percorso,
check_previsto
order by in_previsione desc, descr_orario, id_percorso
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