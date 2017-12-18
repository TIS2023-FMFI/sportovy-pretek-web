<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
?>

<!DOCTYPE HTML>
<html>
<?php
if (!isset($_SESSION['admin']) || !$_SESSION['admin']){
  echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
}
else{
  hlavicka("Nová kategória");
  ?>
  <section>

  <?php


  if ((isset($_POST['posli'])) && (over ($_POST['nazov']))) {
    PRETEKY::pridaj_kategoriu($_POST['nazov']);
    echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=kategorie.php">';
  }


    ?>
    <div id="novy_pouzivatel">
  	  <form method="post" enctype="multipart/form-data">
        <h2>Pridať kategóriu</h2>
  	    <table>
        <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
          <tr>
      		  <td><label for="nazov">Názov:</label></td>
  		      <td><input type="text" name="nazov" id="nazov" size="30" value="<?php if(isset($_POST['nazov'])){echo $_POST['nazov'];} ?>"></td>
  		    </tr>
        </table>
  	  	<p id="buttons">
          <input type="submit" name="posli" value="Pridaj" onclick="console.log('log')">
        </p>
      </form>
    </div>

  </section>
  <br><br>

  <script src="js/jquery.js"></script>
  <script src="js/jquery.datetimepicker.js"></script>
  <script>
  $('#datetimepicker').datetimepicker({
    dayOfWeekStart : 1,
    format:'d-m-Y H:i',
    lang:'sk',
    showAnim: "show"
  });
  </script>
  <?php
  paticka();
}
?>
</html>
