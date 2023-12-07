<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');

$po = new POUZIVATELIA();
$po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);

if (isset($_POST['posli3'])) {
    vymaz_obrazok($_GET['id']);
    pridaj_obrazok($_GET['id']);
}
if (isset($_POST['vymaz'])) {
    vymaz_obrazok($_GET['id']);
} ?>

<!DOCTYPE HTML>
<html lang="sk">
<?php hlavicka("Upraviť údaje používateľa -  " . $po->meno . " " . $po->priezvisko); ?>

<section id="uprav">
    <div id="profil">
        <div id="foto">
            <form method="post" enctype="multipart/form-data">
                <?php zobraz_obrazok($_GET['id']); ?>
            </form>
        </div>

        <div id="obsah">
            <div class="obsah_stl">
                <?php
                $po = new POUZIVATELIA();
                $po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);

                echo "Meno: " . $po->meno . "<br>
				Priezvisko: " . $po->priezvisko . "<br>
				Oddiel: " . $po->oddiel . "<br>
				Osobné ident. číslo: " . $po->os_i_c . "<br>
				Poznámka: " . $po->poznamka . "<br>
				Úspechy: " . $po->uspech . "<br>"; ?>
            </div>

            <div class="obsah_stl">
                <?php
                if (je_kmenovy($_GET['id']) && isset($_SESSION['admin']) && $_SESSION['admin']) {
                    echo "Pohlavie: " . $po->pohlavie . "<br>
					Dátum narodenia: " . $po->narodenie . "<br>
					Krajina narodenia: " . $po->krajina_narodenia . "<br>
					Štátna príslušnosť: " . $po->statna_prislusnost . "<br>
					Krajina trvelého bydliska: " . $po->krajina_trvaleho_pobytu . "<br>
					Ulica: " . $po->ulica . "<br>
					Číslo domu: " . $po->cislo_domu . "<br>
					PSČ: " . $po->psc . "<br>
					Mesto: " . $po->mesto . "<br>
					Telefón: " . $po->telefon . "<br>
					Mail: " . $po->mail . "<br>
					Číslo čipu: " . $po->chip;
                } ?>
            </div>
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
