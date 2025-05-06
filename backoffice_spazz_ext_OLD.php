<?php
//session_set_cookie_params($lifetime);
session_start();

    
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Gestione servizi</title>
<?php 
require_once('./req.php');

the_page_title();


require_once ('./conn.php');

?> 





</head>

<body>

<?php require_once('./navbar_up.php');

$name=dirname(__FILE__);

//************************************************************************************ */
// Controllo permessi
/*if (trim($check_coge) != 't') { 
  require('assenza_permessi.php');
  exit;
}*/
//************************************************************************************ */

?>


<div class="container">



<h4>Percorsi spazzamento / lavaggio consuntivabili</h4>
<hr>

<!--form class="row" name="open_ut" method="post" id="open_ut" autocomplete="off" action="download_driver_ekovision.php" -->
<form class="row" name="open_ut" method="POST" id="open_ut" autocomplete="off" action="https://expo.wingsoft.it/amiu/webapp/indexspazzasuser.php">
<!--cablato operatore pianar da sistemare meglio-->
<input type="hidden" id="operatore" name="operatore" value="6409">
<!--cablato -->
<input type="hidden" id="consuntivatore" name="consuntivatore" value="UT170">
<!--cablato -->
<input type="hidden" id="admin1" name="admin1" value="1958">

<?php 

//echo $username;
// 
// 
// 

# imposto l'orario del server
$today = new DateTime('now');
$timezone = new DateTimeZone('Europe/Rome');
$today->setTimezone($timezone);
?>


<script>

window.onload = function() {
  var data1 = document.getElementById("js-date1");
  console.log(data1)

  
</script>



<div class="form-group col-lg-4">
  <label for="data_isp" class="form-label">Data consuntivazione</label>
  <input type="text" class="form-control" id="js-date1" name="data1" value="<?php echo $today->format('d/m/Y');?>" required>
</div>


<div class="form-group col-lg-4">
<label for="tipo_report" class="form-label" >Seleziona il percorso</label><font color="red">*</font>
<select class="selectpicker show-tick form-control" 
  data-live-search="true" name="piazzola" id="piazzola" placeholder="Seleziona un percorso" required="">

  <?php 
  

  $query2="select distinct id_percorso, desc_percorso, desc_servizio
from spazzamento.cons_percorsi_spazz_x_app cpsxa 
where id_uo = 11 
and to_date('20250430', 'YYYYMMDD') between data_inizio and data_fine
order by 2";

  $result2 = pg_query($conn_hub, $query2);

  while($r2 = pg_fetch_assoc($result2)) { 

?>
              
          <option name="piazzola" value="<?php echo $r2['id_percorso'];?>" ><?php echo $r2['id_percorso'] .' - ' .$r2['desc_percorso'] .' ('.$r2['desc_servizio'].')';?></option>
  <?php } ?>

  </select> 
</div>


<?php 
$dt= new DateTime();
$today = new DateTime();
$last_month = $dt->modify("-1 year");

?>








<div class="form-group col-lg-4">
<button type="submit" id="sbtn" class="btn btn-primary"><i class="fa-solid fa-file-excel"></i> Vai alla consuntivazione</button>
</div>


</form>





<div class="row align-items-center" style="display:none;" id="output_message">
  <hr>
  <img src="./img/loading.gif" alt="loader1" style="height:30px; width:auto;" class="img-fluid" id="loaderImg">
  L'operazione potrebbe impiegare un po' di tempo. Attendere, il file sarà presto disponibile per il download. 
  <img src="./img/loading.gif" alt="loader1" style="height:30px; width:auto;" class="img-fluid" id="loaderImg">

  <i class="fa-solid fa-envelopes-bulk"></i> Inoltre verrà inviato via mail all'indirizzo <?php echo $mail_user;?>
  <!--div id='seconds-counter'> </div-->
</div>


</div>
<?php
require_once('req_bottom.php');
require('./footer.php');
?>


<script>

$('#js-date').datepicker({
    format: 'dd/mm/yyyy',
    //startDate: '+1d', 
    language:'it' 
});

$('#js-date1').datepicker({
    format: 'dd/mm/yyyy', 
    language:'it'
});
</script>


</body>

</html>