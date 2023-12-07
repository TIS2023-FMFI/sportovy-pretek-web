<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();
?>

<!DOCTYPE HTML>
<html lang="sk">
<?php
hlavicka("Tabuľka výkonov");
if (isset($_POST["vymaz"])) {
    if (vymaz_vykon($_POST["ID_VYKON"])) {
        echo "<h4 align='center'>Výkon vymazaný</h4>";
    }
}
vypis_vykony($_GET["id"]);
paticka(); ?>

</html>

<?php
// === PHP Functions ===
function vypis_vykony($id_pouzivatela)
{
    $db = napoj_db();
    if ($db) {
        $sql = <<<EOF
    SELECT * FROM Vykon 
    JOIN Pouzivatelia ON Vykon.id_log = Pouzivatelia.id
    JOIN Preteky ON Vykon.id_pret = Preteky.id
    WHERE id_log = "$id_pouzivatela"
    ORDER BY datum;
EOF;
        $ret = $db->query($sql);
        $sql = <<<EOF
    SELECT meno,priezvisko FROM Pouzivatelia WHERE id = "$id_pouzivatela";
EOF;
        $ret2 = $db->query($sql);
        $row = $ret2->fetchArray(SQLITE3_ASSOC); ?>
        <div>
        <h1 style="text-align:center;"><?php echo $row['meno'] . " " . $row['priezvisko'] ?></h1>
        <table style="width:100%;" border=1 class="tabulkaVykonou">
            <tr>
                <th class="prvy">Dátum</th>
                <th class="prvy">Názov</th>
                <th class="prvy">Miesto</th>
                <th class="prvy">Víťaz</th>
                <th class="prvy">Víťazný čas</th>
                <th class="prvy">Môj čas</th>
                <th class="prvy">Vzdialenosť</th>
                <th class="prvy">Ideálna vzdialenosť</th>
                <th class="prvy">Rýchlosť</th>
                <th class="prvy">Prevýšenie</th>
                <th class="prvy">Odchýlka</th>
                <th class="prvy">Prirážka</th>
                <th class="prvy">Hodnotenie</th>
                <td></td>
                <td></td>
            </tr>
            <?php
            while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["datum"] . "</td>";
                echo "<td>" . $row["nazov"] . "</td>";
                echo "<td>" . $row["miesto"] . "</td>";
                echo "<td>" . $row["vitaz"] . "</td>";
                echo "<td>" . $row["vitaz_cas"] . "</td>";
                echo "<td>" . $row["moj_cas"] . "</td>";
                echo "<td>" . $row["vzdialenosť"] . "</td>";
                echo "<td>" . $row["ideal_vzdialenost"] . "</td>";
                echo "<td>" . $row["rychlost"] . "</td>";
                echo "<td>" . $row["prevysenie"] . "</td>";
                echo "<td>" . $row["odchylka"] . "</td>";
                echo "<td>" . $row["prirazka"] . "</td>";
                echo "<td>" . $row["hodnotenie"] . "</td>";
                echo "<td><a href='uprav_vykon.php?id=" . $row["id"] . "'>Uprav</a></td>";
                echo "<td><form method='post'><input type='hidden' name='ID_VYKON' value='" . $row["id"] . "'><input type='submit' name='vymaz' value='Vymaž'></form></td>";
                echo "</tr>";
            }
            ?>
        </table>
        </div><?php
    }
    $db->close();
}

function vymaz_vykon($id_vykon)
{
    $db = napoj_db();
    if ($db) {
        $sql = <<<EOF
      DELETE FROM Vykon WHERE id = "$id_vykon";
EOF;
        return $db->exec($sql);
    }
    return 0;
}

?>
