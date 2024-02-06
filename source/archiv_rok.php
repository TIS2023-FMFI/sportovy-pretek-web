<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();

if (isset($_GET['odhlas'])) {
    $_SESSION['admin'] = 0;
} ?>

<!DOCTYPE HTML>
<html lang="sk">
<?php
if (is_admin()) {
    hlavicka("Archív " . $_GET['rok']);
} else {
    echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
} ?>

<div id="zoz_pretekov_uzivatel">
    <h2>Zoznam tréningov</h2>
    <?php
    if (isset($_SESSION['zmazany'])) {
        echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $_SESSION['zmazany'] . ' bol zmazaný.</strong>';
        unset($_SESSION['zmazany']);
    }
    if (isset($_POST['aktiv'])) {
        PRETEKY::aktivuj($_POST['id']);
        $pr = new PRETEKY();
        $pr = PRETEKY::vrat_pretek($_POST["id"]);
        if ($pr->AKTIV == 1) {
            echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $pr->NAZOV . ' bol vybratý z archívu.</strong>';
        } else {
            echo '<strong  style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $pr->NAZOV . ' bol archivovaný.</strong>';
        }
    }
    if (isset($_POST['zmaz'])) {
        $pr = new PRETEKY();
        echo $_POST['id'];
        $pr = PRETEKY::vrat_pretek($_POST['id']);
        $rok = $_GET['rok'];
        $nazov = $pr->NAZOV;
        PRETEKY::vymaz_pretek($_POST['id']);
        echo '<meta http-equiv="refresh" content="0; URL=archiv_rok.php?rok=' . $rok . '">';
        $_SESSION['zmazany'] = $nazov;
    } ?>
    <table id="archiv" class="tablesorter" style="width:100%;">
        <thead>
        <tr>
            <th class="prvy header">Typ tréningu</th>
            <th class="prvy header">Dátum konania</th>
            <th class="prvy">Prihlasovanie do</th>
            <th class="prvy">Uprav tréning</th>
            <th class="prvy">Zobraz osobný výkon</th>
            <th class="prvy">Zobraz celkové hodnotenie</th>
            <th class="prvy">Archivácia</th>
            <th class="prvy">Skopíruj tréning</th>
            <th class="prvy">Vymaž tréning</th>
        </tr>
        </thead>
        <tbody>
        <?php PRETEKY::vypis_archiv($_GET['rok']); ?>
    </table>
</div>
<br><br>

<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#archiv").tablesorter({dateFormat: "uk"});
    });
</script>
<?php paticka(); ?>
</html>
