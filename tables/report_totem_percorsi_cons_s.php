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
    case when check_previsto = 1 then true
    else false
    end as check
from 
    (select id_tappa_raggr as tappa, 
    id_percorso as idpercorso, 
    desc_uo as zona,
    nome_via, 
    nota_via as tratto, 
    case 
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=1 then cpra.lun
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=2 then cpra.mar
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=3 then cpra.mer
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=4 then cpra.gio
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=5 then cpra.ven
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=6 then cpra.sab
                when extract(dow from to_date($1, 'YYYY-MM-DD'))=7 then cpra.dom
    end as check_previsto
    from spazzamento.cons_percorsi_spazz_x_app cpra
    where to_date($1, 'YYYY-MM-DD') between data_inizio and data_fine
    and id_percorso = $2  
    and id_uo = 170) as s1
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
$result = pg_execute($conn_hub, "query0", array($_GET['datalav'], $_GET["id"]));  
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