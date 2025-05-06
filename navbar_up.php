<?php
session_start();
//require_once('./check_utente.php');

// Faccio il controllo su SIT



$check_SIT=1;

//echo "<script type='text/javascript'>alert('$check_SIT');</script>";


if ($check_SIT==0){
  if ($check_modal!=1){
  redirect('login.php');
  exit(0);
  } else {
    echo 'Problema autenticazione';
  }
}

$check_edit_piazzola=0;

$check_edit=0; # edit dei percorsi 

$check_superedit=0; # permessi privilegiati


$ruoli_edit_piazzola=array('USER', 'UT', 'IT', 'ADMIN', 'SUPERUSER');
$ruoli_edit=array('UT', 'IT', 'ADMIN', 'SUPERUSER');
$ruoli_superedit=array('IT','ADMIN', 'SUPERUSER');

if (in_array($role_SIT, $ruoli_edit_piazzola)) {
  $check_edit_piazzola=1;
}

if (in_array($role_SIT, $ruoli_edit)) {
  $check_edit=1;
}

if (in_array($role_SIT, $ruoli_superedit)) {
  $check_superedit=1;
}


if ($check_modal!=1){



?>
<div class="navbar-header">
<div id="intestazione" class="banner"> <div id="banner-image">
<h3>  <a class="navbar-brand link-light" href="./index.php">
    <img class="pull-left" src="img\amiu_small_white.png" alt="SIT" width="85px">
    <span>Backoffice 
    <?php 
    if ($_SESSION['test']==1) {
       echo "(ambiente di TEST)";
    }
    ?>
    </span> 
  </a> 
</h3>
</div> 
</div>
<nav class="navbar navbar-sticky-top navbar-expand-lg navbar-light" id="main_navbar">
  <div class="container-fluid">
    <!--a class="navbar-brand" href="#">
    <img class="pull-left" src="img\amiu_small_white.png" alt="SIT" width="85px">
    </a-->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
    aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <!--li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li-->
        <?php if ($id_role_SIT >= 0) { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-controls="navbarDropdown1">
          Consuntivazioni
          </a>
          <div class="dropdown-menu" id="navbarDropdown1" aria-labelledby="navbarDropdown1">
            <a class="dropdown-item" href="./backoffice_spazz_ext.php">Spazzamento / lavaggio</a>
          </div>
        </li>
        <?php } ?>
        
        
        
      </ul>
      
      <!--div class="collapse navbar-collapse flex-grow-1 text-right" id="myNavbar">
        <ul class="navbar-nav ms-auto flex-nowrap"-->
        <span class="navbar-light">
        <!--li class="nav-item dropdown"-->
        <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" 
        aria-expanded="false" aria-controls="navbarDropdown4">

          <i class="fas fa-user"></i> Connesso come <?php echo $_SESSION['username'];?> (
            <?php 
            echo '<i class="fa-solid fa-pencil"></i>';
            ?>)
          </a>
        
        <div class="dropdown-menu" style="left: auto" id="navbarDropdown4" aria-labelledby="navbarDropdown4">
          <ul>
            <li><b>Mail: </b></li>
            <li><b>Profilo: </b></li>
            <li><b>UT/Rimesse: </b></li>
            <hr>
            
          </ul>
        <hr>
          In caso di modifiche fare scrivere dal proprio responsabile a assterritorio@amiu.genova.it    
        </div>


        <!--/li-->
        </span>

    </div>
  </div>
</nav>
<?php 
if ($_SESSION['test']==1) {
?> <div> <?php

$conto_underscore=count(explode("_", basename($_SERVER['PHP_SELF'])));

  //if (count(explode("_", basename($_SERVER['PHP_SELF'])))> 1) { 
    if (explode("_", basename($_SERVER['PHP_SELF']))[$conto_underscore-1] == 'sovr.php'){ 
      ?>
      <h4><i class="fa-solid fa-triangle-exclamation"></i> Ambiente di TEST!</h4>
      <?php
    } else {
      ?>
      <h4><i class="fa-solid fa-triangle-exclamation"></i> Ambiente di TEST ma dati in esercizio!</h4>
      <?php
    }
  /*} else {

?>
 <h4><i class="fa-solid fa-triangle-exclamation"></i> Ambiente di TEST ma dati in esercizio!</h4>
<?php
  } */

  
// TEST e DEBUG COOKIES
/*
foreach ($_COOKIE as $key=>$val)
{
  echo $key.' is '.$val."<br>\n";
}
echo ' session username= '. $_SESSION['username']."<br>";
echo ' session expire= '. $_SESSION['expire']."<br>";
echo 'time = ' .time()."<br>";*/
?>
</div>
</div>
<hr>
<?php } // check_modal 
 } ?>

<script>
  document.addEventListener("DOMContentLoaded", function(){
// make it as accordion for smaller screens
if (window.innerWidth < 992) {

  // close all inner dropdowns when parent is closed
  document.querySelectorAll('.navbar .dropdown').forEach(function(everydropdown){
    everydropdown.addEventListener('hidden.bs.dropdown', function () {
      // after dropdown is hidden, then find all submenus
        this.querySelectorAll('.submenu').forEach(function(everysubmenu){
          // hide every submenu as well
          everysubmenu.style.display = 'none';
        });
    })
  });

  document.querySelectorAll('.dropdown-menu a').forEach(function(element){
    element.addEventListener('click', function (e) {
        let nextEl = this.nextElementSibling;
        if(nextEl && nextEl.classList.contains('submenu')) {	
          // prevent opening link if link needs to open dropdown
          e.preventDefault();
          if(nextEl.style.display == 'block'){
            nextEl.style.display = 'none';
          } else {
            nextEl.style.display = 'block';
          }

        }
    });
  })
}
// end if innerWidth
}); 
// 
</script>