<?php
session_start();
#require('../validate_input.php');

header('Content-Type: application/json; charset=utf-8');



require_once ('../conn.php');





/*echo $_GET["d"];
echo "<br>";
echo $_GET["uos"] ;
*/
# cerco le UT dell'utente


$id=$_GET['id'];
$datalav=$_GET['datalav'];
$id_uo=$_GET['id_uo'];

/*echo $id.'<br>';
echo $datalav.'<br>';
exit();
*/


if(!$conn_hub) {
    die('Connessione fallita !<br />');
} else {
 
    
$query0="
select 
    *, 
    case when check_previsto = 1 or id_causale is not null then 1
    else 0
    end check_prev_cons
from 
    (select distinct id_tappa_raggr as tappa, 
    id_percorso as idpercorso, 
    desc_uo as zona,
    cpra.nome_via, 
    nota_via as tratto, 
    case 
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=1 then cpra.lun
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=2 then cpra.mar
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=3 then cpra.mer
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=4 then cpra.gio
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=5 then cpra.ven
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=6 then cpra.sab
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=7 then cpra.dom
    end as check_previsto,
    e.id_causale, e.punteggio, 
    e.codice, e.datainsert
    from spazzamento.cons_percorsi_spazz_x_app cpra
    left join spazzamento.v_effettuati e on e.tappa = cpra.id_tappa_raggr and datalav = to_date($1, 'YYYY-MM-DD')
    where to_date($1, 'YYYY-MM-DD') between data_inizio and data_fine
    and id_percorso = $2  
    and id_uo = $3) as s1
order by 6 desc, 1";


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
$result = pg_execute($conn_hub, "query0", array($_GET['datalav'], $_GET["id"], $_GET["id_uo"]));  
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