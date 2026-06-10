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
WITH base AS (
    SELECT DISTINCT
        cpra.id_tappa_raggr AS tappa,
        cpra.id_percorso AS idpercorso,
        cpra.desc_uo AS zona,
        cpra.nome_via,
        cpra.nota_via,
        concat(cpra.nome_via, ' - ', cpra.nota_via) AS tratto,
        totem.verify_daily_frequency(
            cod_frequenza_tratto,
            to_date($1, 'DD/MM/YYYY'),
            freq_settimane
        ) AS check_previsto,
        e.id_causale,
        e.punteggio,
        e.codice,
        e.datainsert,
        /* uso row_number per numerare in senso decrescente consuntivazioni sulla stessa tappa
         * se UT vince su operatore (e.substr(e.codice,0,2) desc)
         * a parità di UT vince l'ultima arrivata 
         * */
        ROW_NUMBER() OVER (
            PARTITION BY
                cpra.id_tappa_raggr,
                cpra.id_percorso,
                cpra.desc_uo,
                cpra.nome_via,
                cpra.nota_via,
                concat(cpra.nome_via, ' - ', cpra.nota_via),
                totem.verify_daily_frequency(
                    cod_frequenza_tratto,
                    to_date($1, 'DD/MM/YYYY'),
                    freq_settimane
                )
            ORDER BY 
            case
	        when substr(e.codice,0,2) = 'UT' then 'UT'
	        else 'AA'
            end desc,  
            e.datainsert DESC NULLS LAST
        ) AS rn
    FROM spazzamento.cons_percorsi_spazz_x_app cpra
    LEFT JOIN spazzamento.v_effettuati e
        ON e.tappa = cpra.id_tappa_raggr
       AND e.datalav = to_date($1, 'DD/MM/YYYY')
    WHERE to_date($1, 'DD/MM/YYYY')
          BETWEEN data_inizio AND data_fine
      AND id_percorso = $2
      AND id_uo = $3
)
SELECT
    *,
    CASE
        WHEN check_previsto = 1
          OR id_causale IS NOT NULL
        THEN 1
        ELSE 0
    END AS check_prev_cons
FROM base
WHERE rn = 1
ORDER BY
    check_previsto DESC,
    tappa;";


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