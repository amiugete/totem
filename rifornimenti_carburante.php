<?php
//session_set_cookie_params($lifetime);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// PARAMETRI DA SPOSTARE NELL'ENV
$max_litri = 1000;
$max_importo = 2000;

$costo_minimo = 0.50;
$costo_massimo = 3.00;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="roberto" >

    <title>Gestione rifornimenti carburante</title>
<?php 
require_once('./req.php');

the_page_title();


require_once 'carica_env.php';
require_once 'conn_ok.php';


$badge=$_GET['operatore'] ?? ''; # altrimenti vuoto 



 

?> 

<style>
  
  body {
      background-color: gold !important;; 
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


<div class="row justify-content-md-center mt-4"> 
  <div class="col-md-auto d-flex flex-column align-items-center">

  <!--img src="./img/amiu_small_white.png" style="margin-top: 25px; margin-bottom: 20px; width: 200px;" class="img-fluid d-block mx-auto" alt="..."-->
    <h1 class="mb-1" style="margin-top: 30px; margin-bottom: 30px;">
      <i class="fa-solid fa-gas-pump"></i>
    </h1>

    <h2 class="mt-0">
      Registra rifornimento carburante
    </h2>

  </div>
</div>
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

  // cerco su quale UT è stata fatta l'ultima consuntivazione (in questo caso uso il badge come identificativo dell'operatore)

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


    




    echo '<i class="fa-solid fa-user"></i> Operatore con badge '. $badge . ' ('.$op.' Matr.'.$matricola.')';
   
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
<input type="hidden" class="form-control" id="matricola" name="matricola" value="<?php echo $matricola;?>">


<div class="row justify-content-md-center">
<div class="form-group col-md-6">
    <label for="data_percorsi" class="form-label">Data rifornimento:</label>
    <input type="text" class="form-control" id="js-date3" name="data_percorsi" onchange="cambiato_servizio(this.value);" value="<?php echo $today->format('d/m/Y');?>" required>
    <div id="nota_data" class="form-text"><?php echo $nota_data;?></div>
</div>





<div class="form-group col-md-6">
    <label for="ut0" class="form-label">UT / Rimessa:</label>
    <select class="selectpicker show-tick form-control"
        data-live-search="true"
        name="ut0"
        id="ut0"
        required>
</select>
  </div>
</div>

<hr>

<!-- CAMPO SPORTELLO -->
<div class="row justify-content-md-center">
    <div class="col-md-12">
        <label for="sportello_input" class="form-label">
            <i class="fa-solid fa-truck-moving"></i> 
            Inserisci lo sportello
        </label>
</div>
</div>
<div class="row justify-content-md-center">
    <div class="col-md-6">

        <!--label for="sportello_input" class="form-label">
            Inserisci lo sportello
        </label-->

        <input type="text"
               class="form-control form-control-lg text-center"
               id="sportello_input"
               name="sportello_input"
               maxlength="5"
               readonly
               placeholder="Tocca per inserire">

        <!--div class="form-text text-center">
            Tocca il campo per inserire lo sportello
        </div-->

    </div>


    <!--div class="col-md-6">

        <button type="button"
                class="btn btn-primary btn-lg w-100"
                id="verifica-mezzo">

            <i class="fa-solid fa-magnifying-glass"></i>
            Verifica mezzo

        </button>

    </div-->

</div>
<div class="row mb-3" id="result_mezzo">
<!-- Risultato verifica mezzo -->    
</div>

<script>
// Chiude tutti i toast (o solo quelli sticky se vuoi filtrare)
function closeToast() {
    $(".toast-sticky").fadeOut("fast", function() {
        $(this).remove();
    });
}

// funzione verifica

// =====================================================
// VERIFICA MEZZO
// =====================================================

function verificaMezzo(sportello) {

    sportello = String(sportello || "").trim();

    if (sportello === "") {
        return;
    }

    // Fino alla verifica il resto rimane bloccato
    $(".valore-rifornimento, .passo-successivo")
        .prop("disabled", true);

    // Mostro un messaggio mentre verifico
    $("#result_mezzo")
        .html(
            '<div class="alert alert-info text-center" style="margin-top: 1%;">' +
                '<span class="spinner-border spinner-border-sm me-2"></span>' +
                'Verifica mezzo in corso...' +
            '</div>'
        )
        .fadeIn("slow");

    $.ajax({

        url: "backoffice/verifica_mezzo.php",

        type: "POST",

        data: {
            sportello: sportello
        },

        success: function(response) {

            closeToast();

            $("#result_mezzo")
                .html(response)
                .fadeIn("slow");

            // Mezzo trovato:
            // abilito i passi successivi
            $(".valore-rifornimento")
                .prop("disabled", false);
        },

        error: function(xhr, status, error) {

            closeToast();

            $("#result_mezzo")
                .html(
                    '<div class="alert alert-danger text-center" role="alert" style="margin-top: 1%;">' +
                        '<i class="fa-solid fa-circle-xmark"></i> ' +
                        '<strong>Mezzo non trovato.</strong>' +
                        '<br>' +
                        'Verifica lo sportello inserito.' +
                    '</div>'
                )
                .fadeIn("slow");

            // Rimangono disabilitati
            $(".valore-rifornimento, .passo-successivo")
                .prop("disabled", true);

            console.error(
                "Errore verifica mezzo:",
                status,
                error
            );
        }
    });
}



$("#verifica-mezzo").on("click", function () {

    const sportello =
        $("#sportello_input").val().trim();


    if (sportello === "") {

        showToast(
            "Inserisci lo sportello.",
            "warning",
            true
        );

        return;
    }


    $.ajax({

        url: "backoffice/verifica_mezzo.php",

        type: "POST",

        data: {
            sportello: sportello
        },

        success: function (response) {

            closeToast();

            $("#result_mezzo")
                .html(response)
                .fadeIn("slow");

        },

        error: function (xhr, status, error) {
            closeToast();
            showToast(
                "Mezzo non trovato!",
                "danger",
                true
            );

            console.error(
                "AJAX error:",
                status,
                error
            );
        }

    });

});


</script>
<hr>

<!-- LITRI E IMPORTO -->
<div class="row justify-content-md-center g-3">

    <!-- LITRI -->
    <div class="col-6 col-md-4">

        <label class="form-label fw-bold">
            Litri
        </label>

        <button type="button"
                class="btn btn-outline-primary w-100 valore-rifornimento"
                data-campo="litri"
                style="height: 110px; --bs-btn-disabled-opacity: 0.3;">

            <div class="fs-6">
                <i class="fa-solid fa-gas-pump"></i>
                Litri
            </div>

            <div id="litri_display"
                 class="fs-2 fw-bold mt-2">
                0,00
            </div>

        </button>

        <input type="hidden"
               id="litri_input"
               name="litri"
               value="">

    </div>


    <!-- IMPORTO -->
    <div class="col-6 col-md-4">

        <label class="form-label fw-bold">
            Importo
        </label>

        <button type="button"
                class="btn btn-outline-success w-100 valore-rifornimento"
                data-campo="importo"
                style="height: 110px; --bs-btn-disabled-opacity: 0.3;">

            <div class="fs-6">
                <i class="fa-solid fa-euro-sign"></i>
                Importo
            </div>

            <div id="importo_display"
                 class="fs-2 fw-bold mt-2">
                0,00 €
            </div>

        </button>

        <input type="hidden"
               id="importo_input"
               name="importo"
               value="">

    </div>


    <!-- Km -->
    <div class="col-6 col-md-4">

        <label class="form-label fw-bold">
            Km mezzo
        </label>

        <button type="button"
                class="btn btn-outline-success w-100 valore-rifornimento"
                data-campo="km"
                style="height: 110px; --bs-btn-disabled-opacity: 0.3;">

            <div class="fs-6">
                <i class="fa-solid fa-arrow-up-9-1"></i>
                Km
            </div>

            <div id="km_display"
                 class="fs-2 fw-bold mt-2">
                0
            </div>

        </button>

        <input type="hidden"
               id="km_input"
               name="km"
               value="">

    </div>
</div>

<div class="row justify-content-md-center mt-3">

    <div class="col-md-8">

        <div id="controllo_costo"
             class="alert alert-secondary text-center"
             style="display:none;">

            <span id="costo_descrizione"></span>

        </div>

    </div>

</div>

<hr>

<div class="row justify-content-md-center mt-4 mb-4">

    <div class="col-md-8">

        <button type="submit"
                class="btn btn-success btn-lg w-100 passo-successivo"
                id="submit-rifornimento"
                style="height: 80px; font-size: 1.5rem;">

            <i class="fa-solid fa-check"></i>
            CONFERMA RIFORNIMENTO

        </button>

    </div>

</div>

</form>
</div>
</div>




<script>


// al caricamento della pagina, disabilito i tasti rifornimento 
$(document).ready(function() {

    // Litri e importo inizialmente non disponibili
    $(".valore-rifornimento, .passo-successivo").prop("disabled", true);


    // Se esiste già uno sportello registrato
    // nelle ultime 8 ore, lo imposto e lo verifico
    if (sportelloReg !== "") {

        $("#sportello_input")
            .val(sportelloReg);

        verificaMezzo(sportelloReg);
    }

});



// controllo submit

$("#form_filtro").on("submit", function(event) {

    event.preventDefault();


    // =================================================
    // RECUPERO DATI
    // =================================================

    const sportello =
        $("#sportello_input").val().trim();

    const litriString =
        $("#litri_input").val().trim();

    const importoString =
        $("#importo_input").val().trim();

    const kmString =
        $("#km_input").val().trim();

    const litri =
        parseFloat(litriString);

    const importo =
        parseFloat(importoString);

    const km =
        parseFloat(kmString);

    // =================================================
    // SPORTELLO
    // =================================================

    if (sportello === "") {

        showToast(
            "Inserisci lo sportello.",
            "warning",
            true
        );

        apriKeypad("sportello");

        return;
    }


    // =================================================
    // LITRI - VALORE OBBLIGATORIO
    // =================================================

    if (
        litriString === "" ||
        isNaN(litri) ||
        litri <= 0
    ) {

        showToast(
            "Inserisci i litri del rifornimento.",
            "warning",
            true
        );

        apriKeypad("litri");

        return;
    }


    // =================================================
    // LITRI - MASSIMO
    // =================================================

    if (litri > maxLitri) {

        showToast(
            "I litri inseriti superano il massimo consentito: " +
            maxLitri.toFixed(2).replace(".", ","),
            "danger",
            true
        );

        apriKeypad("litri");

        return;
    }


    // =================================================
    // LITRI - MASSIMO 2 DECIMALI
    // =================================================

    const partiLitri =
        litriString.replace(",", ".").split(".");

    if (
        partiLitri.length > 1 &&
        partiLitri[1].length > 2
    ) {

        showToast(
            "I litri possono avere al massimo due cifre decimali.",
            "danger",
            true
        );

        apriKeypad("litri");

        return;
    }


    // =================================================
    // IMPORTO - VALORE OBBLIGATORIO
    // =================================================

    if (
        importoString === "" ||
        isNaN(importo) ||
        importo <= 0
    ) {

        showToast(
            "Inserisci l'importo del rifornimento.",
            "warning",
            true
        );

        apriKeypad("importo");

        return;
    }


    // =================================================
    // IMPORTO - MASSIMO
    // =================================================

    if (importo > maxImporto) {

        showToast(
            "L'importo inserito supera il massimo consentito: " +
            maxImporto.toFixed(2).replace(".", ",") +
            " €",
            "danger",
            true
        );

        apriKeypad("importo");

        return;
    }


    // =================================================
    // IMPORTO - MASSIMO 2 DECIMALI
    // =================================================

    const partiImporto =
        importoString.replace(",", ".").split(".");

    if (
        partiImporto.length > 1 &&
        partiImporto[1].length > 2
    ) {

        showToast(
            "L'importo può avere al massimo due cifre decimali.",
            "danger",
            true
        );

        apriKeypad("importo");

        return;
    }


    // =================================================
    // CONTROLLO COSTO CARBURANTE
    // =================================================

    // SOLO INFORMATIVO.
    // NON BLOCCA MAI IL SALVATAGGIO.

    verificaCostoCarburante();


    // =================================================
    // COSTO AL LITRO
    // =================================================

    const costoLitro =
        importo / litri;


    // =================================================
    // DATI DA INVIARE
    // =================================================

    const formData = {

        matricola:
            $("#matricola").val(),

        /*uo:
            $("#last_uo").val(),*/

        datalav:
            $("#js-date3").val(),

        ut:
            $("#ut0").val(),

        sportello:
            sportello,

        litri:
            litri.toFixed(2),

        importo:
            importo.toFixed(2),
        
        km:
            km.toFixed(0)

    
        /*costo_litro:
            costoLitro.toFixed(4)*/
    };


    console.log(
        "Invio rifornimento:",
        formData
    );


    // =================================================
    // DISABILITA SUBMIT
    // =================================================

    const $submit =
        $("#submit-rifornimento");


    $submit
        .prop("disabled", true)
        .html(
            '<span class="spinner-border spinner-border-sm me-2"></span>' +
            'Salvataggio...'
        );


    // =================================================
    // AJAX
    // =================================================

    $.ajax({

        url: "backoffice/salva_rifornimento.php",

        type: "POST",

        data: formData,

        dataType: "json",


        success: function(response) {

            // =============================================
            // SALVATAGGIO OK
            // =============================================

            if (response.ok) {

                // Disabilito tutto il form
                $("#form_filtro :input")
                    .prop("disabled", true);


                // Messaggio molto evidente
                $("#result_mezzo").html(
                    '<div class="alert alert-success text-center fs-3 p-4">' +
                        '<i class="fa-solid fa-circle-check fa-2x mb-3"></i>' +
                        '<br>' +
                        '<strong>Rifornimento registrato correttamente</strong>' +
                    '</div>'
                );


                // Dopo 1 secondi ricarico la pagina
                setTimeout(function() {
                    // ricarico la pagina
                    window.location.reload();

                }, 1000);


                return;
            }


            // =============================================
            // ERRORE RESTITUITO DAL PHP
            // =============================================

            $submit
                .prop("disabled", false)
                .html(
                    '<i class="fa-solid fa-check"></i> ' +
                    'CONFERMA RIFORNIMENTO'
                );


            showToast(
                response.message ||
                "Impossibile salvare il rifornimento.",
                "danger",
                true
            );
        },


        // ---------------------------------------------
        // ERRORE AJAX
        // ---------------------------------------------

        error: function(xhr, status, error) {

            $submit
                .prop("disabled", false)
                .html(
                    '<i class="fa-solid fa-check"></i> ' +
                    'CONFERMA RIFORNIMENTO'
                );


            console.error(
                "Errore AJAX:",
                status,
                error
            );

            console.error(
                "Risposta server:",
                xhr.responseText
            );


            showToast(
                "Errore durante il salvataggio del rifornimento.",
                "danger",
                true
            );

        }

    });

});





</script>



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

</script>

  

  

</div>
<?php 
require_once('req_bottom.php');
require('./footer.php');
?>


<!-- MODAL TASTIERINO -->
<div class="modal fade"
     id="keypadModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="keypadTitle">
                    Inserisci valore
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Chiudi">
                </button>

            </div>


            <div class="modal-body">

                <!-- VALORE -->
                <div id="keypadDisplay"
                     class="form-control text-center fs-1 mb-3"
                     style="
                        height: 80px;
                        line-height: 60px;
                        letter-spacing: 3px;
                     ">
                    0
                </div>


                <!-- TASTIERINO -->
                <div class="row g-2">

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            1
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            2
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            3
                        </button>
                    </div>


                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            4
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            5
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            6
                        </button>
                    </div>


                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            7
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            8
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            9
                        </button>
                    </div>


                    <div class="col-4">
                        <button type="button"
                                class="btn btn-dark btn-lg w-100"
                                id="keypad-clear">
                            ←
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-secondary btn-lg w-100 keypad-btn">
                            0
                        </button>
                    </div>

                    <div class="col-4">
                        <button type="button"
                                class="btn btn-warning btn-lg w-100"
                                id="keypad-decimal">
                            ,
                        </button>
                    </div>

                </div>

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary btn-lg"
                        data-bs-dismiss="modal">
                    Annulla
                </button>

                <button type="button"
                        class="btn btn-success btn-lg"
                        id="keypad-ok">
                    <i class="fa-solid fa-check"></i>
                    OK
                </button>

            </div>

        </div>

    </div>

</div>


<style>
/* Stile tastierino */

#keypadModal .modal-content {
    border-radius: 15px;
}

#keypadModal .modal-body {
    padding: 25px;
}

#keypadModal .keypad-btn,
#keypadModal #keypad-clear,
#keypadModal #keypad-decimal {
    height: 75px;
    font-size: 2rem;
    font-weight: bold;
}

#keypadModal .modal-footer button {
    min-height: 65px;
    font-size: 1.3rem;
}

#keypadDisplay {
    height: 90px;
    font-size: 3rem !important;
    font-weight: bold;
    line-height: 70px;
}
</style>

<script type="text/javascript">
// =====================================================
// TASTIERINO RIFORNIMENTO
// =====================================================

let keypadValue = "";
let keypadCampo = "";
let keypadDecimali = 0;
let keypadMassimo = null;


// preso da PHP
const sportelloReg = <?php echo json_encode($sportello_reg ?? ''); ?>;

// Limiti configurati dal PHP
const maxLitri = <?php echo json_encode($max_litri); ?>;
const maxImporto = <?php echo json_encode($max_importo); ?>;

const costoMinimo = <?php echo json_encode($costo_minimo); ?>;
const costoMassimo = <?php echo json_encode($costo_massimo); ?>;


// =====================================================
// APERTURA TASTIERINO
// =====================================================

function aggiornaStatoConferma() {

    const sportello =
        $("#sportello_input").val().trim();

    const litri =
        parseFloat($("#litri_input").val());

    const importo =
        parseFloat($("#importo_input").val());

    const km =
        parseFloat($("#km_input").val());

    const datiCompleti =
        sportello !== "" &&
        !isNaN(litri) &&
        litri > 0 &&
        !isNaN(importo) &&s
        importo > 0;


    $("#submit-rifornimento")
        .prop("disabled", !datiCompleti);
}

function apriKeypad(campo) {

    keypadCampo = campo;

    // Recupero valore esistente
    keypadValue = $("#" + campo + "_input").val() || "";

    // Normalizzo eventuale virgola
    keypadValue = keypadValue.replace(",", ".");


    // Reset stato virgola
    $("#keypad-decimal").prop("disabled", false);


    // -------------------------------------------------
    // SPORTELLO
    // -------------------------------------------------

    if (campo === "sportello") {

        keypadDecimali = 0;
        keypadMassimo = 99999;

        $("#keypadTitle").text("Inserisci sportello");

        $("#keypad-decimal").hide();
    }


    // -------------------------------------------------
    // LITRI
    // -------------------------------------------------

    else if (campo === "litri") {

        keypadDecimali = 2;
        keypadMassimo = maxLitri;

        $("#keypadTitle").text("Inserisci litri");

        $("#keypad-decimal").show();
    }


    // -------------------------------------------------
    // IMPORTO
    // -------------------------------------------------

    else if (campo === "importo") {

        keypadDecimali = 2;
        keypadMassimo = maxImporto;

        $("#keypadTitle").text("Inserisci importo");

        $("#keypad-decimal").show();
    }


    // -------------------------------------------------
    // KM
    // -------------------------------------------------
    else if (campo === "km") {

        keypadDecimali = 0;
        keypadMassimo = 999999;

        $("#keypadTitle").text("Inserisci chilometri mezzo");

        $("#keypad-decimal").hide();    }

    // Se il valore contiene già la virgola,
    // disabilito il tasto virgola
    if (keypadValue.includes(".")) {
        $("#keypad-decimal").prop("disabled", true);
    }


    aggiornaKeypadDisplay();

    $("#keypadModal").modal("show");
}


// =====================================================
// DISPLAY
// =====================================================

function aggiornaKeypadDisplay() {

    // SPORTELLO
    if (keypadDecimali === 0) {

        $("#keypadDisplay").text(
            keypadValue === "" ? "0" : keypadValue
        );

        return;
    }


    // LITRI / IMPORTO / KM

    if (keypadValue === "") {

        $("#keypadDisplay").text("0,00");

        return;
    }


    // -------------------------------------------------
    // Stiamo digitando la parte decimale
    // -------------------------------------------------

    if (keypadValue.includes(".")) {

        const parti = keypadValue.split(".");

        const intero = parti[0] || "0";
        const decimali = parti[1] || "";

        $("#keypadDisplay").text(
            intero + "," + decimali
        );

        return;
    }


    // -------------------------------------------------
    // Solo parte intera
    // -------------------------------------------------

    const numero = parseFloat(keypadValue);

    if (isNaN(numero)) {

        $("#keypadDisplay").text("0,00");

        return;
    }


    $("#keypadDisplay").text(
        numero.toFixed(2).replace(".", ",")
    );
}


// =====================================================
// TASTI NUMERICI
// =====================================================

$(document).on("click", ".keypad-btn", function () {

    const numero = $(this).text().trim();

    console.log('Cliccato il numero '+numero)
    // =================================================
    // SPORTELLO
    // =================================================

    if (keypadCampo === "sportello") {

        // massimo 5 cifre
        if (keypadValue.length >= 5) {
            return;
        }


        keypadValue += numero;


        // Aggiorna campo principale
        $("#sportello_input").val(keypadValue);


        aggiornaKeypadDisplay();

        return;
    }

    
    if (keypadCampo === "sportello") {
        console.log(keypadCampo);

        $("#sportello_input").val(keypadValue);

        // Chiudo il keypad
        bootstrap.Modal.getInstance(
            document.getElementById("keypadModal")
        ).hide();

        // Verifico automaticamente il mezzo
        verificaMezzo(keypadValue);

        return;
    }



    // =================================================
    // LITRI / IMPORTO
    // =================================================


    // -------------------------------------------------
    // PARTE DECIMALE
    // -------------------------------------------------

    if (keypadValue.includes(".")) {

        const parti = keypadValue.split(".");

        const parteIntera = parti[0];
        let parteDecimale = parti[1] || "";


        // massimo 2 decimali
        if (parteDecimale.length >= 2) {
            return;
        }


        parteDecimale += numero;

        keypadValue =
            parteIntera + "." + parteDecimale;


        aggiornaKeypadDisplay();

        return;
    }


    // -------------------------------------------------
    // PARTE INTERA
    // -------------------------------------------------

    keypadValue += numero;


    // Evita 000123
    keypadValue =
        keypadValue.replace(/^0+(?=\d)/, "");


    // -------------------------------------------------
    // Controllo massimo
    // -------------------------------------------------

    const valore = parseFloat(keypadValue);

    if (
        keypadMassimo !== null &&
        !isNaN(valore) &&
        valore > keypadMassimo
    ) {

        // Rimuovo l'ultima cifra
        keypadValue =
            keypadValue.slice(0, -1);
    }

    //console.log('Arrivo qua');
    aggiornaKeypadDisplay();
});


// =====================================================
// VIRGOLA
// =====================================================

$("#keypad-decimal").on("click", function () {

    // Sportello
    if (keypadDecimali === 0) {
        return;
    }


    // Virgola già presente
    if (keypadValue.includes(".")) {
        return;
    }


    // Nessun valore → 0,
    if (keypadValue === "") {
        keypadValue = "0";
    }


    keypadValue += ".";


    // La virgola non può essere premuta nuovamente
    $(this).prop("disabled", true);


    aggiornaKeypadDisplay();
});


// =====================================================
// BACKSPACE
// =====================================================

$("#keypad-clear").on("click", function () {

    keypadValue =
        keypadValue.slice(0, -1);


    // Se sto modificando lo sportello
    if (keypadCampo === "sportello") {

        $("#sportello_input").val(keypadValue);
    }


    // Se ho cancellato la virgola,
    // la riabilito
    if (
        keypadDecimali > 0 &&
        !keypadValue.includes(".")
    ) {

        $("#keypad-decimal").prop("disabled", false);
    }


    aggiornaKeypadDisplay();
});


// =====================================================
// OK
// =====================================================

$("#keypad-ok").on("click", function () {


    
    // Nessun valore
    if (
        keypadValue === "" ||
        keypadValue === "."
    ) {

        showToast(
            "Inserisci un valore.",
            "warning",
            true
        );

        return;
    }


    const valore = parseFloat(keypadValue);


    if (isNaN(valore)) {

        showToast(
            "Valore non valido.",
            "danger",
            true
        );

        return;
    }


    // =================================================
    // CONTROLLO MASSIMO
    // =================================================

    if (
        keypadMassimo !== null &&
        valore > keypadMassimo
    ) {

        showToast(
            "Il valore massimo consentito è " +
            keypadMassimo
                .toFixed(keypadDecimali)
                .replace(".", ","),
            "danger",
            true
        );

        return;
    }


    // =================================================
    // SPORTELLO
    // =================================================

    if (keypadCampo === "sportello") {

        const sportello = keypadValue.trim();


        $("#sportello_input").val(keypadValue);
        
        // Tolgo il focus dal pulsante prima di chiudere la modal
        this.blur();

        // Chiudo il keypad
        bootstrap.Modal.getInstance(
            document.getElementById("keypadModal")
        ).hide();


        // Litri e importo restano disabilitati
        // fino alla verifica positiva
        $(".valore-rifornimento, .passo-successivo")
            .prop("disabled", true);


        // Pulisco eventuale risultato precedente
        $("#result_mezzo")
            .hide()
            .empty();


        // -------------------------------------------------
        // CONTROLLO SPORTELLO
        // -------------------------------------------------

        if (sportello === "") {

            showToast(
                "Inserisci lo sportello.",
                "warning",
                true
            );

            return;
        }


        // -------------------------------------------------
        // VERIFICA MEZZO
        // -------------------------------------------------

        $.ajax({

            url: "backoffice/verifica_mezzo.php",

            type: "POST",

            data: {
                sportello: sportello
            },


            // =============================================
            // MEZZO TROVATO
            // =============================================

            success: function(response) {

                closeToast();

                $("#result_mezzo")
                    .html(response)
                    .fadeIn("slow");


                // Abilito i passi successivi
                $(".valore-rifornimento")
                    .prop("disabled", false);
                
                aggiornaStatoConferma();

            },


            // =============================================
            // MEZZO NON TROVATO / ERRORE
            // =============================================

            error: function(xhr, status, error) {

                closeToast();

                $("#result_mezzo")
                    .html(
                        '<div class="alert alert-danger text-center" role="alert" style="margin-top: 1%;">' +
                            '<i class="fa-solid fa-circle-xmark"></i> ' +
                            '<strong>Mezzo non trovato.</strong>' +
                            '<br>' +
                            'Verifica lo sportello inserito.' +
                        '</div>'
                    )
                    .fadeIn("slow");


                // IMPORTANTISSIMO:
                // i campi successivi restano disabilitati
                $(".valore-rifornimento, .passo-successivo")
                    .prop("disabled", true);


                console.error(
                    "Errore verifica mezzo:",
                    status,
                    error
                );

            }

        });

        return;
    }

    // =================================================
    // LITRI / IMPORTO
    // =================================================

    else {

        // Sempre due decimali internamente
        const valoreFormattato =
            valore.toFixed(2);


        $("#" + keypadCampo + "_input")
            .val(valoreFormattato);


        aggiornaValoreRifornimento(
            keypadCampo,
            valore
        );
    }


    $("#keypadModal").modal("hide");
});


// =====================================================
// AGGIORNA DISPLAY PAGINA
// =====================================================

function aggiornaValoreRifornimento(campo, valore) {

    if (campo === "litri") {

        $("#litri_display").text(
            valore
                .toFixed(2)
                .replace(".", ",")
        );
    }


    else if (campo === "importo") {

        $("#importo_display").text(
            valore
                .toFixed(2)
                .replace(".", ",") + " €"
        );
    }

    else if (campo === "km") {

        $("#km_display").text(
            valore
                .toFixed(0)
                .replace(".", ",")
        );
    }
    // Controlla il costo appena cambia uno dei due valori
    verificaCostoCarburante();
}


// =====================================================
// APERTURA DA LITRI / IMPORTO
// =====================================================

$(document).on(
    "click",
    ".valore-rifornimento",
    function () {

        const campo =
            $(this).data("campo");

        apriKeypad(campo);
    }
);


// =====================================================
// APERTURA SPORTELLO
// =====================================================

$("#sportello_input").on("click", function () {

    // Se modifico lo sportello, devo rifare la verifica
    $(".valore-rifornimento, .passo-successivo").prop("disabled", true);

    // Azzero eventuali risultati precedenti
    $("#result_mezzo").hide().empty();

    apriKeypad("sportello");

});


// Funzione per controllare il costo
function verificaCostoCarburante() {

    const litri =
        parseFloat($("#litri_input").val());

    const importo =
        parseFloat($("#importo_input").val());


    const $alert =
        $("#controllo_costo");

    const $descrizione =
        $("#costo_descrizione");


    // Mancano uno o entrambi i valori
    if (
        isNaN(litri) ||
        isNaN(importo) ||
        litri <= 0 ||
        importo <= 0
    ) {

        $alert.hide();

        return true;
    }


    // Calcolo costo/litro
    const costo =
        importo / litri;


    const costoFormattato =
        costo.toFixed(2).replace(".", ",");


    // =================================================
    // COSTO NORMALE
    // =================================================

   if (
    costo >= costoMinimo &&
    costo <= costoMassimo
) {

    $alert
        .removeClass("alert-secondary alert-warning")
        .addClass("alert-success")
        .show();

    $descrizione.html(
        '<i class="fa-solid fa-circle-check"></i> ' +
        '<strong>Costo carburante: ' +
        costoFormattato +
        ' €/litro</strong>'
    );

    // Fino alla verifica il resto rimane bloccato
    $(".valore-rifornimento, .passo-successivo")
        .prop("disabled", false);
    return;
}


// COSTO ANOMALO
$alert
    .removeClass("alert-secondary alert-success")
    .addClass("alert-warning")
    .show();

$descrizione.html(
    '<i class="fa-solid fa-triangle-exclamation"></i> ' +
    '<strong>Attenzione!</strong> ' +
    'Costo carburante: ' +
    '<strong>' +
    costoFormattato +
    ' €/litro</strong>' +
    '<br>' +
    '<small>Verificare i litri e l\'importo inseriti.</small>'
);
}
</script>


<script type="text/javascript">


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
    });  
});
</script>


</body>

</html>