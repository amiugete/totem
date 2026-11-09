<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../carica_env.php';
require_once '../conn_ok.php';



// =====================================================
// RISPOSTA JSON
// =====================================================

header('Content-Type: application/json; charset=utf-8');


// =====================================================
// RECUPERO DATI POST
// =====================================================
 

$matricola = $_POST['matricola'] ?? '';

if ($matricola === '') {
    $matricola = '0';
}
// default a 0 se badge non riconosciuto

$data_percorsi  = $_POST['datalav'] ?? '';
$uo             = $_POST['ut'] ?? '';

$sportello      = $_POST['sportello'] ?? '';
$litri          = $_POST['litri'] ?? '';
$importo        = $_POST['importo'] ?? '';
$km        =    $_POST['km'] ?? '';

// =====================================================
// CONTROLLO CAMPI OBBLIGATORI
// =====================================================

if (
    $matricola === '' ||
    $data_percorsi === '' ||
    $uo === '' ||
    $sportello === '' ||
    $litri === '' ||
    $importo === ''
) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Dati mancanti --> codice='.$matricola.' data = '.$data_percorsi.' uo ='.$uo.' sport = '.$sportello.'litri='.$litri.' importo = '.$importo.''
    ]);

    exit;
}


// =====================================================
// CONVERSIONE NUMERICI
// =====================================================

$litri_num = filter_var(
    $litri,
    FILTER_VALIDATE_FLOAT
);

$importo_num = filter_var(
    $importo,
    FILTER_VALIDATE_FLOAT
);


if (
    $litri_num === false ||
    $importo_num === false
) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Litri o importo non validi.'
    ]);

    exit;
}


// =====================================================
// CONTROLLO VALORI POSITIVI
// =====================================================

if (
    $litri_num <= 0 ||
    $importo_num <= 0
) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Litri e importo devono essere maggiori di zero.'
    ]);

    exit;
}


// =====================================================
// LIMITI MASSIMI
// =====================================================

// Imposta qui i tuoi valori reali
$max_litri = 9999.99;
$max_importo = 99999.99;


if ($litri_num > $max_litri) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Il numero di litri supera il massimo consentito.'
    ]);

    exit;
}


if ($importo_num > $max_importo) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'L\'importo supera il massimo consentito.'
    ]);

    exit;
}


// =====================================================
// CONTROLLO SPORTELLO
// =====================================================

// Massimo 5 cifre
if (!preg_match('/^[0-9]{1,5}$/', $sportello)) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Sportello non valido.'
    ]);

    exit;
}


// =====================================================
// CONTROLLO DECIMALI
// =====================================================

if (
    preg_match('/\.(\d+)$/', $litri) &&
    strlen(preg_replace('/^.*\./', '', $litri)) > 2
) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'I litri possono avere al massimo due cifre decimali.'
    ]);

    exit;
}


if (
    preg_match('/\.(\d+)$/', $importo) &&
    strlen(preg_replace('/^.*\./', '', $importo)) > 2
) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'L\'importo può avere al massimo due cifre decimali.'
    ]);

    exit;
}


// =====================================================
// CONVERSIONE DATA
// =====================================================

// Il browser invia dd/mm/yyyy.
// PostgreSQL preferisce yyyy-mm-dd.

$data_obj = DateTime::createFromFormat(
    'd/m/Y',
    $data_percorsi
);

if (!$data_obj) {

    http_response_code(400);

    echo json_encode([
        'ok' => false,
        'message' => 'Data rifornimento '.$data_percorsi.' non valida.'
    ]);

    exit;
}

$datalav = $data_obj->format('Y-m-d');


// =====================================================
// COSTO AL LITRO
// =====================================================

// Calcolato dal server, NON ci fidiamo
// del valore eventualmente inviato dal browser.

$costo_litro =
$importo_num / $litri_num;


// =====================================================
// INSERT
// =====================================================

try {




// upsert
$insert_query = "INSERT INTO servizi.carburante_rifornimenti (
    codice, sportello,
    id_ut, datalav,
    litri, importo, chilometri
) VALUES 
(   
    $1, $2,
    $3::int, to_date($4, 'YYYY-MM-DD'),
    $5::numeric, $6::numeric, $7::numeric
);";



$result1 = pg_prepare($conn_hub, "insert_query", $insert_query);
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    throw new Exception(pg_last_error($conn_sit));
}

$result1 = pg_execute($conn_hub, "insert_query", array(
    $matricola,
    $sportello,
    $uo,
    $datalav,
    $litri,
    $importo, 
    $km
    ));
//echo  pg_last_error($conn_hub);
if (pg_last_error($conn_hub)){
    throw new Exception(pg_last_error($conn_sit));
}


if ($result1 === false) {

        throw new Exception(
            pg_last_error($conn_hub)
        );
    }


    // =================================================
    // RISPOSTA OK
    // =================================================

    http_response_code(200);

    echo json_encode([
        'ok' => true,
        'message' => 'Rifornimento registrato correttamente.'
    ]);

    exit;


} catch (Exception $e) {

    // Log server
    /*error_log(
        'Errore salvataggio rifornimento: ' .
        $e->getMessage()
    );*/


    http_response_code(500);

    echo json_encode([
        'ok' => false,
        'message' => 'Errore durante il salvataggio del rifornimento.'. $e->getMessage() 
    ]);

    exit;
}
?>