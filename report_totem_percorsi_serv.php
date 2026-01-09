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
$query_percorso = "select distinct spe.descrizione as desc_percorso
from servizi.servizi_per_ekovision spe 
where spe.id_percorso = $1
and to_date($2, 'DD/MM/YYYY') between spe.data_inizio_validita and spe.data_fine_validita";

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
    


    <div class="row mb-3">
  
        <div class="col-2">
            <label for="exampleInputEmail1" class="form-label">Codice percorso</label>
            <input type="text" class="form-control" id="cod_percorso" readonly value="<?php echo $id; ?>"  >
        </div>

        <div class="col-10">
            <label for="exampleInputEmail1" class="form-label">Descrizione percorso</label>
            <input type="text" class="form-control" id="descrizione" readonly value="<?php echo $desc_percorso; ?>"  >
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-auto">
            <label for="exampleInputEmail1" class="form-label">Data</label>
            <input type="text" class="form-control" id="datalav" readonly value="<?php echo $datalav; ?>"  >
        </div>

        <div class="col-auto">
            <label for="exampleInputEmail1" class="form-label">Matricola (Badge = <?php echo $consuntivatore; ?>) <?php echo $op; ?></label>
            <input type="text" class="form-control" id="consuntivatore" readonly value="<?php echo $matricola; ?>"  >
        </div>
    
         <div class="col-auto" id="box_manisone">


    <?php
    $query_mansioni="select ssu.id_squadra, s.id_qualifica, s.desc_qualifica 
from servizi.servizi_squadre_ut ssu 
left join servizi.squadre s on s.id_squadra = ssu.id_squadra
where ssu.id_ut = $1
and ssu.id_percorso = $2
and to_date($3, 'DD/MM/YYYY') between ssu.data_inizio_validita  and ssu.data_fine_validita ";

    $result = pg_prepare($conn_hub, "query_mansioni", $query_mansioni);
    if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    }
    $result = pg_execute($conn_hub, "query_mansioni", array($id_uo, $id, $datalav));
    if (pg_last_error($conn_hub)){
    echo pg_last_error($conn_hub);
    }

    
    // Carico tutte le righe in un array
$righe = pg_fetch_all($result);

// Conto quante sono
$count = is_array($righe) ? count($righe) : 0;

$i = 1;

if ($count > 0) {
    foreach ($righe as $row) {

        // Se c'è una sola riga → seleziona automaticamente
        $checked = ($count === 1 && $i === 1) ? "checked" : "";

        ?>
        <div class="form-check form-check-inline">
            <input class="form-check-input"
                   type="radio"
                   name="mansione"
                   id="mansione<?php echo $i ?>"
                   value="<?php echo $row['id_qualifica'] ?>"
                   <?php echo $checked ?>>
            <label class="form-check-label" for="mansione<?php echo $i ?>">
                <?php echo $row['desc_qualifica'] ?>
            </label>
        </div>
        <?php

        $i++;
    }
}
    ?>
        </div>
    </div>



    <div class="row mb-3">
    <div class="col-4">
    <label class="form-label"><strong>Guida un mezzo?</strong></label><br>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="guida_mezzo" id="guida_si" value="SI">
        <label class="form-check-label" for="guida_si">Sì</label>
    </div>

    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="guida_mezzo" id="guida_no" value="NO">
        <label class="form-check-label" for="guida_no">No</label>
    </div>
</div>

<!-- CAMPO SPORTELLO (inizialmente nascosto) -->
<div class="col-8" id="box_sportello" style="display:none;">
    <label for="sportello_input" class="form-label">Inserisci lo sportello</label>
    <input type="text" class="form-control" id="sportello_input" 
        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 5);" 
        name="sportello_input" maxlength="5">
    <div class="form-text">Inserisci un numero di massimo 5 cifre.</div>
    <!--div id="keypadDisplay"
                     class="form-control mb-3 fs-3 text-center"
                     style="letter-spacing: 4px;"
                     readonly></div-->

                <div class="row g-2">
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">1</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">2</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">3</button></div>

                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">4</button></div>
                    <div class="col-4"><button type="button"class="btn btn-secondary w-100 keypad-btn">5</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">6</button></div>

                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">7</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">8</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">9</button></div>

                    <div class="col-4"><button type="button" class="btn btn-dark w-100" id="keypad-clear">←</button></div>
                    <div class="col-4"><button type="button" class="btn btn-secondary w-100 keypad-btn">0</button></div>
                    <div class="col-4"><button type="button" class="btn btn-success w-100" id="keypad-ok">Verifica</button></div>
                </div>

            </div>
</div>

<div class="row mb-3" id="result_mezzo">
<!-- Risultato verifica mezzo -->    
</div>

<!-- BOTTONE SALVA (inizialmente nascosto) -->
<button type="button" id="btn_salva" class="btn btn-primary" style="display:none;">Invia dati</button>

<!-- SPAZIO DEDICATO ALL'OUTPUT -->
<div id="ConsOutput" class="text-center">

</div>





<script type="text/javascript">
$(document).ready(function () {

    // Quando l'utente seleziona SI / NO
    $("input[name='guida_mezzo']").change(function () {

        if ($("#guida_si").is(":checked")) {

            // Mostro il campo sportello + required
            $("#box_sportello").slideDown();
            $("#sportello_input").attr("required", true);

            // Mostro il tasto salva
            $("#btn_salva").fadeIn();

        } else if ($("#guida_no").is(":checked")) {

            // Nascondo il campo sportello e tolgo required
            $("#box_sportello").slideUp();
            $("#sportello_input").removeAttr("required");

            // Mostro comunque il tasto salva
            $("#btn_salva").fadeIn();
        }
    });

});
      




let keypadValue = "";

$(document).ready(function () {

    // Apri tastierino quando clicchi sul campo
    /*$("#sportello_input").on("focus click", function () {
        keypadValue = $(this).val();
        $("#keypadDisplay").text(keypadValue);
        $("#keypadModal").modal("show");
    });*/

    // Click sui tasti numerici
    $(".keypad-btn").click(function () {
        if (keypadValue.length < 5) {
            keypadValue += $(this).text();
            console.log(keypadValue);
            $("#sportello_input").val(keypadValue);
        }
    });

    // Tasto Cancel / Backspace
    $("#keypad-clear").click(function () {
        keypadValue = keypadValue.slice(0, -1);
        $("#sportello_input").val(keypadValue);
    });

    // Tasto OK → salva nel campo e chiude
    /*$("#keypad-ok").click(function () {
        $("#sportello_input").val(keypadValue);
        $("#keypadModal").modal("hide");
    });*/
    
    /*$("#keypad-ok").click(function (event) {
        event.stopPropagation();   // 🔥 impedisce di chiudere il modal padre
        event.preventDefault();    // 🔥 evita submit involontari

        $("#sportello_input").val(keypadValue);
        $("#keypadModal").modal("hide");
    });*/

});

$("#keypad-ok").on("click", function () {
    sportello = $("#sportello_input").val().trim();
    // --- CHIAMATA AJAX ---
    $.ajax({
        url: "backoffice/verifica_mezzo.php",
        type: "POST",
        data: {
            sportello: sportello
        },
        success: function (response) {
            closeToast();
            let messaggio= response;

            $("#result_mezzo").html(messaggio).fadeIn("slow");
            //showToast(messaggio, "success", false);
        },
        error: function (xhr, status, error) {

            showToast("Mezzo non trovato!", "danger", true);
            console.error("AJAX error:", status, error);
        }
    });

});



$("#btn_salva").on("click", function () {

    let cod_percorso = $("#cod_percorso").val().trim();
    let datalav = $("#datalav").val().trim();
    let consuntivatore = $("#consuntivatore").val().trim();

    let mansione = $("input[name='mansione']:checked").val() || "";

    let guida = $("#guida_si").is(":checked") ? "t" : 
                $("#guida_no").is(":checked") ? "f" : "";

    let sportello = "";
    if (guida === "t") {
        sportello = $("#sportello_input").val().trim();
    }

    // --- VALIDAZIONE BASICA ---
    if (!cod_percorso || !datalav || !consuntivatore) {
        showToast("Compilare tutti i campi obbligatori.", "danger", true);
        return;
    }
    if (!mansione) {
        showToast("Selezionare una mansione.", "danger", true);
        return;
    }
    if (guida === "") {
        showToast("Indicare se la guida è SI o NO.", "danger", true);
        return;
    }
    if (guida === "t" && sportello === "") {
        showToast("Inserire il numero dello sportello.", "danger", true);
        return;
    }

    // --- CHIAMATA AJAX ---
    $.ajax({
        url: "backoffice/registra_servizio.php",
        type: "POST",
        data: {
            cod_percorso: cod_percorso,
            datalav: datalav,
            consuntivatore: consuntivatore,
            mansione: mansione,
            guida: guida,
            sportello: sportello
        },
        success: function (response) {

            closeToast();
            let messaggio= response;
                $("#ConsOutput").html(
                '<br><div class="alert alert-warning alert-animated" role="alert">' +
                messaggio+
                '</div>'
            ).fadeIn("slow");
            showToast(messaggio, "success", false);
        },
        error: function (xhr, status, error) {

            showToast("Errore durante la registrazione del servizio!", "danger", true);
            console.error("AJAX error:", status, error);
        }
    });

});





</script>


      	










</div>
</div>

<?php
require_once('req_bottom.php');
//require('./footer.php');
?>



</body>

</html>