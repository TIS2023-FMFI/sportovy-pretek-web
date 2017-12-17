
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
}
?>

<div id="zoz_pretekov_uzivatel">
  <h2>Zoznam tréningov</h2>
  <?php if(isset($_SESSION['admin'])&&$_SESSION['admin']){?>
    <input name="novy" type="submit" id="novy" onclick="location.href='novy_pretek.php';" value="Nové preteky">
    <input type="submit" onclick="location.href='kategorie.php';" value="Kategórie">
    <input type="submit" onclick="location.href='oddiely.php';" value="Oddiely"> 
  <?php } ?>
  <table border="1" style="width:100%;">  
    <tr>
      <td class="prvy">Typ tréningu</td>
      <td class="prvy">Dátum konania</td> 
      <td class="prvy">Prihlasovanie do</td>
        
      <?php if(isset($_SESSION['admin'])&&$_SESSION['admin']){?>
        <td class="prvy"></td>
        </tr>
        <?php PRETEKY::vypis_archiv();?>
        </table>
        </div>         
        <?php 
        if(isset($_GET['aktiv'])){
          PRETEKY::aktivuj($_GET['id']);
        }
        if(isset($_GET['zmaz'])){
          PRETEKY::vymaz_pretek($_GET['id']);
          echo '<meta http-equiv="refresh" content="0; URL=archiv.php">';
        }
      }
      else{
        echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';?>       
        </table> 
        </div>
        <br><br>
        <?php
      }


paticka();        
?>
</html>
