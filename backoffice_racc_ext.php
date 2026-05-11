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


if ($_SESSION['test']==1) {
    require_once ('./conn_test.php');
} else {
    require_once ('./conn.php');
}





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
      url: "./tables/report_totem_percorsi_r.php?uos="+uos+"&d="+data_percorsi
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
            
        <h4>Report consuntivazione raccolta da totem - <?php echo $uos_descrizione;?></h4>

        <!-- Button trigger modal -->
<!--button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
  Consuntivazione
</button-->





<hr>
      <div class="row row-cols g-3">
      <small>Seleziona una causale per i percorsi non effettuati</small>
      <div class="col-4 col-auto text-start">
      <select id="causale_tutto_ne"  class="show-tick form-select" data-live-search="true" name="causale_tutto_ne" required="">
      <option name="causale" value="">Seleziona la causale</option>
      <?php 
      $query="select id, descrizione from raccolta.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
      $result = pg_query($conn_hub, $query);
      while($r = pg_fetch_assoc($result)) {
        ?>
			<option name="causale_tutto_ne" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>
			<?php } ?>
      </select>
      
      </div>
      </div>
      </div>
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
        data-url="./tables/report_totem_percorsi_r.php?uos=<?php echo $uos?>&d=<?php echo $today->format('d/m/Y');?>&c=none" 
        data-toolbar="#toolbar" 
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-field="state" data-checkbox="true"></th-->    
        <th data-field="descr_servizio" data-sortable="true" data-visible="true"  data-filter-control="select">Tipo<br>Servizio</th>
        <th data-field="id_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Cod<br>Percorso</th> 
        <th data-field="descr_percorso" data-sortable="true" data-visible="true" data-filter-control="input">Descrizione</th>
        <th data-field="uo_esec" data-sortable="true" data-visible="true" data-filter-control="input">UT<br>esec</th> 
        <th data-field="descr_orario"  data-sortable="true" data-visible="true" data-filter-control="select">Turno</th>
        <th data-field="stato_consuntivazione" data-sortable="true" data-visible="true" data-formatter="nameFormatterStato" 
        data-filter-strict-search="true" data-search-formatter="false" data-filter-data="var:opzioni" data-filter-control="select">Stato</th>
        <th data-field="causali_text" data-sortable="true" data-visible="true" data-filter-control="select">Causali</th>
        <th data-field="in_previsione" data-sortable="true" data-visible="true" data-filter-control="select"
        data-formatter="nameFormatterPrevisto_RACC" 
        data-filter-data="var:opzioni1" data-filter-strict-search="true" data-search-formatter="false">Previsto</th>
        <th data-field="datalav" data-sortable="true"  data-formatter="dateFormat" data-visible="true" data-filter-control="input">data</th>
        <!--th data-field="" data-sortable="true"  data-events="operateEvents" data-formatter="operateFormatter" 
         data-visible="true">Vis Consuntivato</th-->
        <th data-field="" data-sortable="false"  data-events="consEvents"  data-formatter="consFormatter2" data-visible="true">Dett</th>
        <th data-field="id_percorso1" data-sortable="false"  data-events="consEvents3" data-formatter="consFormatter3" data-visible="true"></th>
    </tr>
</thead>
</table>



<div class="modal fade" id="PercorsoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
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
  




  var opzioni = ['COMPLETATO', 'NON CONSUNTIVATO', 'NON CONSUNTIVATO NO AUTISTA', 'NON COMPLETATO', 'NON EFFETTUATO'] ;

function nameFormatterStato(value, row, index) {
  if (row.stato_consuntivazione =='COMPLETATO'){
    return '<span style="font-size: 1em; color: green;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (row.stato_consuntivazione =='NON CONSUNTIVATO' && row.in_previsione ==='PREVISTO') {
    return '<span style="font-size: 1em; color: black;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (row.stato_consuntivazione =='NON CONSUNTIVATO NO AUTISTA' && row.in_previsione ==='PREVISTO') {
    return '<span style="font-size: 1em; color: black;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i> <i class="fa-solid fa-user-xmark"></i> </span>';
  }else if (row.stato_consuntivazione =='NON CONSUNTIVATO' && row.in_previsione ==='NON PREVISTO' ) {
    return ' ';
  } else if (row.stato_consuntivazione === 'NON EFFETTUATO') {
    return '<span style="font-size: 1em; color: red;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  } else if (row.stato_consuntivazione === 'NON COMPLETATO') {
    return '<span style="font-size: 1em; color: orange;"> <i title="'+row.stato_consuntivazione+'" class="fa-solid fa-circle"></i></span>';
  }  else {
    return ' - ';
  }
};


var opzioni1 = ['PREVISTO', 'NON PREVISTO'];

function nameFormatterPrevisto_RACC(value, row, index) {
  //console.log('Valore in_previsione:', row.in_previsione);
  if (row.in_previsione =='PREVISTO'){
    return '<span style="font-size: 1em; color: green;"> <i title="'+row.in_previsione+'" class="fa-regular fa-calendar-check"></i></span>';
  } else if (row.in_previsione =='NON PREVISTO') {
    return '<span style="font-size: 1em; color: red;"> <i title="'+row.in_previsione+'" class="fa-regular fa-calendar-xmark"></i></span>';
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


/*window.operateEvents = {
    'click .info#dett': function (e, value, row, index) {
        console.log('Sono qua report dettaglio...');
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
                var modalEl = document.getElementById('PercorsoModal2');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
        //$('#viewMemberModal').modal('show');
    }
};
*/
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
        '<a class="info btn btn-warning btn-sm" id="cons" data-bs-toggle="modal" title="Visualizza dettagli cons percorso '+row.id_percorso+'" data-bs-target="#viewMemberModal">',
        '<i class="fa-regular fa-square-caret-down"></i>',
        '</a>'
      ].join('');
};




window.consEvents = {
    'click .info#cons': function (e, value, row, index) {
        console.log('Sono qua cons');
        var id = row.id_percorso;
        var datalav = moment(row.datalav).format('DD/MM/YYYY');
        console.log('id = ' +id);
        console.log('datalav = '+datalav);
        $.ajax({   
            type: "POST",
            url: "report_totem_percorsi_cons_r.php",
            data: 'id=' + id + '&datalav='+datalav +'&consuntivatore=UT'+<?php echo $uos;?>+'',
            dataType: "text",                  
            success: function(response){                    
                $(".modal-body").html(response); 
                var modalEl = document.getElementById('PercorsoModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
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





// rendi non eseguito

function consFormatter3(value, row, index) {
      if (row.stato_consuntivazione.startsWith('NON CONSUNTIVATO') && row.in_previsione == 'PREVISTO') {
        if (row.stato_consuntivazione =='NON CONSUNTIVATO' && row.uo_esec.includes(',') && (<?php echo $uos;?> === 16 || <?php echo $uos;?> === 17)) {
          <?php 
          //echo $role_SIT;
          if ($role_SIT == 'VIEW' ) {
          ?>
          return [
          '<button class="info btn btn-danger btn-sm" disabled="" id="no_autista" title="Autista non disponibile">',
          '<i class="fa-solid fa-user-slash"></i>',
          '</button>'
        ].join('');
          <?php
          } else {
          ?>
        return [
          '<button class="info btn btn-danger btn-sm" id="no_autista" title="Autista non disponibile">',
          '<i class="fa-solid fa-user-slash"></i>',
          '</button>'
        ].join('');
        <?php
          }
        ?>
        } else if (row.uo_esec.includes(',') && (<?php echo $uos;?> === 16 || <?php echo $uos;?> === 17)){
          // è il caso cin cui sia già stato rimosso l'autista e sono una rimessa
          return '';
        } else {
        <?php 
          //echo $role_SIT;
          if ($role_SIT == 'VIEW') {
          ?>
          return [
          '<button class="info btn btn-danger btn-sm" disabled="" id="non_eseguito" title="Rendi non eseguito">',
          '<i class="fa-solid fa-xmark"></i>',
          '</button>'
        ].join('');
          <?php
          } else {
          ?>
        return [
          '<button class="info btn btn-danger btn-sm" id="non_eseguito" title="Rendi non eseguito">',
          '<i class="fa-solid fa-xmark"></i>',
          '</button>'
        ].join('');
        <?php
          }
        ?>
      }
      }
};



// questa funzione è per rendere un percorso non eseguito, con inserimento causale
window.consEvents3 = {
    'click .info#non_eseguito': function (e, value, row, index) {
        console.log('Devo rendere il percorso non eseguito');
        var id = row.id_percorso;
        var datalav = moment(row.datalav).format('DD/MM/YYYY');
        console.log('id = ' +id);
        console.log('datalav = '+datalav);
        //console.log('consuntivatore = '+consuntivatore);
        var causale = $('#causale_tutto_ne').val();
        if (!causale) { // controlla se è nullo, vuoto o undefined
            alert('⚠️ Devi selezionare una causale prima di procedere con questa funzione!');
            return; // interrompe l’esecuzione dello script
        }
        console.log('causale = '+causale);


        $.ajax({   
            type: "POST",
            url: "backoffice/non_eseguito_racc.php",
            data: 'id=' + id + '&datalav='+datalav + '&causale='+causale +'&consuntivatore=UT'+<?php echo $uos;?>+'',
            dataType: "text",                  
            success: function(response){                    
                $table.bootstrapTable('refresh', {
                url: "./tables/report_totem_percorsi_r.php?uos="+<?php echo $uos;?>+"&d="+datalav
                
            });
            console.log('refresh tabella fatto');
            },
            error: function (xhr, textStatus, errorThrown, response) {
                console.error("Errore AJAX:", textStatus, errorThrown);

                $("#ConsOutput2").html(`
                  <div class="alert alert-danger alert-animated" role="alert">
                    ❌ Errore durante l'elaborazione della richiesta.<br>
                    <b>Dettagli:</b> ${errorThrown ||'Errore sconosciuto'}<br>
                    ${response}<br>
                    Verifica la connessione o riprova più tardi.
                  </div>
                `).fadeIn("slow");
            }

        });
       
    }
};


// questa funzione è per inserire causale su operatore
window.consEvents3 = {
    'click .info#no_autista': function (e, value, row, index) {
        console.log('Devo aggiungere causale su autista');
        var id = row.id_percorso;
        var datalav = moment(row.datalav).format('DD/MM/YYYY');
        console.log('id = ' +id);
        console.log('datalav = '+datalav);
        //console.log('consuntivatore = '+consuntivatore);
        


        $.ajax({   
            type: "POST",
            url: "backoffice/no_autista.php",
            data: 'id=' + id + '&datalav='+datalav + '&consuntivatore=UT'+<?php echo $uos;?>+'',
            dataType: "text",                  
            success: function(response){                    
                $table.bootstrapTable('refresh', {
                url: "./tables/report_totem_percorsi_r.php?uos="+<?php echo $uos;?>+"&d="+datalav
                
            });
            console.log('refresh tabella fatto');
            },
            error: function (xhr, textStatus, errorThrown, response) {
                console.error("Errore AJAX:", textStatus, errorThrown);

                $("#ConsOutput2").html(`
                  <div class="alert alert-danger alert-animated" role="alert">
                    ❌ Errore durante l'elaborazione della richiesta.<br>
                    <b>Dettagli:</b> ${errorThrown ||'Errore sconosciuto'}<br>
                    ${response}<br>
                    Verifica la connessione o riprova più tardi.
                  </div>
                `).fadeIn("slow");
            }

        });
       
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



<!-- Script -->
<script type="text/javascript">
  const myModalEl = document.getElementById('PercorsoModal');

  myModalEl.addEventListener('hidden.bs.modal', function () {
    // Funzione da eseguire alla chiusura del modal
    console.log("Modal chiuso");
    // Qui puoi chiamare qualsiasi altra funzione
    var data_percorsi=$('#js-date3').val();

    console.log(data_percorsi);
    //console.log($table);
    $table.bootstrapTable('refresh', {
      url: "./tables/report_totem_percorsi_r.php?uos="+<?php echo $uos;?>+"&d="+data_percorsi
    });   
    console.log('refresh tabella fatto');
});

  
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