<?php
session_start();

# definisco quanto andare indietro con i giorni
$giorni_indietro=20;

//require_once('./check_utente.php');

if ($_SESSION['test']==1) {
    require_once('./conn_test.php');
} else {
    require_once('./conn.php');
}

// Faccio il controllo su SIT (sempre produzione non test)

$query_role="SELECT  su.id_user, sr.id_role, sr.\"name\" as \"role\",
su.email
FROM util.sys_users su
join util.sys_roles sr on sr.id_role = su.id_role  
where su.\"name\" ilike $1 and su.id_user>0;";
$result_n = pg_prepare($conn, "my_query_navbar1", $query_role);
if (pg_last_error($conn)){
    echo pg_last_error($conn);
}
$result_n = pg_execute($conn, "my_query_navbar1", array($_SESSION['username']));
if (pg_last_error($conn)){
    echo pg_last_error($conn);
}
$check_SIT=0;
while($r = pg_fetch_assoc($result_n)) {
  $role_SIT=$r['role'];
  $id_role_SIT=(int)$r['id_role'];
  //$id_user_SIT=$r['id_user'];
  $_SESSION['id_user']=$r['id_user'];

  $mail_SIT=$r['email'];
  $check_SIT=1;
}

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
  $check_edit_piazzola=1  ?? 0;
}

if (in_array($role_SIT, $ruoli_edit)) {
  $check_edit=1 ?? 0;
}

if (in_array($role_SIT, $ruoli_superedit)) {
  $check_superedit=1 ?? 0;
}


if ($check_modal!=1){



?>
<div class="navbar-header">
<div id="intestazione" class="banner"> <div id="banner-image">
<h3>  <a class="navbar-brand link-light" href="./index.php">
    <img class="pull-left" src="img\amiu_small_white.png" alt="SIT" width="85px">
    <span> Backoffice consuntivazione 
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
            <a class="dropdown-item" href="./backoffice_racc_ext.php">Raccolta</a>
            <a class="dropdown-item" href="./backoffice_spazz_ext.php">Igiene (Spazzamento / lavaggio / aree verdi, etc)</a>         
          </div>
        </li>
        <?php } ?>
        <?php if ($check_superedit > 0) { ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#"  role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-controls="navbarDropdown1">
          Pagine totem (per soli amministratori)
          </a>
          <div class="dropdown-menu" id="navbarDropdown1" aria-labelledby="navbarDropdown1">
            <a class="dropdown-item" target="_blank" href="./consuntivazione_raccolta.php?operatore=9999">Totem raccolta</a>
            <a class="dropdown-item" target="_blank" href="./consuntivazione_spazzamento.php?operatore=9999">Totem spazzamento</a> 
            <a class="dropdown-item" target="_blank" href="./selezione_servizi.php?operatore=9999">Selezione servizi</a>        
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
        


        <?php 
        $query_utente="select su.\"name\", su.email, 
          concat(sr.name, ' - ', sr.description) as ruolo, 
          case
            when min(suu.id_ut) = -1 then 'Tutte le UT/Rimesse'
            else string_agg(u.descrizione, ', ') 
          end uts, 
          case
            when min(suu.id_ut) = -1 then (select string_agg(distinct id_uo::text, ', ') from anagrafe_percorsi.cons_mapping_uo cmu1 ".$filter_totem_ok.")
            else string_agg(cmu.id_uo::text, ', ') 
          end id_uos, 
          min(suu.id_ut) as controllo 
          from util.sys_users su 
          join util.sys_roles sr on sr.id_role = su.id_role 
          left join util.sys_users_ut suu on suu.id_user = su.id_user 
          left join topo.ut u on u.id_ut = suu.id_ut 
          left join anagrafe_percorsi.cons_mapping_uo cmu on cmu.id_uo_sit = u.id_ut
          where  su.\"name\" ilike $1 and su.id_user > 0
          group by su.\"name\", su.email, sr.name, sr.description";

          //echo $query_utente;
          $result1 = pg_prepare($conn, "my_queryUser", $query_utente);
          $result1 = pg_execute($conn, "my_queryUser", array($_SESSION['username']));


          while($r1 = pg_fetch_assoc($result1)) {
            $mail_user=$r1['email'];
            $profilo=$r1['ruolo'];
            $controllo =$r1['controllo'];
            
            $uts=$r1['uts'];
            $uos=$r1['id_uos'];
            $_SESSION['id_uos']=$r1['id_uos'];

          }
        ?>
        <div class="dropdown-menu" style="left: auto" id="navbarDropdown4" aria-labelledby="navbarDropdown4">
          <ul>
            <li><b>Mail: </b><?php echo $mail_user?></li>
            <li><b>Profilo: </b><?php echo $profilo?></li>
            <li><b>UT/Rimesse: </b><?php echo $uts?></li>
            <hr>
            <li><b>Check ruolo SIT:</b><?php echo $role_SIT; ?></li>
            <li><b>Check superedit: </b><?php echo $check_superedit; ?></li>
          </ul>
        <hr>
          In caso di modifiche fare scrivere dal proprio responsabile a assterritorio@amiu.genova.it 
        <hr>
          <a class="dropdown-item" href="./logout.php">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>   
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

  ?>
      <h4><i class="fa-solid fa-triangle-exclamation"></i> Ambiente di TEST!</h4>
  <?php
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

<?php
  if ($role_SIT=='VIEW' && $controllo== -1) {
    die(' Utente con solo permessi di lettura e nessun limite sulle UT.
    <br> Operazioni di consuntivazione non permesse.
    <br> Contattare l\'amministratore di sistema per eventuali modifiche ai permessi.
    <br><br><a href="./logout.php" class="btn btn-info"> Logout </a>');
  }
?>
