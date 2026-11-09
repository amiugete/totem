<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once '../carica_env.php';
require_once '../conn_ok.php';

$data = $_GET['data_percorsi'];
$ut = $_GET['ut'];
$servizio = $_GET['servizio'];

if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_percorsi = "SELECT DISTINCT id_percorso, desc_percorso, 
              case when id_squadra = 44 then 0 
              else  totem.verify_daily_frequency(
              cpsxa.cod_frequenza_percorso, 
              to_date($1, 'DD/MM/YYYY'),
              cpsxa.freq_settimane) 
              end as previsto, 
              case 
              	when t.fascia_turno = 'N' 
                  and t.fine_ora::int between 4 and 7 
                                          then 1
              	when t.fascia_turno = 'M' then 2
              	when t.fascia_turno = 'P' then 3
              	when t.fascia_turno = 'N'
                and (t.fine_ora::int > 7 or t.fine_ora::int <=3)
                                          then 4
              	else 5
              end as order_turno, 
              case 
              	when t.fascia_turno = 'N' 
                  and t.fine_ora::int between 4 and 7 
                                          then 'T. Notturno'
              	when t.fascia_turno = 'M' then 'T. Antimeridiano'
              	when t.fascia_turno = 'P' then 'T. Pomeridiano'
              	when t.fascia_turno = 'N'
                and (t.fine_ora::int > 7 or t.fine_ora::int <=3)
                                          then 'T. Serale'
              	else 'Altro turno'
              end as  fascia
              FROM spazzamento.cons_percorsi_spazz_x_app cpsxa
              left join raccolta.anagr_turni t on t.id_turno = cpsxa.id_turno
              WHERE cpsxa.id_uo = $2
              AND cpsxa.id_servizio = $3
              AND to_date($1, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine
              order by 3 desc, 4 asc,  2 asc";

$result = pg_prepare($conn_hub, "query_percorsi", $query_percorsi);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_percorsi", array($data, $ut, $servizio));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

/*
echo '<hr><!--ul-->';
// da mettere tabella
$check_previsto =1;
$check_scritta_np =0;
$check_scritta_p =0;



while ($row = pg_fetch_assoc($result)) {
    
    if ($row['previsto'] == 0){
      $check_previsto = 0;
      if ($check_previsto==0 and $check_scritta_np==0){
        echo '<hr><h4 style="color:dimgrey;"><i class="fa-solid fa-calendar-xmark"></i>
         <i> Percorsi NON previsti</i>
        </h4>';
        $check_scritta_np=1;
      }
    } else {
      if ($check_previsto==1 and $check_scritta_p==0){
        echo '<h4 style="color:black;"><i class="fa-solid fa-calendar-check"></i>
         <i> Percorsi previsti</i>
        </h4>';
        $check_scritta_p=1;
      }
    }
    ?>
    <h3>
      <!--li-->
      <div class="form-check">
          <input class="form-check-input" type="radio" onchange="scelto_percorso(this.value);" 
          name="percorso" 
          id="<?php echo $row['id_percorso']?>"
          value="<?php echo $row['id_percorso']?>">
          <label for="<?php echo $row['id_percorso']?>" 
            class="form-check-label" <?php if ($row['previsto'] == 1){ echo 'style="color:black;"';} else { echo 'style="color:dimgrey;"';  }?> for="flexRadioDefault1">
            <?php 
              echo $row['id_percorso']?> - <?php echo $row['desc_percorso'];
            ?>
          </label>
      </div>
        
      <!--/li-->
</h3>
    <?php
  

}
echo '<!--/ul-->';
//pg_free_result($result1); 
*/

require_once("./percorsi_ajax_print.php");
?>

