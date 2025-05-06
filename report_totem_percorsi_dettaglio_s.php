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

?>

<div id="tabella1">
            
        <h4>Dettaglio percorso <?php echo $_GET['id'];?> del <?php echo $_GET['datalav'];?></h4>




        <div class="table-responsive-sm">

                  <!--div id="toolbar">
        <button id="showSelectedRows" class="btn btn-primary" type="button">Crea ordine di lavoro</button>
      </div-->
    
      <div id="toolbar1" class="isDisabled"> 
      <!--a tarGET="_new" class="btn btn-primary btn-sm"
         href="./export_consuntivazione_ekovision.php"><i class="fa-solid fa-file-excel"></i> Esporta xlsx completo</a-->
      </div>
				<table  id="totem_percorsi_dettaglio_s" class="table-hover" 
        idfield="tappa" 
        data-show-columns="true"
        data-show-search-clear-button="true"   
        data-show-export="false" 
				data-search="false" data-show-print="false"  
        data-virtual-scroll="false"
        data-show-pagination-switch="false"
				data-pagination="false" data-page-size=75 data-page-list=[10,25,50,75,100,200,500]
				data-side-pagination="false" 
        data-show-refresh="true" data-show-toggle="true"
        data-show-columns="true"
				data-filter-control="true"
        data-sort-select-options = "true"
        data-url="./tables/report_totem_percorsi_dettaglio_s.php?id=<?php echo $_GET['id'];?>&datalav=<?php echo $_GET['datalav'];?>" 
        data-toolbar="#toolbar1" 
        data-show-footer="false"
        >
        
        
<thead>



 	  <tr>
        <!--th data-checkbox="true" data-field="id"></th-->  
        <!--th data-field="state" data-checkbox="true" ></th-->  
        <th data-field="tappa" data-sortable="true" data-visible="false" data-filter-control="input">Tappa</th>
        <th data-field="nome_via" data-sortable="true" data-visible="true" data-filter-control="input">Via</th>
        <th data-field="tratto" data-sortable="true" data-visible="true" data-filter-control="input">Nota via</th>
        <th data-field="punteggio" data-sortable="true" data-visible="true" data-filter-control="select">% completamento</th> 
        <th data-field="causale" data-sortable="true" data-visible="true" data-filter-control="select">Causale</th> 
        <th data-field="operatore" data-sortable="true" data-visible="true" data-filter-control="select">Operatore</th>
    </tr>
</thead>
</table>





<script type="text/javascript">



var $table = $('#totem_percorsi_dettaglio_s');

$(function() {
    $table.bootstrapTable();
});
  



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