

<!--link rel="stylesheet" href="https://kit-free.fontawesome.com/releases/latest/css/free.min.css"-->
<link rel="stylesheet" href="./vendor/fontawesome-free-6.1.1-web/css/all.min.css">

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>


<!-- Production version -->
<!--script src="https://unpkg.com/@popperjs/core@2" crossorigin="anonymous"></script-->
  


<link rel='icon' href='./favicon.ico' type='image/x-icon' sizes="16x16">

<!--script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script-->

<!-- Bootstrap Core CSS -->
<link rel="stylesheet" href="./vendor/twbs/bootstrap/dist/css/bootstrap.min.css">


<!--ICONE -->
<!--link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css"-->
<link rel="stylesheet" href="./vendor/twbs/bootstrap-icons/font/bootstrap-icons.css">


<!-- Bootstrap Plugins -->
<!-- BOOTSTRAP TABLE -->
<!--link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.18.3/dist/bootstrap-table.min.css"-->
<link rel="stylesheet" href="./vendor/bootstrap-table/dist/bootstrap-table.min.css">
<link rel="stylesheet" href="./vendor/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.css">
<link rel="stylesheet" href="./vendor/bootstrap-table/dist/extensions/group-by-v2/bootstrap-table-group-by.min.css">


<!-- BOOTSTRAP SELECT -->
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">


<!-- BOOTSTRAP DATEPICKER -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link href="./main.css" rel="stylesheet">

<!--link href="./bootstrap-table-1.18.3/dist/bootstrap-table.css" rel="stylesheet"-->

<!--link href="./bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet"-->


<!--script src="./jquery.js"></script-->
<!-- jQuery -->
<script src="./vendor/jquery/jquery-3.6.0.min.js"></script>



<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<style>
.highcharts-figure,
.highcharts-data-table table {
    min-width: 320px;
    max-width: 660px;
    margin: 1em auto;
}

.highcharts-data-table table {
    font-family: Verdana, sans-serif;
    border-collapse: collapse;
    border: 1px solid #ebebeb;
    margin: 10px auto;
    text-align: center;
    width: 100%;
    max-width: 500px;
}

.highcharts-data-table caption {
    padding: 1em 0;
    font-size: 1.2em;
    color: #555;
}

.highcharts-data-table th {
    font-weight: 600;
    padding: 0.5em;
}

.highcharts-data-table td,
.highcharts-data-table th,
.highcharts-data-table caption {
    padding: 0.5em;
}

.highcharts-data-table thead tr,
.highcharts-data-table tr:nth-child(even) {
    background: #f8f8f8;
}

.highcharts-data-table tr:hover {
    background: #f1f7ff;
}
</style>


<!DOCTYPE html>
<html>
	<head>
		<title>Login</title>
	</head>
	<body data-rsssl=1 data-rsssl=1>
    <div class="banner"> <div id="banner-image"></div> </div>

      <div class="container">
				<h3 style="color:orange">Inserisci credenziali AMIU (utente e password con cui accedi al PC) </h3>
		<form action="" method="post" style="display:inline-block;">
        <div class="form-group">
		<label for="exampleInputEmail1">Utente</label>
			<input type="text" class="form-control" name="user" value="" maxlength="50">
		</div>
		
        <div class="form-group">
			<label for="exampleInputPassword1">Password</label>
			<input type="password" class="form-control" name="password" value="" maxlength="50">
		</div>
		<br>
        <div class="form-group">
			<input type="submit"  class="btn btn-primary" name="ldapLogin" value="Login">
		</div>
		</form>


</div>
        

<!--script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script-->


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js" integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB" crossorigin="anonymous"></script>

<!-- Bootstrap Core JavaScript -->
<script src="./vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
<!--script src="./vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script-->


<!--script src="./vendor/twbs/bootstrap/js/dist/alert.js"></script-->
<script src="./vendor/twbs/bootstrap/js/dist/base-component.js"></script>
<!--script src="./vendor/twbs/bootstrap/js/dist/tooltip.js"></script>
<script src="./vendor/twbs/bootstrap/js/dist/popover.js"></script>
<script src="./vendor/twbs/bootstrap/js/dist/collapse.js"></script>
<script src="./vendor/twbs/bootstrap/js/dist/dropdown.js"></script-->

<!--script src="./vendor/twbs/bootstrap/js/dist/popover.js"></script-->




<!-- Bootstrap Plugins -->
<!--script src="./bootstrap-table-1.18.3/dist/bootstrap-table.js"></script-->


<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>


<script src="./vendor/moment.js"></script>



<!--script src="./bootstrap-datepicker/js/bootstrap-datepicker.js"></script-->
<script src="./vendor/bootstrap-table/dist/bootstrap-table.min.js"></script>

<script src="./vendor/bootstrap-table/dist/locale/bootstrap-table-it-IT.min.js"></script>



<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/FileSaver/FileSaver.min.js"></script>




<script src="./vendor/bootstrap-table/dist/extensions/export/bootstrap-table-export.js" ></script>




<script src="./vendor/bootstrap-table/dist/extensions/print/bootstrap-table-print.min.js" ></script>
<script src="./vendor/bootstrap-table/dist/extensions/filter-control/bootstrap-table-filter-control.js" ></script>
<script src="./vendor/bootstrap-table/dist/extensions/auto-refresh/bootstrap-table-auto-refresh.js"></script>
<script src="./vendor/bootstrap-table/dist/extensions/group-by-v2/bootstrap-table-group-by.min.js"></script>






<!-- Latest compiled and minified JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>

<!-- (Optional) Latest compiled and minified JavaScript translation files -->
<!--script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/i18n/defaults-*.min.js"></script-->

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



<script>
function printClass(className) {
	//it is an array so i using only the first element
     var printContents = document.getElementsByClassName(className)[0].innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}


// funzione per stampa al volo 
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}


</script>



<!-- Sticky Footer -->
<footer class="sticky-footer">
<div class="text-center">
    
<span class="copyright">
<hr>
Applicativo realizzato da <a href="https://www.amiu.genova.it" target="_blank">AMIU Genova SPA</a> (Gestione applicativi SIGT) 
e distribuito con licenza open source GNU GPL 3.0. <br>Il codice dell'applicazione è disponibile su 
<a href="https://github.com//amiugete/bilaterale" targer="_new"> <i class="fab fa-github"></i> github</a>. 
In caso di problemi contattare l'<a href="mailto:assterritorio@amiu.genova.it">amministratore di sistema</a><br>
<a href="https://www.amiu.genova.it" target="_blank"><img style="max-width:200px;" class="rounded" src="./img/logo_amiu.jpg" alt=""></a>
</span>
</div>
</footer>


    </body>
</html>
