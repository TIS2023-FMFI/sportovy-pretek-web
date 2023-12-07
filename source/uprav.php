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
}

$zobraz_form = true;

if (isset ($_POST['posli2'])) {
    $po->vymaz_pouzivatela($_GET['id']);
    echo '<meta http-equiv="refresh" content="0; URL=index.php">';
    echo '<p class="chyba">Vymazane!</p>';
    $zobraz_form = false;
}

if ((isset ($_POST['posli'])) && over($_POST['meno']) && over($_POST['priezvisko'])) {
    $po->uprav_pouzivatela($_POST['meno'], $_POST['priezvisko'], $_POST['oddiel'], $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'], $_POST['uspech']);
    if (je_kmenovy($_GET['id'])) {
        $po->uprav_kmenove_info($_POST['pohlavie'], $_POST['narodenie'], $_POST['krajina_narodenia'], $_POST['statna_prislusnost'], $_POST['krajina_trvaleho_pobytu'], $_POST['ulica'], $_POST['cislo_domu'], $_POST['psc'], $_POST['mesto'], $_POST['telefon'], $_POST['mail']);
    }
    unset($po);
    $po = new POUZIVATELIA();
    $po = POUZIVATELIA::vrat_pouzivatela($_GET["id"]);
}

//pridavanie medzi kemnovych clenov
if (isset($_POST['pridajMedziKmenovych'])) {
    pridaj_kmenovy_clen();
}

?>
<!DOCTYPE HTML>
<html lang="sk">
<?php hlavicka("Upraviť údaje používateľa -  " . $po->meno . " " . $po->priezvisko); ?>
<script src="thumbnailviewer.js" type="text/javascript"></script>
<section id="uprav">
    <div id="profil">
        <div id="foto">
            <form method="post" enctype="multipart/form-data">
                <?php zobraz_obrazok($_GET['id']); ?>
            </form>
        </div>
    </div>
    <?php
    if ($zobraz_form) { ?>
    <div id="f">
        <form method="post" enctype="multipart/form-data">
            <table>
                <?php if (isset($_POST['meno']) && !over($_POST['meno'])) {
                    echo '<tr><td><span style="color: red; ">Nevyplnili ste meno!</span></td></tr>';
                } ?>
                <tr>
                    <td><label for="meno">Meno</label></td>
                    <td><input type="text" name="meno" id="meno" size="30" value="<?php if (isset($_POST['meno'])) {
                            echo $_POST['meno'];
                        } else {
                            echo $po->meno;
                        } ?>"></td>
                </tr>
                <?php if (isset($_POST['priezvisko']) && !over($_POST['priezvisko'])) {
                    echo '<tr><td><span style="color: red; ">Nevyplnili ste priezvisko!</span></td></tr>';
                } ?>
                <tr>
                    <td><label for="priezvisko">Priezvisko</label></td>
                    <td><input type="text" name="priezvisko" id="priezvisko" size="30"
                               value="<?php if (isset($_POST['priezvisko'])) {
                                   echo $_POST['priezvisko'];
                               } else {
                                   echo $po->priezvisko;
                               } ?>"></td>
                </tr>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="oscislo">Pohlavie</label></td>
                        <td><input type="text" name="pohlavie" id="pohlavie" size="30"
                                   value="<?php if (isset($_POST['pohlavie'])) {
                                       echo $_POST['pohlavie'];
                                   } else {
                                       echo $po->pohlavie;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="narodenie">Dátum narodenia:</label></td>
                        <td><input type="text" name="narodenie" id="narodenie" size="30"
                                   value="<?php if (isset($_POST['narodenie'])) {
                                       echo $_POST['narodenie'];
                                   } else {
                                       echo $po->narodenie;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="krajina_narodenia">Krajina narodenia:</label></td>
                        <td><input type="text" name="krajina_narodenia" id="krajina_narodenia" size="30"
                                   value="<?php if (isset($_POST['krajina_narodenia'])) {
                                       echo $_POST['krajina_narodenia'];
                                   } else {
                                       echo $po->krajina_narodenia;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="statna_prislusnost">Štátna príšlušnosť:</label></td>
                        <td><input type="text" name="statna_prislusnost" id="statna_prislusnost" size="30"
                                   value="<?php if (isset($_POST['statna_prislusnost'])) {
                                       echo $_POST['statna_prislusnost'];
                                   } else {
                                       echo $po->statna_prislusnost;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="krajina_trvaleho_pobytu">Krajina trvalého pobytu:</label></td>
                        <td><input type="text" name="krajina_trvaleho_pobytu" id="krajina_trvaleho_pobytu" size="30"
                                   value="<?php if (isset($_POST['krajina_trvaleho_pobytu'])) {
                                       echo $_POST['krajina_trvaleho_pobytu'];
                                   } else {
                                       echo $po->krajina_trvaleho_pobytu;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="ulica">Ulica:</label></td>
                        <td><input type="text" name="ulica" id="ulica" size="30"
                                   value="<?php if (isset($_POST['ulica'])) {
                                       echo $_POST['ulica'];
                                   } else {
                                       echo $po->ulica;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="cislo_domu">Číslo domu:</label></td>
                        <td><input type="text" name="cislo_domu" id="cislo_domu" size="30"
                                   value="<?php if (isset($_POST['cislo_domu'])) {
                                       echo $_POST['cislo_domu'];
                                   } else {
                                       echo $po->cislo_domu;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="psc">PSČ:</label></td>
                        <td><input type="text" name="psc" id="psc" size="30" value="<?php if (isset($_POST['psc'])) {
                                echo $_POST['psc'];
                            } else {
                                echo $po->psc;
                            } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="mesto">Mesto:</label></td>
                        <td><input type="text" name="mesto" id="mesto" size="30"
                                   value="<?php if (isset($_POST['mesto'])) {
                                       echo $_POST['mesto'];
                                   } else {
                                       echo $po->mesto;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="telefon">Telefón:</label></td>
                        <td><input type="text" name="telefon" id="telefon" size="30"
                                   value="<?php if (isset($_POST['telefon'])) {
                                       echo $_POST['telefon'];
                                   } else {
                                       echo $po->telefon;
                                   } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <?php
                //----------------admi-------------
                if (je_kmenovy($_GET['id'])) { ?>
                    <tr>
                        <td><label for="mail">Mail:</label></td>
                        <td><input type="text" name="mail" id="mail" size="30" value="<?php if (isset($_POST['mail'])) {
                                echo $_POST['mail'];
                            } else {
                                echo $po->mail;
                            } ?>"></td>
                    </tr>
                <?php }
                //-----------------------------------
                ?>
                <tr>
                    <td><label for="oddiel">Oddiel</label></td>
                    <td><select name="oddiel" id="oddiel">
                            <option value="">-</option>
                            <?php
                            $db = napoj_db();
                            $sql = <<<EOF
            SELECT * FROM oddiely;
EOF;
                            $result = $db->query($sql);
                            while ($row1 = $result->fetchArray(SQLITE3_ASSOC)) {
                                echo '<option value="' . $row1['id'] . '"';
                                if (isset($_POST['oddiel'])) {
                                    if ($_POST['oddiel'] == $row1['id']) {
                                        echo ' selected';
                                    }
                                } else if ($po->oddiel == $row1['id']) {
                                    echo ' selected';
                                }
                                echo '>' . $row1['nazov'] . '</option>';
                            }
                            ?>
                        </select></td>
                </tr>
                <tr>
                    <td><label for="oscislo">Osobné číslo</label></td>
                    <td><input type="text" name="oscislo" id="oscislo" size="30"
                               value="<?php if (isset($_POST['oscislo'])) {
                                   echo $_POST['oscislo'];
                               } else {
                                   echo $po->os_i_c;
                               } ?>"></td>
                </tr>
                <tr>
                    <td><label for="cip">Čip</label></td>
                    <td><input type="text" name="cip" id="cip" size="30" value="<?php if (isset($_POST['cip'])) {
                            echo $_POST['cip'];
                        } else {
                            echo $po->chip;
                        } ?>"></td>
                </tr>
                <tr>
                    <td><label for="poznamka">Poznámka</label></td>
                    <td><input type="text" name="poznamka" id="poznamka" size="30"
                               value="<?php if (isset($_POST['poznamka'])) {
                                   echo $_POST['poznamka'];
                               } else {
                                   echo $po->poznamka;
                               } ?>"></td>
                </tr>
                <tr>
                    <td><label for="uspech">Úspechy</label></td>
                    <td><textarea cols="27" rows="5" name="uspech" id="uspech"><?php if (isset($_POST['uspech'])) {
                                echo $_POST['uspech'];
                            } else {
                                echo $po->uspech;
                            } ?></textarea></td>
                </tr>
            </table>
            <p id="buttons">
                <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1 && !je_kmenovy($_GET["id"])) { ?>
                    <input type="submit" name="pridajMedziKmenovych" value="Pridať medzi kmeňových členov">
                <?php } ?>
                <input type="submit" name="posli" value="Upraviť">
                <input type="submit" name="posli2" value="Vymazať"
                       onclick="return confirm('Naozaj chcete vymazať používateľa?');">
            </p>
        </form>
        <?php } ?>
    </div>
</section>
<br><br>
<?php
unset($pt);
paticka();
?>
</html>
