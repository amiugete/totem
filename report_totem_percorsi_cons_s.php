<?php
//session_set_cookie_params($lifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

    
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


require_once 'carica_env.php';
require_once 'conn_ok.php';






?> 

<style>
/* Stile checkbox tappe previste */
.previsto input[type="checkbox"] {
  pointer-events: none;
  cursor: not-allowed;
  /*opacity: 0.6;*/
}

.modal-content {
    display: flex;
    flex-direction: column;
}

.modal-body {
    display: flex;
    flex-direction: column;
    min-height: 0;
}

.pagina-consuntivazione {
    flex: 1;
    min-height: 0;
    display: flex;
    flex-direction: column;
}

/* PARTE FISSA */
.intestazione-tabella1 {
    flex-shrink: 0;
}

.contenitore-tabella {
    flex: 1;
    min-height: 0;
    overflow-y: auto;
}

.colonna-nascosta {
  width: 0 !important;
  min-width: 0 !important;
  max-width: 0 !important;
  padding: 0 !important;
  border: 0 !important;
  overflow: hidden !important;
}

td.colonna-nascosta,
th.colonna-nascosta {
    display: none !important;
}

.icona-modificata {
    color: #ff0000;
    display: inline-block;
    animation: matita-pulse 1.2s ease-in-out infinite;
}

@keyframes matita-pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }

    50% {
        transform: scale(1.35);
        opacity: 0.55;
    }
}




/* Compatto la tabella */
#totem_percorsi_dettaglio_s {
    font-size: 0.90rem;
}

#totem_percorsi_dettaglio_s th,
#totem_percorsi_dettaglio_s td {
    padding: 0.2rem 0.3rem !important;
}

#totem_percorsi_dettaglio_s select {
    font-size: 0.78rem;
}

/* Intestazioni centrate verticalmente */
#totem_percorsi_dettaglio_s thead th {
vertical-align: middle !important;
}
 
/* Filtro leggermente più piccolo */
#totem_percorsi_dettaglio_s thead input.form-control {
  font-size: 0.70rem !important;
  padding: 0.2rem 0.2rem;
  height: auto;
}


#totem_percorsi_dettaglio_s .form-control {
font-size: 0.70rem !important;
}
</style>



</head>

<body>



<div class="container pagina-consuntivazione">
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




      

     




<div id="intestazione-tabella1">
            
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
          // passo la variabile consuntivatore a javascript
          //const consuntivatore = <?= json_encode($consuntivatore) ?>;
          window.consuntivatore = <?= json_encode($consuntivatore) ?>;
        </script>

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
      <small>Seleziona una causale e la % di completamento da applicare sui tratti selezionati. 
        Con il tasto applica le modifiche sono applicate a tutti i tratti, altrimenti clicca sulle righe che vuoi modificare.
      </small>
      <div class="col-4 col-auto text-start">
      <select id="causale_tutto"  class="show-tick form-select" data-live-search="true" name="causale_tutto" required="">
      <option name="causale_tutto" value="">Seleziona la causale</option>
      <?php 
      $query="select id, descrizione from spazzamento.causali_testi ct  
      where descrizione not like 'TERMINATO SENZA DISSERVIZI' 
      and id not in (98, 102) /* tolgo anche la causale TAPPA AGGIUNTIVA c
      che non è così chiara*/
      order by 2";
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
      <button id="btnApplica" onclick="updateAll()" class="btn btn-warning">
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
      <span id="noteApplica"></span>
      </div>





      <!-- SPAZIO DEDICATO ALL'OUTPUT -->
      <div id="ConsOutput" class="text-center">

      </div>
      <div id="ConsOutputSave" class="text-center">
      </div>
      <hr>
      </div>
      <!-- fine parte fissa-->
      <div class="contenitore-tabella">
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
        <th data-field="causale" data-sortable="true" data-visible="true" data-formatter="causaleForm" data-class="colonna-nascosta">Causale</th>
        <th data-field="punteggio" data-sortable="true" data-visible="true" data-formatter="punteggioForm" data-class="colonna-nascosta">Punteggio</th>
        <th data-field="" data-sortable="true" data-visible="true" data-formatter="causalePunteggioForm">Cons</th>
        <th data-field="" data-sortable="true" data-visible="true" data-formatter="consStato">Stato<br>cons</th>
        <!--th data-field="punteggio" data-sortable="true" data-visible="true" data-filter-control="select">% completamento</th> 
        <th data-field="causale" data-sortable="true" data-visible="true" data-filter-control="select">Causale</th> 
        <th data-field="operatore" data-sortable="true" data-visible="true" data-filter-control="select">Operatore</th-->
    </tr>
</thead>
</table>





<script type="text/javascript">


var $table_tappe = $('#totem_percorsi_dettaglio_s');

$(function() {
    $table_tappe.bootstrapTable({
      stickyHeader: true,
      stickyHeaderOffsetLeft: 0,
      stickyHeaderOffsetRight: 0
  });
});



// Rimuovo eventuali handler già registrati
$table_tappe.off('click', 'select, input, button, a');
$table_tappe.off('click-row.bs.table');



$table_tappe.on('load-success.bs.table', function () {
  controllaApplica();

});



function controllaApplica() {
    //console.log('Sta girando la funzione controllaApplica');
    var disabilita = false;

    $table_tappe.bootstrapTable('getData').forEach(function(row) {

        if (
            row.codice !== null &&
            row.codice !== '' &&
            String(row.codice) !== String(consuntivatore) &&
            !String(consuntivatore).startsWith('UT')
        ) {
            disabilita = true;
        }

    });

    $('#btnApplica').prop('disabled', disabilita);
    if (disabilita === true) {
      $("#noteApplica").html('<small><i class="fa-solid fa-person-circle-exclamation"></i>Ci sono tappe consuntivate da altro operatore. Il tasto applica non può essere utilizzato</small>').fadeIn("slow");   
    } 
}


$('#causale_tutto').on('change', function () {

    const causale = $(this).val();
    const $punteggio = $('#punteggio_tutto');

    //console.log('Ho scelto causale '+causale);
    if (causale === '100') {
        // Causale 100 → può scegliere qualsiasi percentuale
        $punteggio.find('option[value="100"]').prop('disabled', false);
         // Se causale = 100, imposto automaticamente 100%
        $punteggio.val('100');

        // Disabilito tutte le opzioni tranne 100
        $punteggio.find('option').each(function () {
            $(this).prop('disabled', $(this).val() !== '100');
        });
    } else {

        // Riabilito tutte le percentuali
        $punteggio.find('option').prop('disabled', false);
        
        
        //console.log('Controllo il punteggio che ora vale '+ $punteggio.val());
        // Se era già selezionato 100, lo resetto
        if ($punteggio.val() === '100') {
            $punteggio.val('');
        }
        // Altre causali → 100% non consentito
        $punteggio.find('option[value="100"]').prop('disabled', true);
    }
});



$table_tappe.on('change', 'select[id^="insert_"]', function () {

    const causale = $(this).val();

    // Ricavo l'id della tappa
    const tappa = this.id.replace('insert_', '');

    // Select della percentuale della stessa riga
    const $punteggio = $('#punteggio_' + tappa);

    console.log('Per la tappa '+tappa +' ho scelto causale '+causale);
    if (causale === '100') {

        // Causale 100 → imposto automaticamente 100%
        $punteggio.val('100');

        // Disabilito tutte le percentuali tranne 100
        $punteggio.find('option').prop('disabled', false);

        $punteggio.find('option:not([value="100"])')
            .prop('disabled', true);

    } else {

        // Riabilito tutte le percentuali
        $punteggio.find('option').prop('disabled', false);

        // Se era già 100, azzero
        if ($punteggio.val() === '100') {
            $punteggio.val('');
        }
         // 100% non consentito
        $punteggio.find('option[value="100"]')
            .prop('disabled', true);


    }

    var messaggio= '<br><div class="alert alert-warning alert-animated" role="alert"><i class="fa-solid fa-pencil"></i> Modifiche in corso. <b>Ricorda di salvare per rendere effettiva le modifiche.</b></div>';
    $("#ConsOutputSave").html(messaggio).fadeIn("slow");
    evidenziaRigaModificata($(this).closest('tr'));
});


$table_tappe.on('check.bs.table', function (e, row) {
  console.log('Tappa '+ row.tappa+ ' selezionata');
  //console.log(e);
  $('#punteggio_'+row.tappa+'').removeAttr('disabled');
  $('#insert_'+row.tappa+'').removeAttr('disabled');
  
});

$table_tappe.on('uncheck.bs.table', function (e, row) {
  /*if (row.check_previsto === '1') {
        console.log('Tappa: '+ row.tappa+ ' prevista non posso rimoverla');
        $table_tappe.bootstrapTable('check', row.tappa);
        return;
    }*/

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

/*
click sulla parte libera della riga → esegue la tua funzione
click sul select → non esegue la funzione della riga
click sull'eventuale checkbox → non esegue la funzione della riga
click su un bottone → non esegue la funzione della riga
*/

$table_tappe.on('created-controls.bs.table', function () {
    console.log('Cliccato su filtro');
    $table_tappe.find('thead input.bootstrap-table-filter-control')
        .attr('inputmode', 'text');
});





$table_tappe.on('click', 'tbody tr', function (e) {


    const index = $(this).data('index');
    const row = $table_tappe.bootstrapTable('getData')[index];

    const causale = $('#causale_tutto').val();
    const punteggio = $('#punteggio_tutto').val();

    console.log('row.causale = '+row.causale);
    console.log('row.punteggio = '+row.punteggio);

    const isUT = String(window.consuntivatore).startsWith('UT');
    const stessaPersona =
        String(window.consuntivatore) === String(row.codice);

    const giaCompletata =
        row.codice !== null && row.codice !== '' && row.punteggio === '100';

    console.log('isUT ' +isUT+' stessaPersona'+ stessaPersona +' giaCompletata '+ giaCompletata)
    if (!isUT && giaCompletata && !stessaPersona) {
      $("#ConsOutput").html('<br><div class="alert alert-warning alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Tappa già consuntivata come completata da '+row.codice+'. Non posso cambiare la consuntivazione. <br>In caso di problemi segnalare al RUT/assistente/GRIM</div>').fadeIn("slow");
      $('#insert_' + row.tappa).prop('disabled', true);
      $('#punteggio_' + row.tappa).prop('disabled', true);
      return;
    } else {
      $("#ConsOutput").html('').fadeIn("slow");
    }

    // Se ho cliccato su un controllo, non considero il click come click sulla riga
    if ($(e.target).closest('select, input, button, a').length) {
        console.log('Click su select/input/button: esco');
        $("#ConsOutput").html('').fadeIn("slow");
        return;
    }

    console.log('CLICK SULLA RIGA');

    




    if (!causale) {
      $("#ConsOutput").html('<br><div class="alert alert-warning alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Per modificare questa riga Selezionare una causale</div>').fadeIn("slow");
      return;
    }
    if (!punteggio) {
      $("#ConsOutput").html('<br><div class="alert alert-warning alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Selezionare una %</div>').fadeIn("slow");
      return;
    }
    $("#ConsOutput").html('').fadeIn("slow");
    console.log('Applico alla tappa ' + row.tappa);
    console.log('Causale: ' + causale);
    console.log('Punteggio: ' + punteggio);

    $('#insert_' + row.tappa).val(causale);
    $('#punteggio_' + row.tappa).val(punteggio);

    aggiornaCons(row.tappa);
    if (causale === '100') {
      console.log('Causale 100');
        $('#punteggio_' + row.tappa).prop('disabled', true);
    } else {
        console.log('Causale  diverso da 100');
        $('#punteggio_' + row.tappa).prop('disabled', false);
    }


    // Evidenzio la riga modificata
    var messaggio= '<br><div class="alert alert-warning alert-animated" role="alert"><i class="fa-solid fa-pencil"></i> Modifiche in corso. <b>Ricorda di salvare per rendere effettiva le modifiche.</b></div>';
    $("#ConsOutputSave").html(messaggio).fadeIn("slow");
    evidenziaRigaModificata($(this));

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
      var punteggio_all = $('select#punteggio_tutto').find(":selected").val();
      console.log('Punteggio '+punteggio_all);
    } else { 
      var punteggio_all = $('select#punteggio_tutto').find(":selected").val();
      console.log('Punteggio '+punteggio_all);
      
      if (!punteggio_all){
        $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Selezionare una % di completamento</div>').fadeIn("slow");
        return;
      } else if (causale_all != '100' && punteggio_all==='100'){
        $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Causale e punteggio non compatibili</div>').fadeIn("slow");
        return;
      } else if (causale_all === '100' && punteggio_all!='100'){
        $("#ConsOutput").html('<br><div class="alert alert-danger alert-animated" role="alert"><i class="bi bi-exclamation-triangle-fill"></i> Causale e punteggio non compatibili</div>').fadeIn("slow");
        return;
      }
    }
    // messaggio OK
    var messaggio= '<br><div class="alert alert-warning alert-animated" role="alert"> <i class="fa-solid fa-pencil"></i> Le modifiche sono state applicate su tutti i tratti selezionati. <b>Ricorda di salvare per rendere effettiva la modifica.</b></div>';
    //console.log(messaggio);
    $("#ConsOutputSave").html(messaggio).fadeIn("slow");
    return $.map($table_tappe.bootstrapTable('getSelections'), 
    function(row, index) {
        $('#insert_'+row.tappa+' option[value='+causale_all+']').prop("selected", true);
        $('#punteggio_'+row.tappa+' option[value='+punteggio_all+']').prop("selected", true);
        aggiornaCons(row.tappa);
        evidenziaRigaModificata(
            $('#insert_' + row.tappa).closest('tr')
        );
    });
    
  
  };



function evidenziaRigaModificata($element) {
  $element.addClass('riga-modificata');

  if (!$element.find('.icona-modificata').length) {
      $element.find('td:last').prepend(
          '<i class="bi bi-pencil-fill icona-modificata me-1" title="Riga modificata, da salvare"></i>'
      );
  }
}


function aggiornaCons(tappa) {

    //console.log('Sono nella funzione aggiornaCons');
    var $causale = $('#insert_' + tappa);
    var $punteggio = $('#punteggio_' + tappa);

    var causale = $causale.val();
    var punteggio = $punteggio.val();

    var row = $table_tappe.bootstrapTable('getData').find(function(row) {
        return String(row.tappa) === String(tappa);
    });

    //console.log('Per la tappa ' + tappa + ' ho causale ' + causale + ' e punteggio ' + punteggio);
    // Tappa prevista ma ancora senza consuntivazione:
    // nella visualizzazione considero COMPLETATO / 100%
    if (row && row.check_previsto === '1' && !causale) {
        causale = '100';
        punteggio = '100';
    }

    var descrizione = $causale
        .find('option[value="' + causale + '"]')
        .text();

    $('#causale_view_' + tappa).text(descrizione || '');

    $('#punteggio_view_' + tappa).text(
        punteggio ? punteggio + '%' : ''
    );
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
    
      // aggiorno la colonna Cons
      aggiornaCons(row.tappa);
    })

    


};

/*function select_causale() {

    console.log('Chiamo la funzione select_causale');

    $table_tappe.bootstrapTable('getData').forEach(function(row) {

        // Se esiste già una consuntivazione
        if (row.id_causale) {

            $('#insert_' + row.tappa).val(row.id_causale);
            $('#punteggio_' + row.tappa).val(row.punteggio);

        } else if (row.check_previsto === '1') {

            // Tappa prevista NON ancora consuntivata:
            // visualizzo COMPLETATO / 100
            $('#insert_' + row.tappa).val('100');
            $('#punteggio_' + row.tappa).val('100');
        }

        aggiornaCons(row.tappa);
    });
}*/


function update_p(select) {

    const tappa = select.id.replace('insert_', '');
    const causale = $(select).val();

    console.log('Tappa ' + tappa);
    console.log('Causale scelta: ' + causale);

    if (causale === '100') {

        $('#punteggio_' + tappa + ' option:selected').prop('selected', false);
        $('#punteggio_' + tappa).attr('disabled', true);

    } else {

        $('#punteggio_' + tappa + ' option[value=0]').prop('selected', true);
        $('#punteggio_' + tappa).attr('disabled', false);
    }



     

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
        checked: true, readonly: true
      }
    }
    //return value
  }

  
function rowStyle(row, index) {

  // NON selezionata
  /*if (!row.state) {
    return {
      classes: 'text-wrap non-selezionata',
      css: {
        "background-color": "#FFB6C1"
      }
    }
  }*/


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
      css: {"background-color": "#FFB6C1"}
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
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onchange="update_p(this)"  name="causale" required="">',
        '<option name="causale" value="100" selected>COMPLETATO</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  
        where descrizione not like 'TERMINATO SENZA DISSERVIZI' 
        and id not in (98, 102, 100) /* tolgo anche la causale TAPPA AGGIUNTIVA c
        che non è così chiara*/ order by 2";
        $result = pg_query($conn_hub, $query);
        while($r = pg_fetch_assoc($result)) {
        ?>
				'<option name="causale" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>',
				<?php } ?>
        '</select>'//,
        //'</form>'
      ].join(''); 
    } else if (row.check_previsto === '0'){
      return [
      //'<form action="" autocomplete="off" id="insert_'+row.tappa+'">',
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onchange="update_p(this)"   disabled="" name="causale" required="">',
        '<option name="causale" value="">Seleziona una causale</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  
        where descrizione not like 'TERMINATO SENZA DISSERVIZI' 
        and id not in (98, 102) /* tolgo anche la causale TAPPA AGGIUNTIVA c
        che non è così chiara*/ order by 2";
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
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onchange="update_p(this)"  disabled="" name="causale" required="">',
        '<option name="causale" value="">Seleziona una causale</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  
        where descrizione not like 'TERMINATO SENZA DISSERVIZI' 
        and id not in (98, 102) /* tolgo anche la causale TAPPA AGGIUNTIVA c
        che non è così chiara*/ order by 2";
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

function causalePunteggioForm(value, row, index) {

    return [
        '<div class="text-center">',
            '<div class="fw-bold" id="causale_view_' + row.tappa + '"></div>',
            '<div class="text-muted" id="punteggio_view_' + row.tappa + '"></div>',
        '</div>'
    ].join('');
}




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