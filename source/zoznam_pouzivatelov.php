<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

$zobraz_form = false;

$po = new POUZIVATELIA();

if (isset($_POST['del'])&& isset($_POST['id_user'])){
  POUZIVATELIA::vymaz_pouzivatela($_POST['id_user']);
}

if (isset ($_POST['novy'])){
    $zobraz_form = true;
}

if (isset ($_POST['stop'])){
    $zobraz_form = false;
}


if ((isset ($_POST['posli'])) && 
    
    (isset ($_POST['meno'])  && 
    isset ($_POST['priezvisko']) &&
    isset ($_POST['oddiel']) && 
    isset ($_POST['oscislo']) &&
    isset ($_POST['cip']) && 
    isset ($_POST['poznamka'])) )  { 


 $zobraz_form = false;
   $po->pridaj_pouzivatela ($_POST['meno'], $_POST['priezvisko'],$_POST['oddiel'], $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'], $_POST['uspech']);
  
  
    
}
?>

<!DOCTYPE HTML>

<html>

<?php hlavicka("Členovia klubu");?>
  
<section id="vsetci_pouzivatelia">
<form method=post>
  <input type=submit name=novy id=novy value="Pridať člena">
</form> 
<?php
  $po->vypis_zoznam();   
?>  
  <br><br>
    
</section>  

<section>
<?php 

//$zobraz_form =true;
if ($zobraz_form) {
?>

<div  id="novy_pouzivatel">
	<form method="post" enctype="multipart/form-data">
    <h2>Nový používateľ</h2>
		<table>
    <tr>
		    <td><label for="meno">Meno</label></td>
		    <td><input type="text" name="meno" id="meno" size="30" value="<?php echo ""; ?>"></td>
		</tr>
    <tr>
        <td><label for="priezvisko">Priezvisko</label></td>
		    <td><input type="text" name="priezvisko" id="priezvisko" size="30" value="<?php echo ""; ?>"></td>
		</tr>
    <tr>
      <td><label for="oddiel">Oddiel</label></td>
      <td><select name="oddiel">
      <option value="">-</option>
      <?php
      $db = napoj_db();
      $sql =<<<EOF
         SELECT * FROM oddiely;
EOF;
      $result = $db->query($sql);
      while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
        echo '<option value="'.$row1['id'].'">'.$row1['nazov'].'</option>';
      }
      ?>
      </select></td>
    </tr>
    <tr>
		    <td><label for="oscislo">Osobne číslo</label></td>
		    <td><input type="text" name="oscislo" id="oscislo" size="30" value="<?php echo ""; ?>"></td>
		</tr>
    <tr>
        <td><label for="cip">Čip</label></td>
		    <td><input type="text" name="cip" id="cip" size="30" value="<?php echo""; ?>"></td>
		</tr>
    <tr>
        <td><label for="poznamka">Poznámka</label></td>
		    <td><input type="text" name="poznamka" id="poznamka" size="30" value="<?php echo""; ?>"></td>
		</tr>
    <tr>
        <td><label for="uspech">Úspechy</label></td>
		    <td> <textarea cols="27" rows="5" name="uspech" id="uspech"></textarea></td>
		</tr>
  </table>  
    <!--text area-->
		
	  <p id="buttons">
      <input type="submit" name="posli" value="Pridaj používateľa">
      <input type="submit" name="stop" value="Koniec">
     
		</p>  
  </form>
</div>
<?php } ?>	
</section>
  
  
  
  <br><br>
  <?php 
  unset($po);
  paticka();
  ?>



</html>
