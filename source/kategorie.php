<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
?>

<!DOCTYPE HTML>
<html lang="sk">
<?php
if (!is_admin()) {
    echo '<META HTTP-EQUIV="refresh" CONTENT="0;URL=index.php">';
    die();
}
hlavicka("Kateórie");
?>
<section>
    <div id="tab_platby">
        <form method="post">
            <h2>Kategórie</h2>
            <?php
            $pl = new PRETEKY();
            PRETEKY::vypis_zoznam_kategorii();
            ?>
            <p>

                <a href="kategorie_new.php"><input type="button" value="Nová kategória"></a>
                <input name="del" type="submit" id="del"
                       onclick="return confirm('Naozaj chcete vymazať kategóriu?');" value="Vymazať kategóriu">
            </p>
        </form>
        <br><br> <br> <br>
    </div>

    <br>
    <?php
    $zobraz_form = false;

    if ((isset($_POST['del']) && (isset($_POST['incharge'])))) {
        // PHP throws a fit if we try to loop a non-array
        if (is_array($_POST['incharge'])) {
            foreach ($_POST['incharge'] as $val) {
                PRETEKY::vymaz_kategoriu($val);
                echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
            }
        }
    }

    if (isset($_POST['novy']) || isset($_POST['posli'])) {
        $zobraz_form = true;
    }
    if ((isset($_POST['posli'])) && (over($_POST['nazov']))) {
        PRETEKY::pridaj_kategoriu($_POST['nazov']);
        echo '<META HTTP-EQUIV="refresh" CONTENT="0">';
    }
    if (isset($_POST['cancel'])) {
        $zobraz_form = false;
    }
    if ($zobraz_form) {
        ?>
        <div id="novy_pouzivatel">
            <form method="post" enctype="multipart/form-data">
                <h2>Pridať kategóriu</h2>
                <table>
                    <?php if (isset($_POST['nazov']) && !over($_POST['nazov'])) {
                        echo '<tr><td><span style="color: red; ">Nevyplnili ste názov!</span></td></tr>';
                    } ?>
                    <tr>
                        <td><label for="nazov">Názov:</label></td>
                        <td><input type="text" name="nazov" id="nazov" size="30"
                                   value="<?php if (isset($_POST['nazov'])) {
                                       echo $_POST['nazov'];
                                   } ?>"></td>
                    </tr>
                </table>
                <p id="buttons">
                    <input type="submit" name="posli" value="Pridaj">
                    <input type="submit" name="cancel" value="Koniec">
                </p>
            </form>
        </div>
    <?php } ?>
</section>
<br><br>

<script src="js/jquery.datetimepicker.js"></script>
<script>
    $('#datetimepicker').datetimepicker({
        dayOfWeekStart: 1,
        format: 'd-m-Y H:i',
        lang: 'sk',
        showAnim: "show"
    });
</script>
<?php paticka(); ?>
</html>
