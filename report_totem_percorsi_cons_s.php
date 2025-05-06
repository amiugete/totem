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


        <script type="text/javascript">
        


        $(document).ready(function () {                 
                $('#salva_cons').click(function (event) { 
                  console.log("Bottone salva cliccato");
                  event.preventDefault(); 
                  var selectedRows = getRowSelections();
                  console.log(selectedRows);
                  var selectedItems = '';

                  $.each(selectedRows, function(index, value) {
                    selectedItems += selectedRows[index] + ',';
                  });
                  console.log(selectedItems);
                  
                   /* $.ajax({ 
                        url: 'backoffice/add_elemento.php', 
                        method: 'POST', 
                        data: {'id_piazzola':id_piazzola}, 
                        //processData: true, 
                        //contentType: false, 
                        success: function (response) {                       
                            //alert('Your form has been sent successfully.'); 
                            console.log(response);
                              $("#result_add_<?php echo $r['tipo_elemento'];?>").html(response).fadeIn("slow");
                              setTimeout(function(){// wait for 5 secs(2)
                                location.reload(); // then reload the page.(3)
                            }, 1000);
                                      
                        }, 
                        error: function (jqXHR, textStatus, errorThrown) {                        
                            alert('Your form was not sent successfully.'); 
                            console.error(errorThrown); 
                        } 
                    }); */
                });
              });



        function clickButton() {
            console.log("Bottone salva cliccato");

            var selectedRows = getRowSelections();
            var selectedItems = '';
            
            $.each(selectedRows, function(index, value) {
              selectedItems += value + ',';
            });
            console.log(selectedItems);
            //alert('Ora devo lanciare la funzione che crei l\'ordine di lavoro con i seguenti interventi: ' + selectedItems);
            // prevent form from submitting
            
            
            /*var url ="crea_ordine.php?s="+encodeURIComponent(sq)+"&d="+encodeURIComponent(data_inizio)+"&ii="+encodeURIComponent(selectedItems)+"";
				
            console.log(url);
            // get the URL
            http = new XMLHttpRequest(); 
            http.open("GET", url, true);
            http.send();
            console.log('Verifichiamo lo stato');
            console.log(http.readyState);
            
            $('#interventi').bootstrapTable('refresh', {silent: true});
            $('#odl_ok').toast('show');

            //window.location.href = "ordini.php";
             
            */
            //return false;

        };



      </script>


      <hr>
      <form autocomplete="off" id="prospects_form" action="">
      <div name="conferma2" id="conferma2" class="form-group col-lg-3">
      <button type="submit" id="salva_cons" class="btn btn-primary">
      <i class="fa-solid fa-plus"></i> Salva consuntivazione
      </button>
      </div>
      </form>


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
        data-url="./tables/report_totem_percorsi_cons_s.php?id=<?php echo $_GET['id'];?>&datalav=<?php echo $_GET['datalav'];?>" 
        data-toolbar="#toolbar1" 
        data-show-footer="false"
        data-row-style="rowStyle"
        >
        
        
<thead>



 	  <tr>
        <th data-field="state" data-checkbox="true" data-formatter="stateFormatter"></th>  
        <th data-field="tappa" data-sortable="true" data-visible="false" data-filter-control="input">Tappa</th>
        <th data-field="nome_via" data-sortable="true" data-visible="true" data-filter-control="input">Via</th>
        <th data-field="tratto" data-sortable="true" data-visible="true" data-filter-control="input">Nota via</th>
        <th data-field="check_previsto" data-sortable="true" data-visible="true" data-formatter="causaleForm">Previsto</th>
        <!--th data-field="punteggio" data-sortable="true" data-visible="true" data-filter-control="select">% completamento</th> 
        <th data-field="causale" data-sortable="true" data-visible="true" data-filter-control="select">Causale</th> 
        <th data-field="operatore" data-sortable="true" data-visible="true" data-filter-control="select">Operatore</th-->
    </tr>
</thead>
</table>





<script type="text/javascript">



var $table = $('#totem_percorsi_dettaglio_s');

$(function() {
    $table.bootstrapTable();
});


$table.on('check.bs.table', function (e, row) {
  console.log('Tappa '+ row.tappa+ ' selezionata');
  console.log(e);
  $('#insert_'+row.tappa+'').removeAttr('disabled');
});

$table.on('uncheck.bs.table', function (e, row) {
  console.log('Tappa: '+ row.tappa+ ' rimossa');
  console.log(e);
  $('#insert_'+row.tappa+'').attr('disabled',true);

});


function getRowSelections() {
    return $.map($table.bootstrapTable('getSelections'), 
    function(row, index) {
      console.log(row.tappa);
      var causale = $('select#insert_'+row.tappa+'').find(":selected").val();
      console.log(causale);
      if(!causale){
        alert('Specificare una causale');
      };
      return row.tappa+'-'+causale;
    })
  };

window.stateFormatter = (value, row, index) => {
    if (row.check_previsto === '1') {
      return {
        checked: true
      }
    }
    //return value
  }

  
function rowStyle(row, index) {
  if (row.check_previsto === '1') {
    return {
    classes: 'text-nowrap previsto',
    css: {"background-color": "green", "font-weight": "bold"}
    } 
  } else if (row.check_previsto === '0') {
    return {
      classes: 'text-nowrap non-previsto',
      css: {"background-color": "pink"}
    } 
  }
  };




function causaleForm(value, row, index) {
  if (row.check_previsto === '1') {
    return [
        //'<form action="" autocomplete="off" id="insert_'+row.tappa+'">',
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true"  name="causale" id="causale" required="">',
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
        '<select id="insert_'+row.tappa+'"  class="show-tick form-select" data-live-search="true"  disabled="" name="causale" id="causale" required="">',
        '<option name="causale" value="">Seleziona una causale</option>',  
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