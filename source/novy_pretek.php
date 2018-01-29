<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

if (!isset($_SESSION['admin']) || !$_SESSION['admin']){
  echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}
else{
  $zobraz_form = true;
  if ((isset ($_POST['vytvor'])) && over($_POST['nazov']) && over($_POST['datum']) && over($_POST['deadline'])&& is_array($_POST['incharge']))  {
  //$po->uprav_pretek ($_POST['meno'], $_POST['priezvisko'], $_POST['oscislo'], $_POST['cip'], $_POST['poznamka']);
    $po = new PRETEKY();
    $idPret = $po->pridaj_pretek($_POST['nazov'], $_POST['datum'], $_POST['deadline'], $_POST['poznamka']);
    if(is_array($_POST['incharge'])){
      foreach($_POST['incharge'] as $idKat){
        PRETEKY::pridaj_kat_preteku($idPret,$idKat);
      }
    }
  echo '<meta http-equiv="refresh" content="0; URL=index.php">';
  unset($po);
}

?>
<!DOCTYPE HTML>
<html>
<?php
  hlavicka("Nový pretek");
  if(isset($_GET['novy']) && isset ($_GET['id'])){
      $po = new PRETEKY();
      $po = PRETEKY::vrat_pretek($_GET["id"]);
      ?>
      <section class="uprav_preteky">
      <form method="post" enctype="multipart/form-data">
        <table id="novy_pretek_table">
         <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
         <tr>
          <td><label for="nazov">Názov pretekov</label></td>
          <td><input type="text" name="nazov" id="nazov" size="30" value=""></td>
        </tr>
        <?php if(isset($_POST['datum']) && !over($_POST['datum'])){echo'<tr><td><font color="red">Nevyplnili ste dátum!</font></td></tr>';} ?>
        <tr>
          <td><label for="datetimepicker">Dátum konania</label>  </td>
          <td><input type="text" name="datum" id="datetimepicker" size="30" value=""></td>
        </tr>
        <?php if(isset($_POST['deadline']) && !over($_POST['deadline'])){echo'<tr><td><font color="red">Nevyplnili ste deadline!</font></td></tr>';} ?>
        <tr>
          <td><label for="datetimepicker1">Deadline prihlásenia</label></td>
          <td><input type="text" name="deadline" id="datetimepicker1" size="30" value=""></td>
        </tr>
        <tr>
          <td> <label for="poznamka">Poznámka</label> </td>
          <td> <textarea cols="80" rows="15" name="poznamka" id="poznamka"><?php if(isset($_POST['poznamka'])){echo $_POST['poznamka'];}else{echo $po->POZNAMKA;} ?></textarea></td>
        </tr>
      </table>
      <table><?php
        if(isset($_POST['posli'])&&!isset($_POST['incharge'])){
          echo'<tr><td><font color="red">Musíte zadať aspoň jednu kategóriu!</font></td></tr>';
        }
        PRETEKY::vypis_zoznam_pretek_table(); PRETEKY::vypis_zoznam_ostatne_table(); ?></table>
        <p id="buttons">
      <input type="submit" name="vytvor" value="Vytvor">
    </p>
  </form>
</section>
<?php
  }
else{
?>
<section class="uprav_preteky">
	<form method="post" enctype="multipart/form-data">
	  <table>
      <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
		  <tr>
        <td><label for="nazov">Názov preteku</label></td>
		    <td><input type="text" name="nazov" id="nazov" size="30"> </td>
		  </tr>
      <?php if(isset($_POST['datum']) && !over($_POST['datum'])){echo'<tr><td><font color="red">Nevyplnili ste dátum!</font></td></tr>';} ?>
      <tr>
		    <td><label for="datetimepicker">Dátum konania</label>  </td>
		    <td><input type="text" name="datum" id="datetimepicker" size="30"></td>
		  </tr>
      <?php if(isset($_POST['deadline']) && !over($_POST['deadline'])){echo'<tr><td><font color="red">Nevyplnili ste deadline!</font></td></tr>';} ?>
      <tr>
        <td><label for="datetimepicker1">Deadline prihlásenia</label>    </td>
		    <td><input type="text" name="deadline" id="datetimepicker1" size="30" > </td>
		  </tr>
      <tr>
        <td> <label for="poznamka">Poznámka</label> </td>
        <td> <textarea cols="80" rows="15" name="poznamka" id="poznamka"></textarea></td>
      </tr>
	  </table>
    <!-- kategorie-->
    <table><?php
    if(isset($_POST['posli'])&&!isset($_POST['incharge'])){
      echo'<tr><td><font color="red">Musíte zadať aspoň jednu kategóriu!</font></td></tr>';
    }
    PRETEKY::vypis_vsetky_kategorie_table(); ?>
    </table>
    <p id="buttons">
      <input type="submit" name="vytvor" value="Vytvor">
    </p>
  </form>
</section>
<?php
}
?>

<script src="js/jquery.js"></script>
<script src="js/jquery.datetimepicker.js"></script>
<script>
  $('#datetimepicker').datetimepicker({
  dayOfWeekStart : 1,
  format:'Y-m-d H:i',
  lang:'sk',
  showAnim: "show"
  });
</script>
<script>
  $('#datetimepicker1').datetimepicker({
  dayOfWeekStart : 1,
  format:'Y-m-d H:i',
  lang:'sk',
  showAnim: "show"
  });
</script>
<br><br><br>

<?php
paticka();
}
?>
</html>
