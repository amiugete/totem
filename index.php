<?php
//session_set_cookie_params($lifetime);
session_start();

setcookie("username", 'Amiu1', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);


setcookie("password", '6409', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);

setcookie("cisiamo", 'OK', [
  'expires' => time() + 86400,
  'path' => '/amiu/webapp',
  'domain' => '',
  'secure' => true,
  'httponly' => false,
  'samesite' => 'None',
]);

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
  
 


<div class="container text-center">

  <div class="row">
    <div class="col">
      <a type="button" class="btn btn-primary" href="./backoffice_spazz_ext.php">
        <i class="fa-solid fa-broom"></i> Consuntivazione spazzamento/lavaggio
      </a>
    </div>
  </div>
  <!--hr>
  <div class="row">
    <div class="col">
      <a type="button" class="btn btn-primary" disabled="" href="./backoffice_spazz_ext.php">
      <i class="fa-solid fa-trash"></i> Consuntivazione raccolta
      </a>
    </div>
  </div-->

  
</div>

<?php 
require_once('req_bottom.php');
require('./footer.php');
?>


<script type="text/javascript">
 var today = new Date();
 var week_before=new Date();
 week_before.setDate(week_before.getDate() - 7);
$('#js-date3').datepicker({
      format: 'dd/mm/yyyy',
      todayBtn: "linked", // in conflitto con startDate
      endDate:today,
      startDate:week_before,
      language:'it', 
      autoclose: true
  });


  //$('#myIframe').attr('src', "https://expo.wingsoft.it/amiu/webapp/indexdesk.php?operatore=0170"); 
 
  
</script>


</body>

</html>