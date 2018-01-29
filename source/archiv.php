<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

if (isset($_GET['odhlas'])){
  $_SESSION['admin']=0;
}
?>

<!DOCTYPE HTML>
<html>
<?php
if (isset($_SESSION['admin'])&&$_SESSION['admin']){
  hlavicka("Archív");
}
else{
  echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}?>
<section>
<div style="align-items:center;">
<?php PRETEKY::vypis_roky();?>
</div>
</section>
<?php
paticka();
?>
</html>
