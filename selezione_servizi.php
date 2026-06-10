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
require_once('./req.php');

the_page_title();


require_once 'carica_env.php';
require_once 'conn_ok.php';


$badge=$_GET['operatore'] ?? ''; # altrimenti vuoto 



 

?> 

<style>
  
  body {
      background-color: bisque !important;; 
      /*background:linear-gradient(to left, #A9A9A9, #8FBC8F);*/
    }

  .container {
    font-size: 21px; /* modifica la dimensione del testo per tutto il corpo */
  }

  .form-control,
   .filter-option-inner-inner, .text {
    font-size: 23px !important; /* modifica la dimensione del testo per tutto il corpo */
  }

</style>



</head>



<body>


  
 


<div class="container">

<img src="./img/amiu_small_white.png"  style="margin-top: 25px; margin-bottom: 20px; width: 200px;" class="img-fluid d-block mx-auto" alt="...">
<hr>

  <?php
  if ($badge == ''){
  ?>
  <div class="row justify-content-md-center"> 
  <div class="col-md-auto">
    <h1><i class="fa-solid fa-user-slash"></i>
      <br>Operatore non riconosciuto
    </h1>
    <h2><i class="fa-solid fa-circle-minus"></i>
    <br>Impossibile accedere all'applicativo</h2>
  </div>
</div>
  <?php
    die();
  } 

  // cerco su quale UT è stata fatta l'ultima consuntivazione


  $query_ut_operatore='with consuntivazioni_persona as (
select ve.datainsert, cpsxa.id_uo, cpsxa.desc_uo, ve.codice 
from spazzamento.v_effettuati ve 
join spazzamento.cons_percorsi_spazz_x_app cpsxa 
	on cpsxa.id_tappa_raggr = ve.tappa 
where ve.codice = $1	
union 
select ve.datainsert, cpsxa.id_uo, cpsxa.desc_uo, ve.codice 
from raccolta.v_effettuati ve 
join raccolta.cons_percorsi_raccolta_amiu cpsxa 
	on cpsxa.id_tappa = ve.tappa
where ve.codice = $1
)
select * from consuntivazioni_persona 
order by 1 desc limit 1
  ';


  $result_operatore = pg_prepare($conn_hub, "query_ut_operatore", $query_ut_operatore);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    $res_ok=$res_ok+1;
}

   
  $result_operatore = pg_execute($conn_hub, "query_ut_operatore", array($badge));


  if (pg_last_error($conn_hub)){
      echo pg_last_error($conn_hub);
      $res_ok=$res_ok+1;
  }
    
  while($r = pg_fetch_assoc($result_operatore)) {
    $id_last_uo = $r['id_uo'];
    $desc_last_uo = $r['desc_uo'];
  }

  ?>
  <div class="row">
    <div class="col text-center">
      
    <?php 
    
    $consuntivatore = $badge;
    require_once('selezione_operatore.php');

    echo 'Operatore con badge '. $badge . ' ('.$op.')';
   
    if ($desc_last_uo) {
      echo '<br><small>Ultima consuntivazione su '. $desc_last_uo.'</small>';
    } else {
      echo '<br><small>Operatore che non ha mai consuntivato</small>';
    }

    $today = new DateTime('now');
    $timezone = new DateTimeZone('Europe/Rome');
    $today->setTimezone($timezone);
    $hour = $today->format('Hi');
    if ($hour < '0301'){
      $today = $today->modify("-1 day");
      $nota_data='<font color="red"> <i class="fa-solid fa-clock-rotate-left"></i> Prima delle 3 è impostata la data di ieri</font>';
    }
    
    if ($hour >= '0301' AND $hour < '1150'){
        $nota_data='<font color="#cc0000"> <i class="fa-solid fa-clock-rotate-left"></i> E\' impostata la data di oggi.<br> Se sbagliata cliccare sulla data sopra</font>';
    } 

    

    $data_percorsi = $today;

?>
<style>
.vl {
  border-left: 6px solid green;
  height: 72px;
}
</style>
</row>
<form  name="form_filtro" id="form_filtro" autocomplete="off">
<input type="hidden" class="form-control" id="last_uo" name="last_uo" value="<?php echo $id_last_uo;?>">

<div class="row justify-content-md-center">
<div class="form-group col-md-6">
    <label for="data_percorsi" class="form-label">Data verifica:</label>
    <input type="text" class="form-control" id="js-date3" name="data_percorsi" onchange="cambiato_servizio(this.value);" value="<?php echo $today->format('d/m/Y');?>" required>
    <!--input type="text" class="form-control" id="js-date3" name="data_percorsi" value="<?php echo $today->format('d/m/Y');?>" required-->
    <div id="nota_data" class="form-text"><?php echo $nota_data;?></div>
</div>
</div>



<div class="row justify-content-md-center">
<div class="form-group col-md-6">
    <label for="ut0" class="form-label">UT / Rimessa:</label>
    
    <select class="selectpicker show-tick form-control"
        data-live-search="true"
        onchange="cambiata_ut(this.value);"
        name="ut0"
        id="ut0"
        required>
</select>
  </div>
</div>



<div class="row justify-content-md-center">
<div class="form-group col-md-6">
    <label for="tipo_servizio" class="form-label">Tipo servizio </label>
    
    <select class="selectpicker show-tick form-control"
        data-live-search="true"
        onchange="cambiato_tipo(this.value);"
        name="tipo_servizio"
        id="tipo_servizio"
        required>
</select>
  </div>
</div>


<div class="row justify-content-md-center">
<div class="form-group col-md-6">
    <label for="servizio" class="form-label">Servizio </label>
    
    <select class="selectpicker show-tick form-control"
        data-live-search="true"
        onchange="cambiato_servizio(this.value);"
        name="servizio"
        id="servizio"
        required>
</select>
  </div>
</div>


<div class="row justify-content-md-center"> 
  <div class="col-md-auto text-start">
    <div id="percorsi">
      <!-- Qua ci finirà l'elenco percorsi governato da ajax-->
      
    
    
    
    </div>
  </div>
</div>

</form>
</div>
</div>


 <div class="row">
    <div class="col">

<!-- MODAL VUOTO DA POPOLARE CON PERCORSO -->
<div class="modal fade" id="PercorsoModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"> 
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen">
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




  <script>





// sostituisce completamente le options e re-instanzia selectpicker
function replaceSelectpicker(selector, optionsHtml) {
  // distruggi l'istanza precedente (se esiste)
  try { $(selector).selectpicker('destroy'); } catch(e){}

  // sostituisci contenuto
  $(selector).html(optionsHtml);

  // (ri)inizializza
  try {
    $(selector).selectpicker();
  } catch(e) {
    console.warn('selectpicker init failed for', selector, e);
  }
}



// tenta di refreshare o reinizializzare un selectpicker esistente
function ensureSelectpicker(selector) {
  // il wrapper bootstrap-select ha classe .bootstrap-select
  var $sel = $(selector);
  var $wrap = $sel.parent('.bootstrap-select');

  if ($wrap.length) {
    try {
      $sel.selectpicker('refresh');
    } catch(e) {
      // se refresh fallisce, reinizializza
      $sel.selectpicker('destroy');
      $sel.selectpicker();
    }
  } else {
    // non era inizializzato, inizializza
    try { $sel.selectpicker(); } catch(e){ console.warn(e); }
  }
}


  function cambiata_data(val, callback) {
    let last_uo = $('#last_uo').val();
    console.log('Last uo '+ last_uo);
    let data_percorsi = $('#js-date3').val();
    console.log(data_percorsi);
    $.ajax({
        url: './backoffice/ut_combo_ajax_serv.php',
        method: 'GET',
        data: {"last_uo": last_uo, "data_percorsi": data_percorsi },
        success: function(response) {
          $('#ut0').selectpicker('destroy'); // 💣 elimina istanza esistente
          $('#ut0').empty();                 // 🧹 svuota opzioni
          $('#ut0').append(response);        // ➕ aggiungi nuove
          $('#ut0').selectpicker('render');  // 🔁 renderizza di nuovo

          if (callback) callback(); // esegue una funzione successiva
        },
        error: function(xhr, status, error) {
            console.error("Errore AJAX:", error);
             $('#ut0').empty().append('<option value="0">Errore</option>').selectpicker('refresh');
        }
    });
  };


  function cambiata_ut(val) {
    let ut = $('#ut0').val();
    console.log('Ut ' + ut);
    let data_percorsi = $('#js-date3').val();
    console.log('Data '+data_percorsi);
    $.ajax({
        url: './backoffice/tipo_servizio_combo_ajax_serv.php',
        method: 'GET',
        data: { "data_percorsi": data_percorsi , "ut": ut },
        success: function(response) {
          $('#tipo_servizio').selectpicker('destroy'); // 💣 elimina istanza esistente
          $('#tipo_servizio').empty();                 // 🧹 svuota opzioni
          $('#tipo_servizio').append(response);        // ➕ aggiungi nuove
          $('#tipo_servizio').selectpicker('render');  // 🔁 renderizza di nuovo
          $('#servizio').empty();                 // 🧹 svuota opzioni
          $('#percorsi').empty();                 // 🧹 svuota opzioni
        },
        error: function(xhr, status, error) {
            console.error("Errore AJAX:", error);
             $('#servizio').empty().append('<option value="0">Errore</option>').selectpicker('refresh');
        }
    });

  };




  function cambiato_tipo(val) {
    let ut = $('#ut0').val();
    console.log('Ut ' + ut);
    let data_percorsi = $('#js-date3').val();
    console.log('Data '+data_percorsi);
    let tipo = $('#tipo_servizio').val();
    console.log('Tipo '+tipo);
    $.ajax({
        url: './backoffice/servizio_combo_ajax_serv.php',
        method: 'GET',
        data: { "data_percorsi": data_percorsi , "ut": ut, "tipo": tipo },
        success: function(response) {
          $('#servizio').selectpicker('destroy'); // 💣 elimina istanza esistente
          $('#servizio').empty();                 // 🧹 svuota opzioni
          $('#servizio').append(response);        // ➕ aggiungi nuove
          $('#servizio').selectpicker('render');  // 🔁 renderizza di nuovo
          $('#percorsi').empty();                 // 🧹 svuota opzioni
        },
        error: function(xhr, status, error) {
            console.error("Errore AJAX:", error);
             $('#servizio').empty().append('<option value="0">Errore</option>').selectpicker('refresh');
        }
    });

  };


  function cambiato_servizio(val) {
    let data_percorsi = $('#js-date3').val();
    console.log(data_percorsi);
    let ut = $('#ut0').val();
    console.log(ut);
    let servizio = $('#servizio').val();
    console.log(servizio);
    $.ajax({
        url: './backoffice/percorsi_ajax_serv.php',
        method: 'GET',
        data: { data_percorsi: data_percorsi, ut: ut, servizio: servizio },
        success: function(response) {
          $('#percorsi').empty();                 // 🧹 svuota opzioni
          $('#percorsi').append(response);        // ➕ aggiungi nuove
        },
        error: function(xhr, status, error) {
            console.error("Errore AJAX:", error);
             $('#servizio').empty().append('<option value="0">Errore</option>').selectpicker('refresh');
        }
    });

  };


  function scelto_percorso(val) {
    console.log(val); 
    var data_percorsi = $('#js-date3').val();
    console.log(data_percorsi);
    var params = new URLSearchParams(document.location.search);
    var operatore = params.get("operatore"); 
    console.log('operatore = '+operatore);
    var $radio = $('input[name=percorso]:checked');
    var id_percorso = $radio.attr('id');
    console.log('id = ' +id_percorso);
    console.log('datalav = '+data_percorsi);
    let ut = $('#ut0').val();
    console.log('id_uo='+ut);
        $.ajax({   
            type: "POST",
            url: "report_totem_percorsi_serv.php",
            data: 'id=' + id_percorso + '&datalav='+data_percorsi +'&consuntivatore='+operatore+'&id_uo='+ut+'',
            dataType: "text",                  
            success: function(response){                   
                $(".modal-body").html(response); 
                var modalEl = document.getElementById('PercorsoModal');
                var modal = new bootstrap.Modal(modalEl);
                modal.show();
            }
        });
        //$('#PercorsoModal').modal('show');
  };



  const myModalEl = document.getElementById('PercorsoModal');

      myModalEl.addEventListener('hidden.bs.modal', function () {

      console.log("Modal chiuso!");

      
      console.log("Modal chiuso, ricarico la pagina...");
      location.reload(); // ricarica tutta la pagina
      

    
    // qui la tua funzione AJAX/jQuery


   // qui la tua funzione AJAX/jQuery
 
    // qui la tua funzione AJAX/jQuery
    //let data_percorsi = $('#js-date3').val();
    //cambiata_data(data_percorsi);

  
    
    /*let servizio = $('#servizio').val();
    cambiato_servizio(servizio);*/


    


    });

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
 
  
// carico la data all'avvio
$(document).ready(function() {
    //cambiata_data($('#js-date3').val());
    //cambiata_ut($('#ut0').val());
    cambiata_data($('#js-date3').val(), function() {
      cambiata_ut($('#ut0').val());
    });  
});
</script>


</body>

</html>