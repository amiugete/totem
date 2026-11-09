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

    <title>Backoffice registrazioni carburante</title>
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
$string_uos="";


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




$result1 = pg_execute($conn, "query_ut", array($_SESSION['username']));
if (pg_last_error($conn)){
  echo pg_last_error($conn);
  //$res_ok=$res_ok+1;
}


while($r1 = pg_fetch_assoc($result1)) { 
  if (strlen($string_uos) > 0) {
    $string_uos .= ', ';
  }
  $string_uos .= $r1["id_uo"];
  
}
pg_free_result($result1); 

//echo 'String_uos:'. $string_uos;  



$today = new DateTime('now');
$timezone = new DateTimeZone('Europe/Rome');
$today->setTimezone($timezone);
$primo_giorno_mese_precedente = (clone $today)->modify('first day of last month');
?>




<style>
.vl {
  border-left: 6px solid green;
  height: 72px;
}
</style>



<form  class="row row-cols-lg-auto g-3 align-items-center" name="form_filtro" id="form_filtro" autocomplete="off">

<div class="form-group col-lg-3">
    
      <label for="data_percorsi" class="form-label">Data verifica</label>
      <div class="input-group">
      <button type="button" class="btn btn-outline-secondary" id="prevDay" title="Giorno precedente">
        <i class="fa-solid fa-chevron-left"></i>
      </button>
      <input type="text" class="form-control" id="js-date3" name="data_percorsi" onchange="cambiata_data(this.value);" value="<?php echo $primo_giorno_mese_precedente->format('d/m/Y');?>" required>
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
    var uos="<?php echo $string_uos?>";
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
      url: "./tables/report_totem_carburante.php?uos="+uos+"&d="+data_percorsi
    }); 

  });
};



</script>



<hr>

<div id="tabella">
            
        <h4> <i class="fa-solid fa-gas-pump"></i> 
        Report registrazioni carburante UT e Rimesse
      
      <button type="button" class="btn btn-sm" data-bs-container="body" 
        data-bs-toggle="popover" data-bs-placement="right">
        <span style="font-size: 1em; color: #134695;">
          <i class="fa-solid fa-circle-question"></i>
        </span>
      </button>
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
    
      <div id="toolbar"> 


     
      <!--a target="_new" class="btn btn-primary btn-sm"
         href="./export_consuntivazione_ekovision.php"><i class="fa-solid fa-file-excel"></i> Esporta xlsx completo</a-->
      </div>
				<table  id="totem_percorsi_carburante" class="table-hover table-sm" 
        data-show-columns="true"
        data-group-by="false"
        data-search="false"
        data-show-search-clear-button="false"   
        data-show-export="false" 
        data-export-type=['json', 'xml', 'csv', 'txt', 'sql', 'pdf', 'excel',  'doc'] 
				 data-click-to-select="true" data-show-print="false"  
        data-virtual-scroll="false"
        data-show-pagination-switch="true"
				data-pagination="true" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-side-pagination="false" 
        data-search-on-enter-key="true"  
        data-show-refresh="true" data-show-toggle="true"
        data-show-columns="true"
				data-filter-control="true"
        data-sort-select-options = "true"
        data-filter-control-multiple-search="false" 
        data-export-data-type="all"
        data-query-params="queryParams"
        data-url="./tables/report_totem_carburante.php?uos=<?php echo $string_uos?>&d=<?php echo $primo_giorno_mese_precedente->format('d/m/Y');?>&c=none" 
        data-toolbar="#toolbar" 
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-field="state" data-checkbox="true"></th-->  
        <th data-field="origine" data-sortable="true" data-visible="true" 
        data-formatter="origine_formatter"  
        data-filter-strict-search="true" data-search-formatter="false" data-filter-data="var:opzioni1"
         data-filter-control="select">Origine<br>dato</th> 
        <th data-field="uo_totem" data-sortable="true" data-visible="true" data-filter-control="select">UO registrazione</th> 
        <th data-field="sede_mezzo_infopm" data-sortable="true" data-visible="true" data-filter-control="select">Sede mezzo</th> 
        <th data-field="sportello" data-sortable="true" data-visible="true" data-filter-control="input">Sportello</th>  
        <th data-field="targa" data-sortable="true" data-visible="true" data-filter-control="input">Targa</th>    
        <th data-field="descrizione" data-sortable="true" data-visible="false" data-filter-control="input">Desc mezzo</th>
        <th data-field="tipo_carburante" data-sortable="true" data-visible="true" data-filter-control="input">Tipo<br>carburante</th>   
        <th data-field="datalav" data-sortable="true"  data-formatter="dateFormat" data-visible="true" data-filter-control="input">Data<br>rifornimento</th>
        <th data-field="litri" data-sortable="true" data-visible="true" data-filter-control="input">Litri</th>        
        <th data-field="importo" data-sortable="true" data-visible="true" data-filter-control="input">Importo</th>  
        <th data-field="chilometri" data-sortable="true" data-visible="true" data-filter-control="input">Km</th>       
        <th data-field="datainsert" data-sortable="true"  data-formatter="dateFormat2" data-visible="true" data-filter-control="input">Data<br>ora</th>
        <th data-field="nominativo" data-sortable="true" data-visible="true" data-filter-control="input">Nominativo</th>
    </tr>
</thead>
</table>







<script type="text/javascript">



const $table = $('#totem_percorsi_carburante');


$table.on('post-body.bs.table', function () {
    if ($('#export-btn-filtered').length === 0) {
      $('.fixed-table-toolbar .columns')
        .append('<button id="export-btn-filtered" class="btn btn-secondary ms-2" title="Esporta file Excel"><i class="bi bi-download"></i> Esporta tabella</button>');
    }
  });



$(function() {
    $table.bootstrapTable({
      stickyHeader: true,
      stickyHeaderOffsetLeft: 0,
      stickyHeaderOffsetRight: 0
  });
  });
  

  function queryParams(params) {
    var options = $table.bootstrapTable('getOptions')
    if (!options.pagination) {
      params.limit = options.totalRows
    }
    return params
  };





var opzioni1 = ['AMIU Volpara', 'AMIU Scarpino', 'Esterni'];




function origine_formatter (value, row, index) {
  //console.log('Valore in_previsione:', row.in_previsione);
  if (value =='Esterni'){
    return '<span style="font-size: 1em; color: gold;"> <i title="Distributore esterno, registrato tramite totem" class="fa-solid fa-gas-pump"></i></span>';
  } else if (value =='AMIU Volpara') {
    return '<span style="font-size: 1em; color: #006426;"> <i title="AMIU '+row.pompa+'" class="fa-solid fa-gauge-high"></i></span>';
  } else if (value =='AMIU Scarpino') {
    return '<span style="font-size: 1em; color: #134695;"> <i title="AMIU '+row.pompa+'" class="fa-solid fa-gauge-high"></i></span>';
  } else {
    return '-';
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









  /*data.forEach(d=>{
       data_creazione = moment(d.data_creazione).format('DD/MM/YYYY HH24:MI')
       });*/
    
    function dateFormatter(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    };



/* Funzione per gestire export*/
$(function() {
  console.log($("#js-date3").val());
  initTableExport({
    tableId: "totem_percorsi_carburante",
    exportAllBtn: "#export-btn",
    exportFilteredBtn: "#export-btn-filtered",
    baseUrl: "./tables/report_totem_carburante.php",
    
    fileName: "Rifornimenti_carburante", //  di default verrà aggiunta la data e l'ora di esportazione e l'estensione,
    sheetName: "Rifornimenti",
   
    extraParams: () => {
      // parametri extra della pagina
      //const range = $('input[name="daterange"]').val().split(" - ");
      return {
        d: $("#js-date3").val() == "" ? "" : $("#js-date3").val(),
        uos: <?php echo json_encode($string_uos); ?>
      };
    }
  });
});


</script>


</div>	



</div>

<?php

?>

</div>




<?php 
require_once('req_bottom.php');
require('./footer.php');
?>



<script type="text/javascript">
  var popoverContent = `La pagina mostra i rifornimenti 
  <ul>
    <li> registrati sugli impianti AMIU siti in Scarpino e Volpara sui mezzi di proprietà delle tue UT</li>
    <li> registrati tramite <b>Totem Consuntivazione</b></li>
      <ul> 
      <li> sui mezzi di proprietà delle tue UT</li>
      <li> registrate sui totem della tua UT (mezzi "in prestito")</li>
      </ul>
  </ul>
  
     
    <br>`;


  var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
  var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
    return new bootstrap.Popover(popoverTriggerEl, {
      html: true,
      content: popoverContent
    })
  })

</script>


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
  //current.setHours(0, 0, 0, 0);
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