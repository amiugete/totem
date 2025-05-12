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

//the_page_title();


require_once ('./conn.php');






?> 




</head>

<body>



<div class="container">
<?php 

//require_once("select_ut.php");

$id = $_POST['id'];
$datalav= $_POST['datalav'];
$consuntivatore= $_POST['consuntivatore'];

$id_uo=str_replace('UT', '', $consuntivatore);

?>




      

     




<div id="tabella1">
            
        <h4>Dettaglio percorso <?php echo $id;?> del <?php echo $datalav;?></h4>


        <script type="text/javascript">
        
        $(document).ready(function () {                 
                $('#salva_cons').click(function (event) { 
                  console.log("Bottone salva cliccato");
                  event.preventDefault(); 
                  var datalav=$('#datalav').val();
                  console.log(datalav);
                  
                  var consuntivatore=$('#consuntivatore').val();
                  console.log(consuntivatore);
                  
                  var selectedRows = getRowSelections();
                  console.log(selectedRows);
                  var selectedItems = '';

                  $.each(selectedRows, function(index, value) {
                    selectedItems += selectedRows[index] + ',';
                  });
                  console.log(selectedItems);
                  
                  $.ajax({ 
                    url: 'backoffice/cons_tappe_spazzamento.php', 
                    method: 'POST', 
                    data: {'cons_tappe':selectedItems, 'datalav':datalav, 'consuntivatore':consuntivatore }, 
                    //processData: true, 
                    //contentType: false, 
                    success: function (response) {                       
                        //alert('Your form has been sent successfully.'); 
                        console.log(response);
                          $("#ConsOutput").html(response).fadeIn("slow");
                          /*setTimeout(function(){// wait for 5 secs(2)
                            location.reload(); // then reload the page.(3)
                        }, 1000);*/
                        //return false;
                                  
                    }, 
                    error: function (jqXHR, textStatus, errorThrown) {                        
                        alert('Your form was not sent successfully.'); 
                        console.error(errorThrown); 
                    } 
                  });
                  console.log('provo refresh pagina');
                  $(function() {    // Faccio refresh della data-url
                    $table1.bootstrapTable('refresh', {
                      url: "./tables/report_totem_percorsi_cons_s.php?id=<?php echo $id;?>&datalav=<?php echo $datalav;?>&id_uo=<?php echo $id_uo;?>"
                    }); 
                  });
                  return false;
                  console.log('Fatto refresh pagina');
                });
              });



        



      </script>


      <hr>
      <div class="row row-cols g-3">
      <div class="col-4 col-auto text-start">
      <select id="causale_tutto"  class="show-tick form-select" data-live-search="true" name="causale" required="">
      <option name="causale" value="">Seleziona la causale</option>
      <?php 
      $query="select id, descrizione from spazzamento.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
      $result = pg_query($conn_hub, $query);
      while($r = pg_fetch_assoc($result)) {
        ?>
			<option name="causale" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>
			<?php } ?>
      </select>
      <small>Seleziona causale e % da applicare su tratti selezionati</small>
      </div>
      <div class="col-2 text-start">
      <select id="punteggio_tutto" class="show-tick form-select" data-live-search="true"  name="punteggio" required="">
      <option name="punteggio" value="">% completamento</option>
      <option name="punteggio" value="100">100</option>
      <option name="punteggio" value="75">75</option>
      <option name="punteggio" value="50">50</option>
      <option name="punteggio" value="25">25</option>
      <option name="punteggio" value="50">0</option>
      </select>
    
      
      </div>
      <div class="col-3 text-start">
      <button onclick="updateAll()" class="btn btn-warning">
      <i class="fa-solid fa-list-check"></i> Applica
      </button>
      </div>

      <div class="col-3 text-end">
      <form autocomplete="off" id="prospects_form" action="">
      <input type="hidden" class="form-control" id="datalav" name="datalav" value="<?php echo $datalav;?>">
      <input type="hidden" id="consuntivatore" name="consuntivatore" value="<?php echo $consuntivatore;?>">
      <div name="conferma2" id="conferma2" class="form-group">
      <button type="submit" id="salva_cons" class="btn btn-primary">
      <i class="fa-solid fa-plus"></i> Salva consuntivazione
      </button>
      </div>
      </form>
      </div>
      </div>





      <!-- SPAZIO DEDICATO ALL'OUTPUT -->
      <div id="ConsOutput" class="text-center">

      </div>


        <div class="table-responsive-sm">

                  <!--div id="toolbar">
        <button id="showSelectedRows" class="btn btn-primary" type="button">Crea ordine di lavoro</button>
      </div-->
    
      <div id="toolbar1" class="isDisabled"> 
      <!--a tarGET="_new" class="btn btn-primary btn-sm"
         href="./export_consuntivazione_ekovision.php"><i class="fa-solid fa-file-excel"></i> Esporta xlsx completo</a-->
      </div>
				<table  id="totem_percorsi_dettaglio_s" class="table-hover table-sm" 
        idfield="tappa" 
        data-show-search-clear-button="false"   
        data-show-export="false" 
				data-search="false" data-show-print="false"  
        data-virtual-scroll="false"
        data-show-pagination-switch="false"
				data-pagination="false" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-side-pagination="false" 
        data-show-refresh="false" data-show-toggle="false"
        data-show-columns="false"
				data-filter-control="true"
        data-sort-select-options = "true"
        data-url="./tables/report_totem_percorsi_cons_s.php?id=<?php echo $id;?>&datalav=<?php echo $datalav;?>&id_uo=<?php echo $id_uo;?>" 
        data-toolbar="#toolbar1" 
        data-show-toolbar="false"
        data-show-footer="false"
        data-row-style="rowStyle"
        >
        
        
<thead>



 	  <tr>
        <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>  
        <th data-field="tappa" data-sortable="true" data-visible="false" data-filter-control="input">Tappa</th>
        <th data-field="nome_via" data-sortable="true" data-visible="true" data-filter-control="input">Via</th>
        <th data-field="tratto" data-sortable="true" data-visible="true" data-filter-control="input">Nota via</th>
        <th data-field="check_previsto" data-sortable="true" data-visible="true">Previsto</th>
        <!--th data-field="check_prev_cons" data-sortable="true" data-visible="false">Previsto</th-->
        <th data-field="causale" data-sortable="true" data-visible="true" data-formatter="causaleForm">Causale</th>
        <th data-field="punteggio" data-sortable="true" data-visible="true" data-formatter="punteggioForm">Punteggio</th>
        <th data-field="" data-sortable="true" data-visible="true" data-formatter="consStato">Stato<br>consuntivazione</th>
        <!--th data-field="punteggio" data-sortable="true" data-visible="true" data-filter-control="select">% completamento</th> 
        <th data-field="causale" data-sortable="true" data-visible="true" data-filter-control="select">Causale</th> 
        <th data-field="operatore" data-sortable="true" data-visible="true" data-filter-control="select">Operatore</th-->
    </tr>
</thead>
</table>





<script type="text/javascript">



var $table1 = $('#totem_percorsi_dettaglio_s');

$(function() {
    $table1.bootstrapTable();
});


$table1.on('check.bs.table', function (e, row) {
  console.log('Tappa '+ row.tappa+ ' selezionata');
  //console.log(e);
  $('#punteggio_'+row.tappa+'').removeAttr('disabled');
  $('#insert_'+row.tappa+'').removeAttr('disabled');
  
});

$table1.on('uncheck.bs.table', function (e, row) {
  console.log('Tappa: '+ row.tappa+ ' rimossa');
  //console.log(e);
  //$('#insert_'+row.tappa+' option:selected').find($('option')).
  //$('#insert_'+row.tappa+'').find($('option')).attr('selected',false);
  $('#insert_'+row.tappa+' option:selected').prop("selected", false)
  $('#insert_'+row.tappa+'').attr('disabled',true);
  $('#punteggio_'+row.tappa+' option[value=0]').prop("selected", true);
  $('#punteggio_'+row.tappa+'').attr('disabled',true);
});



//dopo aver caricato la tabella chiamo questa funzione
$table1.on('post-body.bs.table', function (e, row) {
//$table1.on('load-success.bs.table', function (e, row) {
  //console.log('caricata la tabella');
  select_causale();
});




function updateAll() {
    var causale = $('select#causale_tutto').find(":selected").val();  
    console.log(causale);
    //controlli su causali e punteggio
    if (!causale){
      $("#ConsOutput").html('<div class="alert alert-danger" role="alert">Selezionare una causale</div>').fadeIn("slow");
      return;
    } else if (causale === '100'){
      console.log('Causale 100');
      $('#punteggio_tutto option[value=100]').prop("selected", true);
    } 
    var punteggio = $('select#punteggio_tutto').find(":selected").val();
    
    if (!punteggio){
      $("#ConsOutput").html('<div class="alert alert-danger" role="alert">Selezionare una % di completamento</div>').fadeIn("slow");
      return;
    } else if (causale != '100' && punteggio==='100'){
      $("#ConsOutput").html('<div class="alert alert-danger" role="alert">Causale e punteggio non compatibili</div>').fadeIn("slow");
      return;
    }

    // messaggio OK
    var messaggio= '<div class="alert alert-warning" role="alert"> Le modifiche sono state applicate su tutti i tratti selezionati. <b>Ricorda di salvare per rendere effettiva la modifica.</b></div>';
    console.log(messaggio);
    $("#ConsOutput").html(messaggio).fadeIn("slow");
    return $.map($table1.bootstrapTable('getSelections'), 
    function(row, index) {
        $('#insert_'+row.tappa+' option[value='+causale+']').prop("selected", true);
        $('#punteggio_'+row.tappa+' option[value='+punteggio+']').prop("selected", true);
    });
    
  
  };



function getRowSelections() {
    return $.map($table1.bootstrapTable('getSelections'), 
    function(row, index) {
      //console.log(row.tappa);
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      //console.log(causale);
      var punteggio = $('select#punteggio_'+row.tappa+'').find(":selected").val();
      //console.log(punteggio);
      /*if(!causale){
        alert('Specificare una causale');
      };*/
      return row.tappa+'-'+causale+'-'+punteggio;
    })
  };



function select_causale() {
  //console.log('Chiamo la funzione select_causale');
  return $.map($table1.bootstrapTable('getSelections'), 
    function(row, index) {
      // tolgo i
      if (row.id_causale) {
        $('#insert_'+row.tappa+'').attr('disabled',false);
        $('#punteggio_'+row.tappa+'').attr('disabled',false);
      }
        $('#insert_'+row.tappa+' option[value='+row.id_causale+']').prop("selected", true);
        $('#punteggio_'+row.tappa+' option[value='+row.punteggio+']').prop("selected", true);
    })
};


function update_p() {
  return $.map($table1.bootstrapTable('getSelections'), 
    function(row, index) {
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      console.log(causale);
      if (causale==='100'){
        $('#punteggio_'+row.tappa+' option:selected').prop("selected", false);
        $('#punteggio_'+row.tappa+'').attr('disabled',true);
      } else {
         $('#punteggio_'+row.tappa+' option[value=0]').prop("selected", true);
      }
    })
}


function getRowSelections() {
    return $.map($table1.bootstrapTable('getSelections'), 
    function(row, index) {
      //console.log(row.tappa);
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      //console.log(causale);
      var punteggio = $('select#punteggio_'+row.tappa+'').find(":selected").val();
      //console.log(punteggio);
      /*if(!causale){
        alert('Specificare una causale');
      };*/
      return row.tappa+'-'+causale+'-'+punteggio;
    })
  };

window.stateFormatter = (value, row, index) => {
    if (row.check_prev_cons === '1') {
      return {
        checked: true
      }
    }
    //return value
  }

  
function rowStyle(row, index) {
  // previsto e fatto o da consuntivare
  if (row.check_previsto === '1' && (row.id_causale === '100' || (!row.id_causale))) {
    return {
    classes: 'text-wrap previsto fatto',
    css: {"background-color": "#008000", "font-weight": "bold"}
    } 
  //  previsto e non fatto 
  } else if (row.check_previsto === '1' && (row.id_causale != '100') ){
    return {
      classes: 'text-wrap previsto non-fatto',
      css: {"background-color": "#ffba08"}
    } 
  // non previsto e da consuntivare
  } else if (row.check_previsto === '0' && (row.id_causale === '100') ){
    return {
      classes: 'text-wrap non-previsto fatto',
      css: {"background-color": "#70e000"}
    } 
  // non previsto e da consuntivare
  } else if (row.check_previsto === '0' && (!row.id_causale) ){
    return {
      classes: 'text-wrap non-previsto',
      css: {"background-color": "pink"}
    } 
  }
  
  };



  function  consStato(value, row, index) {
  if ((!row.id_causale) ) {
    return "";
   } else {
    return "Consuntivato da "+row.codice+" il "+ moment(row.datainsert).format('DD/MM/YYYY HH:mm') +"";
   }
  };


function  punteggioForm(value, row, index) {
  if ((row.check_previsto === '1' && (!row.id_causale) ) || (row.id_causale === '100')) {
    return [
        '<select id="punteggio_'+row.tappa+'" class="show-tick form-select" data-live-search="true"  name="punteggio" required="">',
        '<option name="punteggio" value="100" selected>100</option>',
        '<option name="punteggio" value="75">75</option>',
        '<option name="punteggio" value="50">50</option>',
        '<option name="punteggio" value="25">25</option>',
        '<option name="punteggio" value="50">0</option>',
        '</select>'
        //'</form>'
      ].join(''); 
    } else {
    return [
        '<select id="punteggio_'+row.tappa+'" class="show-tick form-select" data-live-search="true" disabled="" name="punteggio" required="">',
        '<option name="punteggio" value="100">100</option>',
        '<option name="punteggio" value="75">75</option>',
        '<option name="punteggio" value="50">50</option>',
        '<option name="punteggio" value="25">25</option>',
        '<option name="punteggio" value="0" selected>0</option>',
        '</select>'//,
        //'</form>'
      ].join('');
        }

}





function causaleForm(value, row, index) {
  if ((row.check_previsto === '1' && (!row.id_causale)) || ( row.id_causale=== '100')) {
    return [
        //'<form action="" autocomplete="off" id="insert_'+row.tappa+'">',
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onclick="update_p()" name="causale" required="">',
        '<option name="causale" value="100">COMPLETATO</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
        $result = pg_query($conn_hub, $query);
        while($r = pg_fetch_assoc($result)) {
        ?>
				'<option name="causale" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>',
				<?php } ?>
        '</select>'//,
        //'</form>'
      ].join(''); 
    } else {
          return [
      //'<form action="" autocomplete="off" id="insert_'+row.tappa+'">',
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onclick="update_p()"  disabled="" name="causale" id="causale" required="">',
        '<option name="causale" value="">Seleziona una causale</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  
        where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
        $result = pg_query($conn_hub, $query);
        while($r = pg_fetch_assoc($result)) {
        ?>
            '<option name="causale" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>',
				<?php } ?>
        '</select>'//,
        //'</form>'
      ].join('');
        }
     
   
  };






</script>


</div>	










</div>
</div>

<?php
require_once('req_bottom.php');
//require('./footer.php');
?>



</body>

</html>