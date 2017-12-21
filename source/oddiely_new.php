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
  hlavicka("Novy oddiel");
  ?>
  <section>

<br>
<?php


if ((isset($_POST['posli'])) && (over ($_POST['nazov']))) {
   PRETEKY::pridaj_oddiel($_POST['nazov']);
   echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=oddiely.php">';
}


?>
  <div id="novy_pouzivatel">
	  <form method="post" enctype="multipart/form-data">
      <h2>Pridať oddiel</h2>
	    <table>
        <?php if(isset($_POST['nazov']) && !over($_POST['nazov'])){echo'<tr><td><font color="red">Nevyplnili ste názov!</font></td></tr>';} ?>
        <tr>
          <td><label for="nazov">Názov:</label></td>
		      <td><input type="text" name="nazov" id="nazov" size="30" value="<?php if(isset($_POST['nazov'])){echo $_POST['nazov'];} ?>"></td>
		    </tr>
      </table>
	  	<p id="buttons">
        <input type="submit" name="posli" value="Pridaj">
		  </p>
    </form>
  </div>

</section>

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
