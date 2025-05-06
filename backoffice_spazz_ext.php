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


# filtro sulle UT
$filter_totem="OK";


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


<div class="container">
<?php 

$uos=170;

$today = new DateTime('now');
$timezone = new DateTimeZone('Europe/Rome');
$today->setTimezone($timezone);
$hour = $today->format('Hi');
if ($hour < '0820'){
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
    <input type="hidden" class="form-control" id="uos" name="uos" value="11" required>




<div class="form-group col-lg-3">
    <label for="data_percorsi" class="form-label">Data verifica</label>
    <input type="text" class="form-control" id="js-date3" name="data_percorsi" onchange="cambiata_data(this.value);" value="<?php echo $today->format('d/m/Y');?>" required>
    <!--input type="text" class="form-control" id="js-date3" name="data_percorsi" value="<?php echo $today->format('d/m/Y');?>" required-->
    <div id="nota_data" class="form-text"><?php echo $nota_data;?></div>
</div>
<!--div class="form-group col-lg-2">
<button type="submit" class="btn btn-primary">Filtra</button>
</div-->
</form>

<script>
  function cambiata_data(val) {
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
      url: "./tables/report_totem_percorsi_s.php?uos="+uos+"&d="+data_percorsi
    }); 

  });
};

/*$(document).ready(function () {                 
  $('#form_filtro').submit(function (event) { 
      console.log("Bottone filtro cliccato");
      var data_percorsi=$('#js-date3').val();
      console.log(data_percorsi);
      var uos="<?php echo $uos?>";
      console.log(uos);
      if ($('#filtro_percorsi').is(":checked"))
      {
        var filtro_percorsi = 'all';
      } else {
        var filtro_percorsi = 'none';
      }
      console.log(filtro_percorsi);
      $("#nota_data").html("").fadeIn("slow");
      //$(function() {    // Faccio refres della data-url
        $table.bootstrapTable('refresh', {
          url: "./tables/report_totem_piazzola.php?uos="+uos+"&d="+data_percorsi+"&c="+filtro_percorsi
        }); 
      return false;
      //});
  });
});*/

</script>


<hr>

<div id="tabella">
            
        <h4>Report consuntivazione spazzamento da totem </h4>

        <!-- Button trigger modal -->
<!--button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Consuntivazione
</button-->





        <div class="table-responsive-sm">

                  <!--div id="toolbar">
        <button id="showSelectedRows" class="btn btn-primary" type="button">Crea ordine di lavoro</button>
      </div-->
    
      <div id="toolbar" class="isDisabled"> 
      <!--a target="_new" class="btn btn-primary btn-sm"
         href="./export_consuntivazione_ekovision.php"><i class="fa-solid fa-file-excel"></i> Esporta xlsx completo</a-->
      </div>
				<table  id="totem_percorsi" class="table-hover" 
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
        data-url="./tables/report_totem_percorsi_s.php?uos=<?php echo $uos?>&d=<?php echo $today->format('d/m/Y');?>&c=none" 
        data-toolbar="#toolbar" 
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-checkbox="true" data-field="id"></th-->  
        <!--th data-field="state" data-checkbox="true" ></th-->  
        <th data-field="descr_servizio" data-sortable="true" data-visible="true"  data-filter-control="select">Tipo<br>Servizio</th>
        <th data-field="id_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Cod<br>Percorso</th> 
        <th data-field="descr_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Descrizione</th>
        <th data-field="uo_esec" data-sortable="true" data-visible="true" data-filter-control="input">UT<br>esec</th> 
        <th data-field="descr_orario"  data-sortable="true" data-visible="true" data-filter-control="select">Turno</th>
        <th data-field="stato_consuntivazione" data-sortable="true" data-visible="true" data-formatter="nameFormatterStato" 
        data-filter-strict-search="true" data-search-formatter="false" data-filter-data="var:opzioni" data-filter-control="select">Stato</th>
        <th data-field="causali_text" data-sortable="true" data-visible="true" data-filter-control="select">Causali</th>
        <th data-field="in_previsione" data-sortable="true" data-visible="true" data-filter-control="select"
        data-formatter="nameFormatterPrevisto" data-filter-data="var:opzioni1" data-filter-strict-search="true" data-search-formatter="false">Previsto</th>
        <th data-field="datalav" data-sortable="true"  data-formatter="dateFormat" data-visible="true" data-filter-control="input">data</th>
        <!--th data-field="" data-sortable="true"  data-events="operateEvents" data-formatter="operateFormatter" 
         data-visible="true">Vis Consuntivato</th-->
        <th data-field="" data-sortable="true"  data-events="consEvents"  data-formatter="consFormatter2" 
        data-visible="true">Consuntivazione</th>
    </tr>
</thead>
</table>



<div class="modal fade" id="viewMemberModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-scrollable modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <!--h5 class="modal-title" id="exampleModalLabel">Titolo</h5-->
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-body">
                <!-- output data here-->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
      </div>
    </div>
  </div>
</div>





<script type="text/javascript">



var $table = $('#totem_percorsi');

$(function() {
    $table.bootstrapTable();
  });
  




  var opzioni = ['COMPLETATO', 'NON CONSUNTIVATO', 'NON COMPLETATO', 'NON EFFETTUATO'] ;

function nameFormatterStato(value, row, index) {
  if (value =='COMPLETATO'){
    return '<span style="font-size: 1em; color: green;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (value =='NON CONSUNTIVATO') {
    return '<span style="font-size: 1em; color: black;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (value =='NON EFFETTUATO') {
    return '<span style="font-size: 1em; color: red;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (value =='NON COMPLETATO') {
    return '<span style="font-size: 1em; color: orange;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  }  
};


var opzioni1 = ['PREVISTO', 'NON PREVISTO'];

function nameFormatterPrevisto(value, row, index) {
  if (value =='PREVISTO'){
    return '<span style="font-size: 1em; color: green;"> <i title="'+value+'" class="fa-regular fa-calendar-check"></i></span>';
  } else if (value =='NON PREVISTO') {
    return ' ';
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


window.operateEvents = {
    'click .info#dett': function (e, value, row, index) {
        console.log('Sono qua');
        var id = row.id_percorso;
        var datalav = row.datalav;
        console.log('id = ' +id);
        console.log('datalav = '+datalav);

        $.ajax({   
            type: "GET",
            url: "report_totem_percorsi_dettaglio_s.php",
            data: 'id=' + id + '&datalav='+datalav,
            dataType: "text",                  
            success: function(response){                    
                $(".modal-body").html(response); 
            }
        });
        $('#viewMemberModal').modal('show');
    }
};

// da backoffice
//https://expo.wingsoft.it/amiu/webapp/indexspazzasuser.php



/*                       
<input type="hidden" name="operatore" value="9148">
<input type="hidden" name="consuntivatore" value="UT11"> 
<input type="hidden" name="codperc" value="0201240201">
<input type="hidden" name="servizio1" value="Spazzamento Manuale">
<input type="hidden" name="zonaivo" value="Zona N 02 Manuale Modena Settembrini">
<input type="hidden" name="zona1" value="UT SAMPIERDARENA">
<input type="hidden" name="data1" value="2025-04-29">
<input type="hidden" name="admin1" value="1958">    
<input type="submit" style="background-color:red;color:white;" value="Vedi percorso completo e modifica ">
*/                     


function consFormatter1(value, row, index) {
      return [
        '<form class="row" target="_blank" method="POST" autocomplete="off" action="https://expo.wingsoft.it/amiu/webapp/indexspazzasuser.php">',
        '<!--cablato operatore pianar da sistemare meglio-->',
        '<input type="hidden" name="operatore" value="9148">',
        '<!--cablato -->',
        '<input type="hidden" name="consuntivatore" value="UT11"> ',
        '<!--cablato -->',
        '<input type="hidden" name="codperc" value="'+row.id_percorso+'">',
        '<input type="hidden" name="servizio1" value="'+row.descr_servizio+'">',
        '<input type="hidden" name="zonaivo" value="'+row.descr_percorso+'">',
        '<input type="hidden" name="zona1" value="'+row.uo_esec+'">',
        '<input type="hidden" name="data1" value="'+moment(row.datalav).format('YYYY-MM-DD')+'">',
        '<input type="hidden" name="admin1" value="1958">',
        '<button type="submit" id="sbtn" class="btn btn-warning btn-sm"><i class="fa-solid fa-file-excel"></i></button>',
        '</form>'
      ].join('');
};

// da totem
//https://expo.wingsoft.it/amiu/webapp/indexspazzapoi.php

function consFormatter0(value, row, index) {
      return [
        '<form class="row"  method="POST" autocomplete="off" action="https://expo.wingsoft.it/amiu/webapp/indexspazzapoi.php">',
        '<!--cablato operatore pianar da sistemare meglio-->',
        '<input type="hidden" id="operatore" name="operatore" value="9148">',
        '<input type="hidden" id="zona1" name="zona1" value="">',
        '<input type="hidden" id="verifica" name="verifica" value="ok">',
        '<input type="hidden" id="causalevel1" name="causalevel1" value="COMPLETATO">',
        '<input type="hidden" id="causaperc1" name="causaperc1" value="100">',
        '<input type="hidden" id="tutto" name="tutto" value="tutto">',
        '<!--cablato -->',
        '<input type="hidden" id="consuntivatore" name="consuntivatore" value="UT11">',
        '<!--cablato -->',
        '<input type="hidden" id="admin1" name="admin1" value="1958">',
        '<input type="hidden" id="data1" name="data1" value="'+moment(row.datalav).format('YYYY-MM-DD')+'">',
        '<input type="hidden" id="codperc" name="codperc" value="'+row.id_percorso+'|'+row.descr_percorso+'">',
        '<input type="hidden" id="servizio1" name="servizio1" value="'+row.descr_servizio+'">',
        '<!--input type="hidden" id="zonaivo" name="zonaivo" value="'+row.descr_percorso+'"-->',
        
        '<input type="hidden" id="codzona" name="codzona" value="'+row.uo_esec+'">',
        '<input type="hidden" id="fidel" name="fidel" value="'+row.uo_esec+'">',
        '<button type="submit" id="sbtn" class="btn btn-warning btn-sm"><i class="fa-solid fa-file-excel"></i></button>',
        '</form>'
      ].join('');
};


function consFormatter2(value, row, index) {
      return [
        '<a class="info btn btn-warning btn-sm" id="cons" data-bs-toggle="modal" data-bs-target="#viewMemberModal">',
        '<i class="fa-regular fa-square-caret-down"></i>',
        '</a>'
      ].join('');
};



window.consEvents = {
    'click .info#cons': function (e, value, row, index) {
        console.log('Sono qua cons');
        var id = row.id_percorso;
        var datalav = row.datalav;
        console.log('id = ' +id);
        console.log('datalav = '+datalav);
        $.ajax({   
            type: "GET",
            url: "report_totem_percorsi_cons_s.php",
            data: 'id=' + id + '&datalav='+datalav,
            dataType: "text",                  
            success: function(response){                    
                $(".modal-body").html(response); 
            }
        });
        $('#viewMemberModal').modal('show');
    }
};




// questa era una prova per collegarsi a Gava
/*
window.consEvents = {
    'click .info#cons': function (e, value, row, index) {
        console.log('Cliccato cons');
        var codperc = row.id_percorso;
        var servizio1=row.descr_servizio;
        var zonaivo = row.descr_percorso; 
        var data1 = moment(row.datalav).format('YYYY-MM-DD');
        var zona1 = row.uo_esec; 
        console.log(codperc);
        console.log(servizio1);
        console.log(zonaivo);
        console.log(data1);
        console.log(zona1);
        var operatore = '6409';
        var consuntivatore = 'UT170';
        var admin1 = '1958';
        $.ajax({   
            type: "POST",
            url: "<?php echo $wingsoft_url?>indexspazzasuser.php",
            data: 'operatore=' + operatore + 'servizio1=' + servizio1 + 'zonaivo=' + zonaivo +'operatore=' + zona1 +'zona1=' + consuntivatore + 'codperc=' + codperc + '&data1='+data1,
            dataType: "text", 
            crossDomain: true,
            xhrFields: {
                withCredentials: true
            },
            success: function(response){                    
                $(".modal-body").html(response); 
            }
        });
        $('#viewConsModal').modal('show')
    }
};
*/

  /*data.forEach(d=>{
       data_creazione = moment(d.data_creazione).format('DD/MM/YYYY HH24:MI')
    });*/
    
    function dateFormatter(value) {
      return moment(value).format('DD/MM/YYYY HH:mm')
    };

</script>


</div>	



</div>


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