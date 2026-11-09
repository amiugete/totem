<?php
//session_set_cookie_params($lifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    <title>Backoffice registrazioni</title>
<?php 
require_once('./req.php');

the_page_title();


require_once 'carica_env.php';
require_once 'conn_ok.php';





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
  
  <script>
  function utScelta(val) {
    document.getElementById('open_ut').submit();
  }


</script>


<div class="container-fluid">

<?php 


# filtro sulle UT che abbiano il totem
$filter_totem="OK";


require_once('./query_ut.php');


$result1 = pg_prepare($conn, "query_ut", $query_ut);
if (pg_last_error($conn)){
  echo pg_last_error($conn);
  //$res_ok=$res_ok+1;
}
$result1 = pg_execute($conn, "query_ut", array($_SESSION['username']));
if (pg_last_error($conn)){
  echo pg_last_error($conn);
  //$res_ok=$res_ok+1;
}

// se una sola UT non la faccio nemmeno scegliere

$r = pg_fetch_all ($result1);
//echo count($r)."<br>";
if (count($r) == 1) {
  $ut0 = $r[0]['id_ut'];
} else {
  $ut0=$_POST['ut0'];
}



if ($ut0) {
  $query0='select u.id_ut, u.descrizione, cmu.id_uo
  from topo.ut u
  left join anagrafe_percorsi.cons_mapping_uo cmu on cmu.id_uo_sit = u.id_ut 
  where id_ut = $1';

  $result0 = pg_prepare($conn, "my_query0", $query0);
  $result0 = pg_execute($conn, "my_query0", array($ut0));
  
  while($r0 = pg_fetch_assoc($result0)) {
      $uos=$r0["id_uo"];
      $uos_descrizione= $r0["descrizione"];
  }
  //echo $uos;


$today = new DateTime('now');
$timezone = new DateTimeZone('Europe/Rome');
$today->setTimezone($timezone);
$hour = $today->format('Hi');
if ($hour < '1120'){
    $today = $today->modify("-1 day");
    $nota_data='<font color="red"> <i class="fa-solid fa-clock-rotate-left"></i> Prima della fine del turno mattutino è impostata di default la data di ieri </font>';
} 
?>




<style>
.vl {
  border-left: 6px solid green;
  height: 72px;
}
</style>

<form  class="row row-cols-lg-auto g-3 align-items-center" name="form_filtro" id="form_filtro" autocomplete="off">
   

    <input type="hidden" class="form-control" id="uos" name="uos" value="<?php echo $uos;?>" required>

<div class="form-group col-lg-3">
  <?php echo $uos_descrizione." - " ;?>
<a class="btn btn-info" href="<?php echo basename($_SERVER['PHP_SELF']);?>"> <i class="fa-solid fa-house"></i>Cambia UT</a>
</div>

<i class="fa-solid fa-grip-lines-vertical"></i>

<div class="form-group col-lg-3">
    
      <label for="data_percorsi" class="form-label">Data verifica</label>
      <div class="input-group">
      <button type="button" class="btn btn-outline-secondary" id="prevDay" title="Giorno precedente">
        <i class="fa-solid fa-chevron-left"></i>
      </button><input type="text" class="form-control" id="js-date3" name="data_percorsi" onchange="cambiata_data(this.value);" value="<?php echo $today->format('d/m/Y');?>" required>
      <!--input type="text" class="form-control" id="js-date3" name="data_percorsi" value="<?php echo $today->format('d/m/Y');?>" required-->
       <button type="button" class="btn btn-outline-secondary" id="nextDay" title="Giorno successivo">
        <i class="fa-solid fa-chevron-right"></i>
      </button></div>
       <div id="nota_data" class="form-text"><?php echo $nota_data;?></div>

</div>
<!--div class="form-group col-lg-2">
<button type="submit" class="btn btn-primary">Filtra</button>
</div-->
</form>

<script>



  function cambiata_data(val) {
    aggiornaBottoniNavigazione();
    console.log("Bottone cambiata_data  cliccato");
    var data_percorsi=val;
    console.log(data_percorsi);
    var uos="<?php echo $uos?>";
    console.log(uos);
    if ($('#check_id').is(":checked"))
    {
      var filtro_percorsi = 'all';
    } else {
      var filtro_percorsi = 'none';
    }
    
    $("#nota_data").html("").fadeIn("slow");
    $(function() {    // Faccio refres della data-url
    $table.bootstrapTable('refresh', {
      url: "./tables/report_totem_percorsi_registrazioni.php?uos="+uos+"&d="+data_percorsi
    }); 

  });
};



</script>



<hr>

<div id="tabella">
            
        <h4>
          <i class="fa-solid fa-user-tag"></i> 
          Report registrazioni personale e mezzi - <?php echo $uos_descrizione;?>
        </h4>

        <!-- Button trigger modal -->
<!--button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Consuntivazione
</button-->





<hr>
      
      <div id="ConsOutput2" class="text-center"></div>

        <div class="table-responsive-sm">

                  <!--div id="toolbar">
        <button id="showSelectedRows" class="btn btn-primary" type="button">Crea ordine di lavoro</button>
      </div-->
    
      <div id="toolbar" class="isDisabled"> 


     
      <!--a target="_new" class="btn btn-primary btn-sm"
         href="./export_consuntivazione_ekovision.php"><i class="fa-solid fa-file-excel"></i> Esporta xlsx completo</a-->
      </div>
				<table  id="totem_percorsi_registrazioni" class="table-hover table-sm" 
        idfield="ID_SCHEDA" 
        data-show-columns="true"
        data-group-by="false"
        data-group-by-field='["indirizzo", "frazione"]'
        data-show-search-clear-button="true"   
        data-show-export="false" 
        data-export-type=['json', 'xml', 'csv', 'txt', 'sql', 'pdf', 'excel',  'doc'] 
				data-search="false" data-click-to-select="true" data-show-print="false"  
        data-virtual-scroll="false"
        data-show-pagination-switch="false"
				data-pagination="false" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-side-pagination="false" 
        data-show-refresh="true" data-show-toggle="true"
        data-show-columns="true"
				data-filter-control="true"
        data-sort-select-options = "true"
        data-filter-control-multiple-search="false" 
        data-export-data-type="all"
        data-url="./tables/report_totem_percorsi_registrazioni.php?uos=<?php echo $uos?>&d=<?php echo $today->format('d/m/Y');?>&c=none" 
        data-toolbar="#toolbar" 
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-field="state" data-checkbox="true"></th-->    
        <th data-field="descr_servizio" data-sortable="true" data-visible="true"  data-filter-control="select">Tipo<br>Servizio</th>
        <th data-field="id_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Cod<br>Percorso</th> 
        <th data-field="descr_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Descrizione</th>
        <!--th data-field="uo_esec" data-sortable="true" data-visible="true" data-filter-control="input">UT<br>esec</th--> 
        <th data-field="descr_orario"  data-sortable="true" data-visible="true" data-filter-control="select">Turno</th>
        <th data-field="in_previsione" data-sortable="true" data-visible="true" data-filter-control="select"
        data-formatter="nameFormatterPrevisto_RACC" 
        data-filter-data="var:opzioni1" data-filter-strict-search="true" data-search-formatter="false">Previsto</th>
        <th data-field="nominativo" data-sortable="true" data-visible="true" data-filter-control="input">Nominativo</th>
        <th data-field="sportello" data-sortable="true" data-visible="true" data-filter-control="input">Sportello</th>        
        <th data-field="datalav" data-sortable="true"  data-formatter="dateFormat" data-visible="true" data-filter-control="input">Data<br>percorso</th>
        <th data-field="datainsert" data-sortable="true"  data-formatter="dateFormat2" data-visible="true" data-filter-control="input">Ultimo<br>aggiornamento</th>
        <th data-field="da_inviare" data-sortable="true" data-visible="true" data-filter-control="input" data-formatter="sent_format"></th>
        <th data-field="errori" data-sortable="true" data-visible="true" data-filter-control="input" data-formatter="error_format"></th>
        <!--th data-field="" data-sortable="true"  data-events="operateEvents" data-formatter="operateFormatter" 
         data-visible="true">Vis Consuntivato</th-->
    </tr>
</thead>
</table>







<script type="text/javascript">



var $table = $('#totem_percorsi_registrazioni');

$(function() {
    $table.bootstrapTable();
  });
  






var opzioni1 = ['PREVISTO', 'NON PREVISTO'];

function nameFormatterPrevisto_RACC(value, row, index) {
  //console.log('Valore in_previsione:', row.in_previsione);
  if (row.in_previsione =='PREVISTO'){
    return '<span style="font-size: 1em; color: green;"> <i title="'+row.in_previsione+'" class="fa-regular fa-calendar-check"></i></span>';
  } else if (row.in_previsione =='NON PREVISTO') {
    return '<span style="font-size: 1em; color: red;"> <i title="'+row.in_previsione+'" class="fa-regular fa-calendar-xmark"></i></span>';
  }
};

function sent_format(value, row, index) {
if (row.nominativo && value == true ){
    return '<span style="font-size: 1em; color: red;"> <i title="Dato ancora da inviare a Ekovision" class="fa-solid fa-upload"></i></span>';
  } else if (row.nominativo ){
    return '<span style="font-size: 1em; color: green;"> <i title="Dato già inviato a Ekovision" class="fa-solid fa-check"></i></span>';
  } else {
    return '';
  }
};


function error_format(value, row, index) {
if (row.nominativo && value == true ){
    return '<span style="font-size: 1em; color: orange;"> <i title="Errore nell\'invio a Ekovision" class="fa-solid fa-triangle-exclamation"></i></span>';
  } else {
    return '';
  }
};

  function queryParams(params) {
    var options = $table.bootstrapTable('getOptions')
    if (!options.pagination) {
      params.limit = options.totalRows
    }
    return params
  };



function dateFormat(value, row, index) {
   if (value){ 
    return moment(value).format('DD/MM/YYYY (ddd)');
   } else {
    return '-';
   }
};


function dateFormat2(value, row, index) {
   if (value){ 
    return moment(value).format('DD/MM/YYYY HH:mm');
   }
};



function operateFormatter(value, row, index) {
    if (row.stato_consuntivazione =='NON CONSUNTIVATO') {
      return '-';
    } else {
      return [
        '<a class="info btn btn-info btn-sm" data-bs-toggle="modal" id="dett" data-bs-target="#viewMemberModal">',
        '<i class="fa-regular fa-square-caret-down"></i>',
        '</a>'
      ].join('');
    }
};







  /*data.forEach(d=>{
       data_creazione = moment(d.data_creazione).format('DD/MM/YYYY HH24:MI')
       });*/
    
    function dateFormatter(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    };

</script>


</div>	



</div>

<?php

// se non ho selezionato la UT

} else {
  //echo 'Sono qua';
  //echo $_SESSION['username'];
 

//echo "<br>". $query_ut;
  ?>
<form class="row" name="open_ut" method="post" id="open_ut" autocomplete="off" action="<?php echo basename($_SERVER['PHP_SELF']);?>" >
<div class="form-group col-lg-4">
<select class="selectpicker show-tick form-control" 
data-live-search="true" name="ut0" id="ut0" onchange="utScelta(this.value);" required="">

  <option name="ut0" value="0">Seleziona una UT</option>


<?php            







$result1 = pg_execute($conn, "query_ut", array($_SESSION['username']));
if (pg_last_error($conn)){
  echo pg_last_error($conn);
  //$res_ok=$res_ok+1;
}


while($r1 = pg_fetch_assoc($result1)) { 
?>    
      <option name="ut0" value="<?php echo $r1['id_ut'];?>" ><?php echo $r1['descrizione']?></option>
<?php 
}
pg_free_result($result1); 
?>

</select>  
</div>
</form>

<?php
  
}
?>

</div>




<?php 
require_once('req_bottom.php');
require('./footer.php');
?>





<script type="text/javascript">
 var today = new Date();
 var week_before=new Date();
 week_before.setDate(week_before.getDate() - <?php echo intval($giorni_indietro);?>);
$('#js-date3').datepicker({
      format: 'dd/mm/yyyy',
      todayBtn: "linked", // in conflitto con startDate
      endDate:today,
      startDate:week_before,
      language:'it', 
      autoclose: true
  });




// Funzione di aggiornamento pulsanti
function aggiornaBottoniNavigazione() {
  console.log('Sono dentro la funzione aggiornaBottoniNavigazione');
  var current = $('#js-date3').datepicker('getDate');
  var today = new Date();


  var week_before=new Date();
  week_before.setDate(week_before.getDate() - <?php echo intval($giorni_indietro);?>);
  // azzera ore per confronto solo giorno/mese/anno
  current.setHours(0, 0, 0, 0);
  today.setHours(0, 0, 0, 0);
  week_before.setHours(0, 0, 0, 0);


  console.log(current.getTime());
  console.log(today.getTime());
  console.log(week_before.getTime());
  

  // Disabilita next se la data corrente è oggi
  if (current.getTime() === today.getTime()) {
    $('#nextDay').prop('disabled', true);
  } else {
    $('#nextDay').prop('disabled', false);
  }


// Disabilita prevDay se la data corrente è oggi
  if (current.getTime() === (week_before.getTime())) {
    $('#prevDay').prop('disabled', true);
  } else {
    $('#prevDay').prop('disabled', false);
  }
}




// Al cambio data (manuale o da datepicker)
$('#js-date3').on('changeDate change', function () {
});



// Pulsante giorno precedente
$('#prevDay').on('click', function() {
  var current = $('#js-date3').datepicker('getDate');
  if (current) {
    current.setDate(current.getDate() - 1);
    $('#js-date3').datepicker('setDate', current).trigger('change');
  }
});

// Pulsante giorno successivo
$('#nextDay').on('click', function() {
  var current = $('#js-date3').datepicker('getDate');
  if (current) {
    current.setDate(current.getDate() + 1);
    $('#js-date3').datepicker('setDate', current).trigger('change');
  }
});

// Inizializza lo stato dei pulsanti al primo caricamento
aggiornaBottoniNavigazione();

  //$('#myIframe').attr('src', "https://expo.wingsoft.it/amiu/webapp/indexdesk.php?operatore=0170"); 
 

</script>


</body>

</html>