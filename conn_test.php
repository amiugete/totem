<?php 
$conn = pg_connect("host=amiupostgres port=5432 dbname=sit_test user=gisamiu password=gisamiu");
if (!$conn) {
        die('<br>Could not connect to DB PostgreSQL, please contact the administrator.');
}


$url_sit="http://amiupostgres/SIT_TEST";

$url_api_chiusura="http://amiupostgres/SIT_API_TEST/api/piazzole/";


$url_eliminazione_percorso="http://amiupostgres/SIT_API_TEST/api/percorsi/removeastapercorso/";


$titolo_app = 'APPLICATIVO SIT - PASSAGGIO A BILATERALE (<b style="color:red">Versione di test</b>)'; 



include 'ldap.php';
include 'jwt.php';
?>
