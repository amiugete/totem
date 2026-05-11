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
from raccolta.cons_percorsi_raccolta_amiu cpsxa 
where cpsxa.id_percorso = $1
and to_date($2, 'DD/MM/YYYY') between cpsxa.data_inizio and cpsxa.data_fine";

$result0 = pg_prepare($conn_hub, "query_percorso", $query_percorso);

$res_ok=0;

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


 



}


?>



<!-- TOAST CONTAINER -->
<div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div id="dynamicToast" class="toast align-items-center text-bg-primary border-0" role="alert"
         aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toastMessage">
                <!-- Messaggio dinamico -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto"
                    data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

  
<script type="text/javascript">
function showToast(message, type = "warning", sticky = true) {

    // Suono — beep semplice (Web Audio API)
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioCtx.createOscillator();
        const gainNode = audioCtx.createGain();

        oscillator.type = "square";
        oscillator.frequency.setValueAtTime(600, audioCtx.currentTime); // tono udibile
        gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);

        oscillator.connect(gainNode);
        gainNode.connect(audioCtx.destination);

        oscillator.start();
        oscillator.stop(audioCtx.currentTime + 0.15);
    } catch (e) {
        console.log("Audio non supportato");
    }

    // Vibrazione su mobile
    if (navigator.vibrate) navigator.vibrate([80, 40, 80]);


    // Creazione toast
    const toastId = "toast_" + Date.now();

    const toastHtml = `
        <div id="${toastId}" class="toast-sticky toast-${type}">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <span>${message}</span>
                <span class="toast-close-btn" onclick="$('#${toastId}').remove();">&times;</span>
            </div>
        </div>
    `;

    $("#toastContainer").append(toastHtml);

    // Se NON sticky → autodistrugge dopo 3.5s
    if (!sticky) {
        setTimeout(() => {
            $("#" + toastId).fadeOut("slow", function () { $(this).remove(); });
        }, 3500);
    }
}




// Chiude tutti i toast (o solo quelli sticky se vuoi filtrare)
function closeToast() {
    $(".toast-sticky").fadeOut("fast", function() {
        $(this).remove();
    });
}


</script>

     




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
                    url: 'backoffice/cons_tappe_raccolta.php', 
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
                      url: "./tables/report_totem_percorsi_cons_r.php?id=<?php echo $id;?>&datalav=<?php echo $datalav;?>&id_uo=<?php echo $id_uo;?>"
                    }); 
                  });
                  return false;
                  console.log('Fatto refresh pagina');
                });
              });



        



      </script>


      <hr>
      <div class="row row-cols g-3">
      
      <div class="col-3 text-start">
      <select id="causale_tutto"  class="show-tick form-select" data-live-search="true" name="causale_tutto" required="">
      <option name="causale_tutto" value="">Causale</option>
      <?php 
      $query="select id, descrizione from raccolta.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
      $result = pg_query($conn_hub, $query);
      while($r = pg_fetch_assoc($result)) {
        ?>
			<option name="causale_tutto" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>
			<?php } ?>
      </select>
      <small>Da applicare su tutto il percorso o sulle vie selezionate</small>
      </div>



      
      <div class="col-3 text-start">
      <select id="via_tutto"  class="show-tick form-select" data-live-search="true" name="causale_tutto" required="">
      <option name="via_tutto" value="">Via..</option>
      <?php 
      $query2="select distinct cpra.id_via, cpra.nome_via 
FROM raccolta.cons_percorsi_raccolta_amiu cpra
WHERE id_percorso = $1
and TO_DATE($2, 'DD/MM/YYYY') BETWEEN data_inizio AND data_fine
order by 2";
      $result2 = pg_prepare($conn_hub, "query_vie", $query2);

      if (!pg_last_error($conn_hub)){
          #$res_ok=0;
      } else {
          pg_last_error($conn_hub);
          $res_ok= $res_ok+1;
      }
      //echo "Sono qua 2";
      $result2 = pg_execute($conn_hub, "query_vie", array($id, $datalav));  
      if (!pg_last_error($conn_hub)){
          #$res_ok=0;
      } else {
          pg_last_error($conn_hub);
          $res_ok= $res_ok+1;
      }



      while($r2 = pg_fetch_assoc($result2)) {
        ?>
			<option name="via_tutto" value="<?php echo trim($r2["id_via"]);?>"><?php echo $r2["nome_via"] ;?></option>
			<?php } ?>
      </select>
      <small>Seleziona una via</small>
      </div>
  
      
      <div class="col-2 text-start">
        <div class="form-check">
        <input class="form-check-input" type="checkbox" value="1" disabled="" id="non_previste">
        <label class="form-check-label" for="non_previste">
          Anche non previste
        </label>
        </div>
      </div>
      
      <div class="col-2 text-start">
      <button onclick="updateAll()" class="btn btn-warning btn-sm">
      <i class="fa-solid fa-list-check"></i> Applica a tappe previste
      <br><small>(opzionalmente via)</small>
      </button>
      </div>


      



      <div class="col-2 text-end">
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
				<table  id="totem_percorsi_dettaglio_r" class="table-hover table-sm" 
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
				data-filter-control="false"
        data-sort-select-options = "true"
        data-url="./tables/report_totem_percorsi_cons_r.php?id=<?php echo $id;?>&datalav=<?php echo $datalav;?>&id_uo=<?php echo $id_uo;?>" 
        data-toolbar="#toolbar1" 
        data-show-toolbar="false"
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th-->  
        <th data-field="num_elementi" data-sortable="false" data-visible="true" data-formatter="numBidoni" >Num</th>
        <th data-field="tappa" data-sortable="false" data-visible="true" data-formatter="tastiBidoni" >Tappa</th>
        <th data-field="id_via" data-sortable="false" data-visible="false" >Cod via</th>
        <th data-field="riferimento" data-sortable="true" data-visible="true" data-filter-control="input">Tratto</th>
        <th data-field="check_previsto" data-sortable="true" data-formatter="nameFormatterPrevisto" data-visible="true">Previsto</th>
        <th data-field="id_causale" data-sortable="true" data-visible="true" data-formatter="causaleForm">Causale</th>

        <th data-field="" data-sortable="true" data-visible="true" data-formatter="consStato">Stato<br>consuntivazione</th>
        <th data-field="fatto" data-sortable="true" data-visible="false" data-formatter="consStato">Fatti</th>
        <!--th data-field="punteggio" data-sortable="true" data-visible="true" data-filter-control="select">% completamento</th> 
        <th data-field="causale" data-sortable="true" data-visible="true" data-filter-control="select">Causale</th> 
        <th data-field="operatore" data-sortable="true" data-visible="true" data-filter-control="select">Operatore</th-->
    </tr>
</thead>
</table>





<script type="text/javascript">


var $table_tappe = $('#totem_percorsi_dettaglio_r');


$(function() {
  $table_tappe.bootstrapTable({ });
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




$( "#causale_tutto" ).on( "change", function() {
  console.log('Causale globale cambiata');
  let c_a = $('#causale_tutto').val();
  if (c_a === "100") {
      $('#non_previste').attr('disabled',false);
  } else {
      $('#non_previste').prop('checked', false);
      $('#non_previste').attr('disabled',true);

  }

} );

function updateAll() {
    console.log('Sono nella funzione updateAll');

    const causale_all = $('#causale_tutto').val();

    const via_all = $('#via_tutto').val();
    

    // Verifico se applicare anche alle tappe non previste
    let np = $('#non_previste').is(':checked') ? 1 : 0;


    console.log('Causale da applicare: ' + causale_all);
    if (via_all) {
      console.log('Via da applicare: ' + via_all);
    }
    
    console.log('Non previste? ' + np);
    
    // --- 1️⃣ Controllo causale ---
    if (!causale_all) {
        closeToast();
        let msg = '<i class="bi bi-exclamation-triangle-fill"></i> Seleziona una causale prima di applicare.';
        
        showToast(msg, "danger", true);

        $("#ConsOutput").html(
            '<br><div class="alert alert-danger alert-animated" role="alert">' +
            msg +
            '</div>'
        ).fadeIn("slow");

        return;
    }

    // Messaggio finale OK
    let messaggio_ok =
        '<i class="bi bi-exclamation-triangle-fill"></i> Le modifiche sono state applicate. <b>Ricorda di salvare.</b>';

    // --- 2️⃣ Ciclo su tutte le righe della tabella ---
    const allRows = $table_tappe.bootstrapTable('getData');

    allRows.forEach(row => {

        // Considero solo le righe previste
        if ((row.check_previsto !== '1') && (np===0)) {
          console.log('' + row.tappa + ' perchè non prevista devo ripulirla');

          //$('#insert_' + row.tappa).val('82');  
          updateTappa(row.tappa, causale_all);
          //$('#insert_' + row.tappa).attr('disabled',true);
          $('#insert_' + row.tappa).val(causale_all);  
          //console.log('Ho finito ma non va');
          return;
        } 
        
        if ((!via_all) || (via_all && row.id_via === via_all)) {
          console.log('Aggiorno la tappa ' + row.tappa + ' della via ' + row.id_via);
        
        
          let tappa = row.tappa;
          console.log(tappa);
          if (np){
            // Aggiorno select causale
            $('#insert_' + tappa).attr('disabled',false);
            $('#insert_' + tappa).val(causale_all);
          } else {
            // Aggiorno select causale
            $('#insert_' + tappa).val(causale_all);
          }


          // --- 3️⃣ Se COMPLETATO (100) → tutte verdi ---
          if (causale_all === "100") {
               updateTappa(tappa, causale_all); // mette tutto verde e aggiorna numero
          }
          // --- 4️⃣ Altra causale → tutte rosse, numero 0 ---
          else {
              updateTappa(tappa, causale_all);  // mette tutto rosso e numero = 0
          }
        }else {
          //console.log('Salto la tappa ' + row.tappa + ' della via ' + row.id_via);
          return;
        } 
    });

    // Mostro messaggio finale
    closeToast();
    showToast(messaggio_ok, "success", false);

    $("#ConsOutput").html('<div class="alert alert-warning alert-animated" role="alert">'+messaggio_ok+'</div>').fadeIn("slow");
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


/*function getRowSelections() {


    return $.map($table_tappe.bootstrapTable('getData'), 
    function(row, index) {
      var num_elementi = parseInt(row.num_elementi) || 0;
      let numBidoniVerdi = $('[id^="'+row.tappa+'_"].bin-green').length;
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      console.log('Causale tappa '+row.tappa+': '+causale);
      return row.tappa+'-'+causale+'-'+numBidoniVerdi;
    })
  };*/

  function getRowSelections() {
    return $.map($table_tappe.bootstrapTable('getData'), 
    function(row, index) {
      var num_elementi = parseInt(row.num_elementi) || 0;
      let numBidoniVerdi = $('[id^="'+row.tappa+'_"].bin-green').length;
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      console.log('Causale tappa '+row.tappa+': '+causale);

      // ← AGGIUNGI QUESTO: salta le tappe non previste senza causale valida
      if (row.check_previsto !== '1' && (!causale || causale === '')) {
        console.log('Salto tappa non prevista senza causale: ' + row.tappa);
        return null; // $.map ignora i valori null/undefined
      }

      return row.tappa+'-'+causale+'-'+numBidoniVerdi;
    })
}



  /*window.stateFormatter = (value, row, index) => {
    if (row.check_prev_cons === '1') {
      return {
        checked: true
      }
    }
  
  }*/

  






  function  consStato(value, row, index) {
  if ((!row.id_causale) ) {
    return "";
   } else {
    return "Consuntivato da "+row.codice+" il "+ moment(row.datainsert).format('DD/MM/YYYY HH:mm') +"";
   }
  };



function numBidoni(value, row, index) {
  
  let html2 = ''; // Qui accumuliamo i pulsanti
  // Converte in numero per sicurezza
  let num_elementi = parseInt(row.num_elementi) || 0;
  let fatto = parseInt(row.fatto) || 0;
  if ((row.check_previsto === '1' && (!row.id_causale)) || (row.id_causale === '100')) {
    // Crea un pulsante per ogni contenitore
    html2= `<input type="number" id="update_${row.tappa}" style="cursor:pointer;width:40px;text-align:center;" readonly value=${num_elementi} onclick="updateTappa('${row.tappa}', '${row.id_causale}')">`
  } else {
    html2= `<input  type="number" id="update_${row.tappa}" style="cursor:pointer;width:40px;text-align:center;" readonly value=${fatto} onclick="updateTappa('${row.tappa}', '${row.id_causale}')">`
  }
  return html2;
}


function tastiBidoni(value, row, index) {
  //console.log('Row:', row);
  //console.log('num_contenitori =', row.num_elementi);
  // Mostra i pulsanti solo se le condizioni sono rispettate
  let html = ''; // Qui accumuliamo i pulsanti

  // Converte in numero per sicurezza
  let num_elementi = parseInt(row.num_elementi) || 0;
  let num_elementi_fatti = parseInt(row.fatto) || 0;
  let id_causale = row.id_causale;

  /*console.log('num_elementi =', num_elementi);
  console.log('num_elementi fatti =', num_elementi_fatti);
  console.log('id_causale =', row.id_causale);*/

  /*if ((row.check_previsto === '1' && (!row.id_causale)) || (row.id_causale === '100')) {
    
    // Crea un pulsante per ogni contenitore
    for (let i = 1; i <= num_elementi; i++) {
      html += `
          <i id="${row.tappa}_${i}" class="fa-solid fa-trash-can bin-icon bin-green" onclick="updateCont('${row.tappa}_${i}', ${num_elementi})"></i> 
      `;
    }
  } else {
    // Crea un pulsante per ogni contenitore
    for (let i = 1; i <= num_elementi; i++) {
      html += `
          <i id="${row.tappa}_${i}" class="fa-solid fa-trash-can bin-icon bin-red" onclick="updateCont('${row.tappa}_${i}', ${num_elementi})"></i> 
      `;
    }


  }*/
  //finchè non è consuntivato id_causale è null e num_elementi_fatti è 0 quindi metto tutto fatto
  const tutti_fatti = (
    (id_causale === null || id_causale === '') && num_elementi_fatti === 0
  );

  // se tutti_fatti is true allora fatti = num_elementi altrimenti num_elementi_fatti
  const fatti = tutti_fatti ? num_elementi : num_elementi_fatti;
  for (let i = 1; i <= num_elementi; i++) {
    const colore = (i <= fatti) ? 'bin-green' : 'bin-red';

    html += `
      <i
        id="${row.tappa}_${i}"
        class="fa-solid fa-trash-can bin-icon ${colore}"
        onclick="updateCont('${row.tappa}_${i}')">
      </i>
    `;
  }

  
  // Ritorna i pulsanti come HTML concatenato
  return html;
}









// intercetta il click sul contenitore
function updateCont(value, num_elementi) {
    console.log("Click su contenitore", value);

    let messaggio_ok= "Dato modificato correttamente. Quando hai terminato ricordati di cliccare su salva";

    const [tappa, bidone] = value.split("_");
    const causale = $('#insert_' + tappa).val();
    const icon = $("#" + value);
    const isRed = icon.hasClass("bin-red");
    const isGreen = icon.hasClass("bin-green");
    let numBidoniVerdi = $(`[id^="${tappa}_"].bin-green`).length;
    let numBidoniRossi = $(`[id^="${tappa}_"].bin-red`).length;
    // ─────────────────────────────────────────────
    // 1️⃣ BIDONE VERDE → può diventare rosso SOLO se causale valida e != 100
    // ─────────────────────────────────────────────
    if (isGreen) {
      //console.log(isGreen);
    // Se causale NON valida → messaggio
      if (!causale || causale === "100") {
          closeToast();
        let messaggio='<i class="bi bi-exclamation-triangle-fill"></i> ' +
              'Per rimuover un contenitore devi selezionare una causale diversa da COMPLETATO';
          $("#ConsOutput").html(
              '<br><div class="alert alert-danger alert-animated" role="alert">' +
              messaggio +
              '</div>'
          ).fadeIn("slow");
          showToast(messaggio, "danger", true);
          return;
      }
      // verde → rosso
        icon.removeClass("bin-green").addClass("bin-red");
    // ─────────────────────────────────────────────
    // 2️⃣ BIDONE ROSSO → può tornare verde SOLO se causale == 100
    // ─────────────────────────────────────────────
    } else if (isRed) {
        if (causale !== "100" && numBidoniRossi == 1) {
          closeToast();
        let messaggio='<i class="bi bi-exclamation-triangle-fill"></i> ' +
              'Per ripristinare il contenitore devi impostare la causale come COMPLETATO';
            $("#ConsOutput").html(
              '<br><div class="alert alert-warning alert-animated" role="alert">' +
               messaggio+
              '</div>'
          ).fadeIn("slow");
          showToast(messaggio, "warning", true);
          return;
        }

        // rosso → verde
        icon.removeClass("bin-red").addClass("bin-green");
    }



  

    // Aggiorna il totale dei contenitori selezionati
    const selected = $(`[id^="${tappa}_"].bin-green`).length;
    $("#update_" + tappa).val(selected);

    console.log("Totale aggiornato per tappa", tappa, "=", selected);
    
    closeToast();
    showToast(messaggio_ok, "success", false);
    $("#ConsOutput").fadeOut("fast", function() {
        $(this).empty();
    });
    return;
}




function updateTappa(tappa, caus) {
    console.log("Click su update tappa:", tappa);
    const causale = $('#insert_'+tappa).val();
    //console.log('Causale globale passata:', causaleAll);
    console.log('Causale singola selezionata:', causale);
    
    let messaggio_ok= "Dato modificato correttamente. Quando hai terminato ricordati di cliccare su salva";
    /*if (causaleAll != None){
      console.log('Uso causale globale');
      causale = causaleAll;
    } else {
      console.log('Uso causale singola');
      causale = $('#insert_' + tappa).val();
    }*/

    const bidoni = $(`[id^="${tappa}_"]`);
    const rossi = bidoni.filter(".bin-red").length;
    const verdi = bidoni.filter(".bin-green").length;

    // ─────────────────────────────────────────────
    // 0️⃣ Controllo: nessuna causale → errore
    // ─────────────────────────────────────────────
    if (!causale) {
        closeToast();
        let messaggio='<i class="bi bi-exclamation-triangle-fill"></i> ' +
            'Seleziona una causale prima di modificare la tappa.' ;
        $("#ConsOutput").html(
            '<br><div class="alert alert-danger alert-animated" role="alert">' +
            messaggio +
            '</div>'
        ).fadeIn("slow");
        showToast(messaggio, "danger", true);
        return;
    }

    // ─────────────────────────────────────────────
    // 1️⃣ Causale diversa da 100 → tutti ROSSI
    // ─────────────────────────────────────────────
    if (causale !== "100") {

        // Se già tutti rossi → warning
        if (rossi === bidoni.length) {
            closeToast();
            let messaggio='<i class="bi bi-exclamation-triangle-fill"></i> ' +
                'Tutti i contenitori sono già rimossi. Selezionare COMPLETATO per ripristinarli';
            $("#ConsOutput").html(
                '<br><div class="alert alert-warning alert-animated" role="alert">' +
                 messaggio+
                '</div>'
            ).fadeIn("slow");
            showToast(messaggio, "warning", true);
            return;
        }

        // Tutti diventano rossi
        bidoni.removeClass("bin-green").addClass("bin-red");

        // Numero selezionati = 0
        $("#update_" + tappa).val(0);
        $('#insert_' + tappa).val(causale);

        console.log("Tappa", tappa, "→ tutti rossi, selezionati = 0");
        closeToast();
        showToast(messaggio_ok, "success", false);
        $("#ConsOutput").fadeOut("fast", function() {
            $(this).empty();
        });
        return;
    }

    // ─────────────────────────────────────────────
    // 2️⃣ Causale == 100 → tutti VERDI
    // ─────────────────────────────────────────────
    if (causale === "100") {

        // Se sono già tutti verdi → warning
        if (verdi === bidoni.length) {
            closeToast();
            let messaggio='Tutti i contenitori sono già impostati come presenti.<br><br>Se la tappa non fosse stata fatta selezionare una causale';
            $("#ConsOutput").html(
                '<br><div class="alert alert-warning alert-animated" role="alert">' +
                '<i class="bi bi-exclamation-triangle-fill"></i> ' +
                messaggio +
                '</div>'
            ).fadeIn("slow");
            showToast(messaggio, "warning", true);
            return;
        }

        // Tutti diventano verdi
        bidoni.removeClass("bin-red").addClass("bin-green");

        // Numero selezionati = totale contenitori
        $("#update_" + tappa).val(bidoni.length);

        console.log(
            "Tappa", tappa,
            "→ tutti verdi, selezionati =", bidoni.length
        );
        closeToast();
        showToast(messaggio_ok, "success", false);
        $("#ConsOutput").fadeOut("fast", function() {
            $(this).empty();
        });
        return;
    }
}






/*function causaleForm(value, row, index) {
  if ((row.check_previsto === '1' && (!row.id_causale)) || ( row.id_causale === '100')) {
    return [
        //'<form action="" autocomplete="off" id="insert_'+row.tappa+'">',
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onclick="update_p()" name="causale" required="">',
        '<option name="causale" value="100">COMPLETATO</option>',  
        <?php 
        $query="select id, descrizione from raccolta.causali_testi ct  where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
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
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true" onclick="update_p()"  name="causale" id="causale" required="">',
        '<option name="causale" value="">Seleziona una causale</option>',  
        <?php 
        $query="select id, descrizione from spazzamento.causali_testi ct  
        where descrizione not like 'TERMINATO SENZA DISSERVIZI' order by 2";
        $result = pg_query($conn_hub, $query);
        while($r = pg_fetch_assoc($result)) {
        ?>
          if (row.id_causale == '<?php echo trim($r["id"]);?>') {
            '<option name="causale" value="<?php echo trim($r["id"]);?>" selected><?php echo $r["descrizione"] ;?></option>',
          } else {
            '<option name="causale" value="<?php echo trim($r["id"]);?>"><?php echo $r["descrizione"] ;?></option>',
          }
				<?php } ?>
        '</select>'//,
        //'</form>'
      ].join('');
        }
     
   
  };*/




function causaleForm(value, row, index) {

    let html = '';
    let selectedId = row.id_causale ?? '';

    if ((row.check_previsto === '1' && !row.id_causale) || row.id_causale === '100') {

        html += '<select id="insert_' + row.tappa + '" class="show-tick form-select" data-live-search="true" onclick="update_p()" name="causale" required>';
        html += '<option value="100">COMPLETATO</option>';

        <?php
        $query = "SELECT id, descrizione 
                  FROM raccolta.causali_testi
                  WHERE id <> '100' /*NOT LIKE 'TERMINATO SENZA DISSERVIZI' */
                  ORDER BY 2";
        $result = pg_query($conn_hub, $query);
        while ($r = pg_fetch_assoc($result)) {
        ?>
            html += '<option value="<?php echo trim($r["id"]); ?>"><?php echo addslashes($r["descrizione"]); ?></option>';
        <?php } ?>

        html += '</select>';
    } else if (row.check_previsto === '0') {

        html += '<select id="insert_' + row.tappa + '" class="show-tick form-select" data-live-search="true" onclick="update_p()" name="causale" required>';
        html += '<option value="">Seleziona una causale</option>';

        <?php
        $query = "SELECT id, descrizione 
                  FROM raccolta.causali_testi
                  WHERE descrizione = 'COMPLETATO' 
                  ORDER BY 2";
        $result = pg_query($conn_hub, $query);
        while ($r = pg_fetch_assoc($result)) {
        ?>
            if (selectedId == '<?php echo trim($r["id"]); ?>') {
                html += '<option value="<?php echo trim($r["id"]); ?>" selected><?php echo addslashes($r["descrizione"]); ?></option>';
            } else {
                html += '<option value="<?php echo trim($r["id"]); ?>"><?php echo addslashes($r["descrizione"]); ?></option>';
            }
        <?php } ?>

        html += '</select>';
    } else {

        html += '<select id="insert_' + row.tappa + '" class="show-tick form-select" data-live-search="true" onclick="update_p()" name="causale" required>';
        html += '<option value="">Seleziona una causale</option>';

        <?php
        $query = "SELECT id, descrizione 
                  FROM raccolta.causali_testi
                  WHERE descrizione NOT LIKE 'TERMINATO SENZA DISSERVIZI' 
                  ORDER BY 2";
        $result = pg_query($conn_hub, $query);
        while ($r = pg_fetch_assoc($result)) {
        ?>
            if (selectedId == '<?php echo trim($r["id"]); ?>') {
                html += '<option value="<?php echo trim($r["id"]); ?>" selected><?php echo addslashes($r["descrizione"]); ?></option>';
            } else {
                html += '<option value="<?php echo trim($r["id"]); ?>"><?php echo addslashes($r["descrizione"]); ?></option>';
            }
        <?php } ?>

        html += '</select>';
    }

    return html;
}


function nameFormatterPrevisto(value, row, index) {
  if (row.check_previsto == '1' || row.check_previsto === 1 || row.check_previsto === true){
    return '<span style="font-size: 1em; color: green;"> <i title="'+row.check_previsto+'" class="fa-regular fa-calendar-check"></i></span>';
  } else {
    return '<span style="font-size: 1em; color: red;"> <i title="'+row.check_previsto+'" class="fa-regular fa-calendar-xmark"></i></span>';
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

<?php
?>