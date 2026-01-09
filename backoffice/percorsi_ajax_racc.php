<?php
session_start();
if ($_SESSION['test']==1) {
    require_once ('../conn_test.php');
} else {
    require_once ('../conn.php');
}

$data = $_GET['data_percorsi'];
$ut = $_GET['ut'];
$servizio = $_GET['servizio'];

if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_percorsi = "SELECT DISTINCT id_percorso, desc_percorso, 
              totem.verify_daily_frequency(
              cpsxa.cod_frequenza_percorso, 
              to_date($1, 'DD/MM/YYYY'),
              cpsxa.freq_settimane) as previsto 
              FROM raccolta.cons_percorsi_raccolta_amiu cpsxa
              WHERE cpsxa.id_uo = $2
              AND cpsxa.id_servizio = $3
              AND to_date($4, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine
              order by 3 desc, 2";

$result = pg_prepare($conn_hub, "query_percorsi", $query_percorsi);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_percorsi", array($data, $ut, $servizio, $data));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

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
          <input class="form-check-input" type="radio" onchange="scelto_percorso(this.value);" name="percorso" id="<?php echo $row['id_percorso']?>">
          <label class="form-check-label" <?php if ($row['previsto'] == 1){ echo 'style="color:black;"';} else { echo 'style="color:dimgrey;"';  }?> for="flexRadioDefault1">
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
