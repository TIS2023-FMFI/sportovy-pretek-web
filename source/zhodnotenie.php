<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
$navodik = false;

?>
<!DOCTYPE HTML>
<html>
<script type="text/javascript" src="sorter/jquery-latest.js"></script>
<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script> 
<?php
  $pr = new PRETEKY();
  $pr=PRETEKY::vrat_pretek($_GET["id"]);
  hlavicka($pr->NAZOV);
  if (isset($_POST["uloz"])){
    for ($pom = 0; $pom < $_POST["pocet"]; $pom++){
    	PRETEKY::zapis_cas($_GET["id"],$_POST["id".$pom],$_POST["cas".$pom]);
    }
  }
  if (isset($_POST["uprav"])){
    for ($pom = 0; $pom < $_POST["pocet"]; $pom++){
    	PRETEKY::uprav_cas($_GET["id"],$_POST["id".$pom],$_POST["cas".$pom]);
    }
  }
  if (isset($_POST["export"])){
  	PRETEKY::exportuj_zhodnotenie($_GET["id"]);
  }
  $db = napoj_db();
  $sql = <<<EOF
  	SELECT COUNT(*) AS POCET FROM Zhodnotenie WHERE id_pret = $pr->ID;
EOF;
$ret = $db->query($sql);
$row = $ret->fetchArray(SQLITE3_ASSOC);
$db->close();
if ($row["POCET"] <= 0){
	if (!isset($_SESSION["admin"]) || !$_SESSION["admin"]){
		echo "<section><h2>Ešte nebolo pridané hodnotenie tréningu.</h2></section>";
	} else{

?>
<section id="zhodnotenie_table"> 
<form method="post" >
<?php
$db = napoj_db();
$sql =<<<EOF
           SELECT * FROM Prihlaseni JOIN Pouzivatelia ON Prihlaseni.id_pouz = Pouzivatelia.id WHERE Prihlaseni.id_pret = $pr->ID;
EOF;
$ret = $db->query($sql);
$i = 0;
echo "<table id='zhodnotenie_table'><tr><th>Meno</th><th>Priezvisko</th><th>Čas</th></tr>";
while($row = $ret->fetchArray(SQLITE3_ASSOC)){
  echo "<tr>";
 
  echo "<td>".$row['meno']."</td>"; 

  echo "<td>".$row['priezvisko']."</td>";

  echo '<td><input type="text" name="cas'.$i.'" required/><input type="hidden" name="id'.$i.'" value="'.$row["ID_POUZ"].'"/></td>';
        
  echo "</tr>";

  $i++;
}
if($i == 0){
  echo "<h1>Nikto nebol prihlásený na preteky.</h1>";
}else{
  echo '<tr><td></td><td></td><td><input type="submit" name="uloz" value="Ulož"><input type="hidden" name="pocet" value="'.$i.'"></td></tr>';
}
echo "</table>";
?>
  </form>
  </section>   
  <?php   
  }
  } else{
  	?>
  	<section>
  	<table id="zhodnotenie_table">
  	<tr><th>Kategória</th><th>Meno</th><th>Priezvisko</th><th>Čas</th></tr>
  	<?php
  	
  	if (isset($_SESSION["admin"]) && $_SESSION["admin"] && isset($_POST["upravuj"])){
  		echo '<form method="post">';
  		PRETEKY::vypis_zhodnotenie_admin($_GET["id"]);
  		echo "</form>";
  	} else{
  		PRETEKY::vypis_zhodnotenie($_GET["id"]);
  	}
  	?>
  	</table>
  	</section>
  	<?php
  } 
    unset($pr);
    paticka();
  ?>
</html>