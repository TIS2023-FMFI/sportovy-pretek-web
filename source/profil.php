<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

$po = new POUZIVATELIA();
$po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);

if(isset($_POST['posli3'])){
  vymaz_obrazok($_GET['id']);
 	pridaj_obrazok($_GET['id']);
}
if(isset($_POST['vymaz'])){
 	vymaz_obrazok($_GET['id']);
}

?>
<!DOCTYPE HTML>
<html>
<?php hlavicka("Upraviť údaje používateľa -  ".$po->meno." ".$po->priezvisko);?>

<section id="uprav">
 	<div id="profil">
		<div id="foto">
      <form method="post" enctype="multipart/form-data">
        <?php zobraz_obrazok($_GET['id']); ?>
      </form>
    </div>
    <div id="obsah">
	    <?php 
	    $po = new POUZIVATELIA();
	    $po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);
	    
	echo "<label for='meno'>Meno: </label><label>".$po->meno."</label><br><label for='priezvisko'>Priezvisko: <label><label>".$po->priezvisko."</label><br>
		<label for='meno'>Oddiel: </label><label>".$po->oddiel."</label><br>
		<label for='os_i_c'>Osobné ident. číslo: </label>
		<label>".$po->os_i_c."</label><br>
		<label for='poznamka'>Poznámka: </label>
		<label>".$po->poznamka."</label><br>
		<label for='uspechy'>Úspechy: </label>
		<label>".$po->uspech."</label><br>";
		
		if(je_kmenovy($_GET['id'])){
			echo 
			"<label for='pohavie'>Pohlavie: </label>
			<label>".$po->pohlavie."</label><br>
			<label for='narodenie'>Dátum narodenia: </label>
			<label>".$po->narodenie."</label><br>
			<label for='krajinaN'>Krajina narodenia: </label>
			<label>".$po->krajina_narodenia."</label><br>
			<label for='statna'>Štátna príslušnosť: </label>
			<label>".$po->statna_prislusnost."</label><br>
			<label for='krajina'>Krajina trvelého bydliska: </label>
			<label>".$po->krajina_trvaleho_pobytu."</label><br>
			<label for='ulica'>Ulica: </label>
			<label>".$po->ulica."</label><br>
			<label for='cislo_domu'>Číslo domu: </label>
			<label>".$po->cislo_domu."</label><br>
			<label for='psc'>PSČ: </label>
			<label>".$po->psc."</label><br>
			<label for='mesto'>Mesto: </label>
			<label>".$po->mesto."</label><br> 
			<label for='telefon'>Telefón: </label>
			<label>".$po->telefon."</label><br>
			<label for='mail'>Mail: </label>
			<label>".$po->mail."</label><br>
			<label for='cip'>Číslo čipu: </label>
			<label>".$po->chip."</label><br>
			<label for='oddiel'>Oddiel: </label>"; 
		}?>
	</div>
	</div>
</section>
<br><br>

<script src="thumbnailviewer.js" type="text/javascript"></script>  

<?php 
unset($po);
paticka();
?>
</html>