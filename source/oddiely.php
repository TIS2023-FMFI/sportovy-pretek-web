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
  hlavicka("Oddiely");
  ?>
  <section>
  <div id="tab_platby">
    <form method="post">
      <h2>Oddiely</h2>
      <table border="1" style="width:100%">
        <tr>
          <td class="prvy"></td>
          <td class="prvy">ID oddielu</td>
          <td class="prvy">Názov</td>
        </tr>
        <?php
        $pl = new PRETEKY();
        PRETEKY::vypis_zoznam_oddiely();
        ?>
      </table>
      <p>
        <a href="oddiely_new.php"><input type="button" value="Nový oddiel"></a>
        <input name="del" type="submit" id="del" onclick="return confirm('Naozaj chcete vymazať oddiel?');" value="Vymazať oddiel">
      </p>
    </form>
    <br><br> <br> <br>
</div>
<br>
<?php



if ((isset($_POST['del']) && (isset($_POST['incharge'])))){
  // PHP throws a fit if we try to loop a non-array
  if(is_array($_POST['incharge'])){
    foreach($_POST['incharge'] as $val){
      PRETEKY::vymaz_oddiel($val);
        echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
      }
    }
  }



 ?>
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

?>
</html>
