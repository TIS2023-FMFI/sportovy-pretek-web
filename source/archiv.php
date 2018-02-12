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
<!--
<div id="zoz_pretekov_uzivatel">
  <h2>Zoznam tréningov</h2>
  <?php
 /*  if(isset($_SESSION['zmazany'])){
      echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek '.$_SESSION['zmazany'].' bol zmazaný.</strong>';
      unset($_SESSION['zmazany']);
    }
    if(isset($_GET['aktiv'])){
      PRETEKY::aktivuj($_GET['id']);
      $pr = new PRETEKY();
      $pr=PRETEKY::vrat_pretek($_GET["id"]);
      if($pr->AKTIV == 1){
        echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek '.$pr->NAZOV.' bol aktivovaný.</strong>';
      }
      else{
        echo '<strong  style="color:green; font-size:15px; margin-left:30px;">Pretek '.$pr->NAZOV.' bol deaktivovaný.</strong>';
      }
    }
    if(isset($_GET['zmaz'])){
      $pr = new PRETEKY();
      $pr=PRETEKY::vrat_pretek($_GET["id"]);
      $nazov = $pr->NAZOV;
      PRETEKY::vymaz_pretek($_GET['id']);
      echo '<meta http-equiv="refresh" content="0; URL=archiv.php">';
      $_SESSION['zmazany'] = $nazov;
    }
  ?>
  <table border="1" id="archiv" class="tablesorter" style="width:100%;">  
    <thead>
      <tr>
        <th class="prvy">Typ tréningu</td>
        <th class="prvy">Dátum konania</td> 
        <th class="prvy">Prihlasovanie do</td>
      </tr>
    </thead>
    <tbody>
      <?php 
        PRETEKY::vypis_archiv();     
      ?>
  </table> 
</div>
<br><br>

?>
<script type="text/javascript" src="sorter/jquery-latest.js"></script>
<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
     $("#archiv").tablesorter({dateFormat: "uk"});
  });
</script> */ 

