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

$check_modal=1;

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



<div class="container">
<?php 

//require_once("select_ut.php");

$id = $_POST['id'];
$datalav= $_POST['datalav'];
// cerco la descrizione del percos
$query_percorso = "select distinct cpsxa.desc_percorso
from spazzamento.cons_percorsi_spazz_x_app cpsxa 
where cpsxa.id_percorso = $1
and to_date($2, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine";

$result0 = pg_prepare($conn_hub, "query_percorso", $query_percorso);

if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo pg_last_error($conn_hub);
    #$res_ok= $res_ok+1;
}
//echo "Sono qua 2";
$result0 = pg_execute($conn_hub, "query_percorso", array($id, $datalav));  
if (!pg_last_error($conn_hub)){
    #$res_ok=0;
} else {
    echo  pg_last_error($conn_hub);
    #$res_ok= $res_ok+1;
}

while($r0 = pg_fetch_assoc($result0)) {
  $desc_percorso =  $r0['desc_percorso'];
}


//echo $_POST['consuntivatore'];
$consuntivatore= $_POST['consuntivatore'];

if (str_starts_with($consuntivatore, 'UT')){
  //echo 'Inizia con UT';
  $id_uo = str_replace('UT', '', $consuntivatore);
} else {
  //echo 'Non inizia con UT';
  $id_uo = $_POST['id_uo'];

  
  include('selezione_operatore.php');


while($r = pg_fetch_assoc($result)) {
  $op =  $r['op'];
}
 



}


?>




      

     




<div id="tabella1">
            
        <h4>Cod: <?php echo $id;?> - Desc: <?php echo $desc_percorso;?></h4> 
        <small>Data: <?php echo $datalav;?></small>
        <?php 
        if ($op){
          echo '<small>Operatore '.$consuntivatore . ' ('.$op.')</small>'; 
        } else{
          echo ' <small>Sono su backoffice come '.$consuntivatore.'</small> ';
          if ($_SESSION['test']==1) {
            echo ' <small>Ambiente di test</small> ';
          }
        }

        ?>
    

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
                    $table_tappe.bootstrapTable('refresh', {
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
      <small>Seleziona una causale e la % di completamento da applicare su tratti selezionati</small>
      <div class="col-4 col-auto text-start">
      <select id="causale_tutto"  class="show-tick form-select" data-live-search="true" name="causale_tutto" required="">
      <option name="causale_tutto" value="">Seleziona la causale</option>
      <?php 
      $query="select id, descrizione from spazzamento.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
      $result = pg_query($conn_hub, $query);
      while($r = pg_fetch_assoc($result)) {
        ?>
			<option name="causale_tutto" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>
			<?php } ?>
      </select>
      
      </div>
      <div class="col-2 text-start">
      <select id="punteggio_tutto" class="show-tick form-select" data-live-search="true"  name="punteggio_tutto" required="">
      <option name="punteggio_tutto" value="">%</option>
      <option name="punteggio_tutto" value="100">100</option>
      <option name="punteggio_tutto" value="75">75</option>
      <option name="punteggio_tutto" value="50">50</option>
      <option name="punteggio_tutto" value="25">25</option>
      <option name="punteggio_tutto" value="0">0</option>
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
      <i class="fa-solid fa-floppy-disk"></i> Salva
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
        <th data-field="tratto" data-sortable="true" data-visible="true" data-filter-control="input">Tratto</th>
        <th data-field="check_previsto" data-sortable="true" data-visible="false">Previsto</th>
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



var $table_tappe = $('#totem_percorsi_dettaglio_s');

$(function() {
    $table_tappe.bootstrapTable();
});


$table_tappe.on('check.bs.table', function (e, row) {
  console.log('Tappa '+ row.tappa+ ' selezionata');
  //console.log(e);
  $('#punteggio_'+row.tappa+'').removeAttr('disabled');
  $('#insert_'+row.tappa+'').removeAttr('disabled');
  
});

$table_tappe.on('uncheck.bs.table', function (e, row) {
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
$table_tappe.on('post-body.bs.table', function (e, row) {
//$table_tappe.on('load-success.bs.table', function (e, row) {
  //console.log('caricata la tabella');
  select_causale();
});




function updateAll() {
  console.log('Sono nella funzione updateAll')
    var causale_all = $('select#causale_tutto').find(":selected").val();  
    console.log('Causale '+causale_all);
    //controlli su causali e punteggio
    if (!causale_all){
      $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i>Selezionare una causale</div>').fadeIn("slow");
      return;
    } else if (causale_all === '100'){
      $('#punteggio_tutto option[value=100]').prop("selected", true);
    } else { 
      var punteggio_all = $('select#punteggio_tutto').find(":selected").val();
      console.log('Punteggio '+punteggio_all);
      
      if (!punteggio_all){
        $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Selezionare una % di completamento</div>').fadeIn("slow");
        return;
      } else if (causale_all != '100' && punteggio_all==='100'){
        $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Causale e punteggio non compatibili</div>').fadeIn("slow");
        return;
      }
    }
    // messaggio OK
    var messaggio= '<br><div class="alert alert-warning alert-animated" role="alert"> <i class="bi bi-exclamation-triangle-fill"></i> Le modifiche sono state applicate su tutti i tratti selezionati. <b>Ricorda di salvare per rendere effettiva la modifica.</b></div>';
    console.log(messaggio);
    $("#ConsOutput").html(messaggio).fadeIn("slow");
    return $.map($table_tappe.bootstrapTable('getSelections'), 
    function(row, index) {
        $('#insert_'+row.tappa+' option[value='+causale_all+']').prop("selected", true);
        $('#punteggio_'+row.tappa+' option[value='+punteggio_all+']').prop("selected", true);
    });
    
  
  };



function getRowSelections() {
    return $.map($table_tappe.bootstrapTable('getSelections'), 
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
  return $.map($table_tappe.bootstrapTable('getSelections'), 
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
  return $.map($table_tappe.bootstrapTable('getSelections'), 
    function(row, index) {
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      console.log('Ho scelto manualmente la causale ' + causale);
      if (causale==='100'){
        $('#punteggio_'+row.tappa+' option:selected').prop("selected", false);
        $('#punteggio_'+row.tappa+'').attr('disabled',true);
      } else {
         $('#punteggio_'+row.tappa+' option[value=0]').prop("selected", true);
         $('#punteggio_'+row.tappa+'').attr('disabled',false);
      }
    })
}


function getRowSelections() {
    return $.map($table_tappe.bootstrapTable('getSelections'), 
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
    /*css: {"background-color": "#40BF40", "font-weight": "bold"}*/
    css: {"background-color": "rgba(64, 191, 64, 0.6)", "color":"#ffffff !important"}
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
        '<option name="punteggio" value="0">0</option>',
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
//se cariccato da modal non ricarico footer e req_bottom, altrimenti se caricato da url diretto li carico
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    require_once('req_bottom.php');
    require('./footer.php');
}
?>



</body>

</html>