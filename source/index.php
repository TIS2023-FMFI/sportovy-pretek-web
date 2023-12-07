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
if (isset($_SESSION['admin']) && $_SESSION['admin']) {
    hlavicka("Admin");
} else {
    hlavicka("ŠK Sandberg: Prihlasovanie na preteky.");
} ?>

<div id="zoz_pretekov_uzivatel">
    <h2>Zoznam pretekov</h2>
    <?php
    if (isset($_SESSION['admin']) && $_SESSION['admin']) {
        ?>
        <input name="novy" type="submit" class="novy" onclick="location.href='novy_pretek.php';" value="Nové preteky">
        <input type="submit" onclick="location.href='kategorie.php';" value="Kategórie">
        <input type="submit" onclick="location.href='oddiely.php';" value="Oddiely">
        <?php
    }
    if (isset($_SESSION['zmazany'])) {
        echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $_SESSION['zmazany'] . ' bol zmazaný.</strong>';
        unset($_SESSION['zmazany']);
    }
    if (isset($_POST['aktiv'])) {
        PRETEKY::aktivuj($_POST['id']);
        $pr = new PRETEKY();
        $pr = PRETEKY::vrat_pretek($_POST["id"]);
        if ($pr->AKTIV == 1) {
            echo '<strong style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $pr->NAZOV . ' bol aktivovaný.</strong>';
        } else {
            echo '<strong  style="color:green; font-size:15px; margin-left:30px;">Pretek ' . $pr->NAZOV . ' bol archivovaný.</strong>';
        }
    }
    if (isset($_POST['zmaz'])) {
        $pr = new PRETEKY();
        $pr = PRETEKY::vrat_pretek($_POST["id"]);
        $nazov = $pr->NAZOV;
        PRETEKY::vymaz_pretek($_POST['id']);
        echo '<meta http-equiv="refresh" content="0; URL=index.php">';
        $_SESSION['zmazany'] = $nazov;
    } ?>
    <table id="treningy" class="tablesorter" style="width:100%;">
        <thead>
        <tr>
            <th class="prvy">Názov pretekov</th>
            <th class="prvy">Dátum konania</th>
            <th class="prvy">Prihlasovanie do</th>
            <th class="prvy">Zobraz osobný výkon</th>
            <th class="prvy">Zobraz výsledky</th>
            <?php if (isset($_SESSION['admin']) && $_SESSION['admin']) { ?>
                <th class="prvy">Uprav preteky</th>
                <th class="prvy">Archivácia</th>
                <th class="prvy">Skopíruj preteky</th>
                <th class="prvy">Vymaž preteky</th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
        if (isset($_SESSION['admin']) && $_SESSION['admin']) {
            PRETEKY::vypis_zoznam_admin();
        } else {
            PRETEKY::vypis_zoznam();
        } ?>
        </tbody>
    </table>
</div>
<br><br>

<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#treningy").tablesorter({dateFormat: "uk"});
    });
</script>
<?php paticka(); ?>
</html>
