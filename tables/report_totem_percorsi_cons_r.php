<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
#require('../validate_input.php');

header('Content-Type: application/json; charset=utf-8');



require_once '../carica_env.php';
require_once '../conn_ok.php';





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
SELECT 
    s1.*, 
    CASE 
        WHEN check_previsto = 1 OR id_causale IS NOT NULL THEN 1
        ELSE 0
    END AS check_prev_cons
FROM (
    SELECT DISTINCT 
        cpra.id_tappa AS tappa, 
        cpra.id_percorso AS idpercorso, 
        cpra.desc_uo AS zona,
        cpra.id_via, 
        CONCAT(cpra.nome_via, ', ', cpra.civico, ' - ', cpra.utente_posizione, ' - ', cpra.nome_elemento) AS riferimento, 
        e.fatto, 
        cpra.num_elementi,
        totem.verify_daily_frequency(
            cod_frequenza_tratto,            TO_DATE($1, 'DD/MM/YYYY'),
            freq_settimane
        ) AS check_previsto,
        e.id_causale,
        e.codice,
        e.datainsert,
        e.fatto
    FROM raccolta.cons_percorsi_raccolta_amiu cpra
    /* faccio distinct on (tappa) per dare un solo valore
     * 	con l' ORDER BY decido  
         * se UT vince su operatore (e.substr(e.codice,0,2) desc)
         * a parità di UT vince l'ultima arrivata 
         * */
    LEFT JOIN (
        SELECT DISTINCT ON (tappa)
            tappa,
            id_causale,
            codice,
            datainsert,
            fatto
        FROM raccolta.v_effettuati
        WHERE datalav = TO_DATE($1, 'DD/MM/YYYY')
        ORDER BY tappa, 
        /* tappa essendo nel distinct on deve esserci per forza*/
        case
	        when substr(codice,0,2) = 'UT' then 'UT'
	        else 'AA'
        end desc,
        datainsert DESC
    ) e ON e.tappa = cpra.id_tappa
    WHERE TO_DATE($1, 'DD/MM/YYYY') BETWEEN data_inizio AND data_fine
      AND id_percorso = $2
      AND id_uo = $3
) AS s1
ORDER BY check_prev_cons DESC, 1;";


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