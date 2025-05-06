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
 
    
$query0="select tappa, nome_via, tratto,
punteggio,
causale, 
case 
	when vpes.cognome is not null then concat(vpes.matricola, ' - ', vpes.cognome,' ', vpes.nome)
	else e.codice
end as operatore
from spazzamento.effettuati e
left join totem.v_personale_ekovision_step1 vpes on vpes.codice_badge::text = e.codice 
where idpercorso = $1 
and datalav = to_date($2, 'YYYY-MM-DD')
order by 1";


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
$result = pg_execute($conn_hub, "query0", array($_GET["id"], $_GET['datalav']));  
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