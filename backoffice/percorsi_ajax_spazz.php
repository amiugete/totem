<?php
require_once('../conn.php');

$data = $_GET['data_percorsi'];
$ut = $_GET['ut'];
$servizio = $_GET['servizio'];

if (!$data) {
    echo "<option value='0'>Data non valida</option>";
    exit;
}

$query_percorsi = "SELECT DISTINCT id_percorso, desc_percorso
              FROM spazzamento.cons_percorsi_spazz_x_app cpsxa
              WHERE cpsxa.id_uo = $1
              AND cpsxa.id_servizio = $2
              AND to_date($3, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine
              order by 2";

$result = pg_prepare($conn_hub, "query_percorsi", $query_percorsi);
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}
$result = pg_execute($conn_hub, "query_percorsi", array($ut, $servizio, $data));
if (pg_last_error($conn_hub)){
  echo pg_last_error($conn_hub);
}

echo '<hr><!--ul-->';
// da mettere tabella
while ($row = pg_fetch_assoc($result)) {
    ?>
    <h3>
      <!--li-->
      <div class="form-check">
          <input class="form-check-input" type="radio" onchange="scelto_percorso(this.value);" name="percorso" id="<?php echo $row['id_percorso']?>">
          <label class="form-check-label" for="flexRadioDefault1">
          <?php echo $row['id_percorso']?> - <?php echo $row['desc_percorso'];?>
          </label>
      </div>
        
      <!--/li-->
</h3>
    <?php
}
echo '<!--/ul-->';
//pg_free_result($result1); 
