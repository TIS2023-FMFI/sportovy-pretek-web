<?php

class POUZIVATELIA
{
    public $id;
    public $meno;
    public $priezvisko;
    public $oddiel;
    public $os_i_c;
    public $chip;
    public $poznamka;
    public $uspech;
    //kmenovy_clen
    public $pohlavie;
    public $narodenie;
    public $krajina_narodenia;
    public $statna_prislusnost;
    public $krajina_trvaleho_pobytu;
    public $ulica;
    public $cislo_domu;
    public $psc;
    public $mesto;
    public $telefon;
    public $mail;
    public $id_kmen_clen;

    function __construct()
    {
    }

    function nacitaj($id, $meno, $priezvisko, $oddiel, $os_i_c, $chip, $poznamka, $uspech)
    {
        $this->id = $id;
        $this->meno = $meno;
        $this->priezvisko = $priezvisko;
        $this->oddiel = $oddiel;
        $this->os_i_c = $os_i_c;
        $this->chip = $chip;
        $this->poznamka = $poznamka;
        $this->uspech = stripslashes($uspech);
    }

    function nacitaj_kmenove_info($pohlavie, $narodenie, $krajina_narodenia, $statna_prislusnost, $krajina_trvaleho_pobytu, $ulica, $cislo_domu, $psc, $mesto, $telefon, $mail, $id_kmen_clen)
    {
        $this->pohlavie = $pohlavie;
        $this->narodenie = $narodenie;
        $this->krajina_narodenia = $krajina_narodenia;
        $this->statna_prislusnost = $statna_prislusnost;
        $this->krajina_trvaleho_pobytu = $krajina_trvaleho_pobytu;
        $this->ulica = $ulica;
        $this->cislo_domu = $cislo_domu;
        $this->psc = $psc;
        $this->mesto = $mesto;
        $this->telefon = $telefon;
        $this->mail = $mail;
        $this->id_kmen_clen = $id_kmen_clen;
    }


    function pridaj_pouzivatela($meno, $priezvisko, $os_i_c, $chip, $poznamka, $uspech)
    {
        $meno2 = $meno;
        $priezvisko2 = $priezvisko;
        $os_i_c2 = $os_i_c;
        $chip2 = strtoupper($chip);
        $poznamka2 = $poznamka;
        $uspech2 = htmlentities($uspech, ENT_QUOTES, "UTF-8");
        $db = napoj_db();
        $sql = <<<EOF
      INSERT INTO Pouzivatelia (meno,priezvisko,id_oddiel,os_i_c,cip,poznamka,uspech)
      VALUES ("$meno2", "$priezvisko2", null, "$os_i_c2", "$chip2", "$poznamka2","$uspech2");
EOF;
        $ret = $db->exec($sql);
        if (!$ret) {
            echo $db->lastErrorMsg();
            $ret = -1;
        } else {
            $ret = $db->lastInsertRowID();
        }
        $db->close();
        return $ret;
    }

    function bezDiakritiky($text)
    {
        $trans = array(
            'á' => 'a', 'ä' => 'a', 'Á' => 'A', 'Ä' => 'A', 'Č' => 'C', 'č' => 'c', 'Ď' => 'D', 'ď' => 'd', 'É' => 'E', 'é' => 'e', 'í' => 'i', 'Í' => 'I', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L',
            'Ň' => 'N', 'ň' => 'n', 'Ó' => 'O', 'Ô' => 'O', 'ó' => 'o', 'ô' => 'o', 'Ŕ' => 'R', 'ŕ' => 'r', 'Š' => 'S', 'š' => 's', 'Ť' => 'T', 'ť' => 't', 'Ú' => 'U', 'ú' => 'u', 'ý' => 'y', 'Ý' => 'Y',
            'Ž' => 'Z', 'ž' => 'z');
        return strtr($text, $trans);
    }

    function over_pouzivatela($meno, $priezvisko)
    {
        $db = napoj_db();
        $sql = <<<EOF
        SELECT meno, priezvisko FROM Pouzivatelia;
EOF;
        $ret = $db->query($sql);
        while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            if ((POUZIVATELIA::bezDiakritiky($row['meno']) == POUZIVATELIA::bezDiakritiky($meno)) &&
                (POUZIVATELIA::bezDiakritiky($row['priezvisko']) == POUZIVATELIA::bezDiakritiky($priezvisko))) {
                $db->close();
                return $row['meno'] . " " . $row['priezvisko'];
            }
        }
        $db->close();
        return "";
    }

    static function vrat_pouzivatela($id)
    {
        if (!is_numeric($id)) {
            return null;
        }
        $db = napoj_db();
        $pouz_sql = <<<EOF
       SELECT * FROM Pouzivatelia WHERE id = $id;
EOF;
        $ret = $db->query($pouz_sql);
        if (has_rows($ret)) {
            $row = $ret->fetchArray(SQLITE3_ASSOC);
            $p = new self();
            $p->nacitaj($row['id'], $row['meno'], $row['priezvisko'], $row['id_oddiel'], $row['os_i_c'], $row['cip'], $row['poznamka'], $row['uspech']);
            if (is_admin()) {
                $kmen_sql = <<<EOF
       SELECT * FROM Kmenovi_clenovia AS k JOIN Pouzivatelia AS p ON p.id_kmen_clen = k.id WHERE p.id = $id;
EOF;
                $ret3 = $db->query($kmen_sql);
                $kmen_row = $ret3->fetchArray(SQLITE3_ASSOC);
                $p->nacitaj_kmenove_info($kmen_row['pohlavie'], $kmen_row['datum_narodenia'], $kmen_row['krajina_narodenia'], $kmen_row['statna_prislusnost'], $kmen_row['krajina_trvaleho_pobytu'], $kmen_row['ulica'], $kmen_row['cislo_domu'], $kmen_row['psc'], $kmen_row['mesto'], $kmen_row['telefon'], $kmen_row['mail'], $kmen_row['id_kmen_clen']);

            }
            return $p;
        } else {
            echo 'Zvoleny pouzivatel neexistuje';
        }
        return null;
    }

    static function vypis_zoznam()
    {
        $db = napoj_db();
        $sql = <<<EOF
        SELECT * from Pouzivatelia LEFT JOIN Oddiely ON Pouzivatelia.id_oddiel=Oddiely.id ORDER BY Oddiely.id IS NULL, Oddiely.nazov;;
EOF;
        $akt_oddiel = ".";
        $ret = $db->query($sql);
        while ($row = $ret->fetchArray(SQLITE3_ASSOC)) {
            if ($akt_oddiel != $row['nazov']) {
                $akt_oddiel = $row['nazov'];
                if ($akt_oddiel == "") {
                    echo "<h2 class=ZC_nadpis>Bez oddielu</h2>";
                } else {
                    echo "<h2 class=ZC_nadpis>" . $akt_oddiel . "</h2>";
                }
            }
            POUZIVATELIA::vypis_profil($row);
        }
        $db->close();
    }

    static function vypis_profil($pouz)
    {
        ?>
        <div class="profil_ram">
            <p><strong><?php echo $pouz['MENO'] . " " . $pouz['PRIEZVISKO'] ?></strong></p>
            <div class="foto_ram">
                <?php
                if (file_exists('pictures/' . $pouz['ID'] . '.gif')) {
                    $subor = 'pictures/' . $pouz['ID'] . '.gif';
                } else if (file_exists('pictures/' . $pouz['ID'] . '.png')) {
                    $subor = 'pictures/' . $pouz['ID'] . '.png';
                } else if (file_exists('pictures/' . $pouz['ID'] . '.jpg')) {
                    $subor = 'pictures/' . $pouz['ID'] . '.jpg';
                } else if (file_exists('pictures/' . $pouz['ID'] . '.jpeg')) {
                    $subor = 'pictures/' . $pouz['ID'] . '.jpeg';
                } else {
                    $subor = 'pictures/no_photo.jpg';
                }
                ?>
                <a href="<?php echo $subor; ?>"><img src="<?php echo $subor; ?>" alt="Profilová fotografia"></a>
            </div>

            <?php
            if ($pouz['nazov'] != "") {
                echo "<p>Oddiel: " . $pouz['nazov'] . "</p>";
            } ?>
            <p>Osobné identifikačné číslo: <?php echo $pouz['OS_I_C'] ?></p>
            <p>Čip: <?php echo $pouz['OS_I_C'] ?></p>
            <p>Poznámka: <?php echo $pouz['POZNAMKA'] ?></p>
            <p>Úspechy: <?php echo $pouz['USPECH'] ?></p>
            <p><a href="tabulka_vykonov.php?id=<?php echo $pouz['ID'] ?>">Osobné výkony</a></p>
            <p><a href="uprav.php?id=<?php echo $pouz['ID'] ?>">Uprav</a></p>
            <?php
            if (isset($_SESSION['admin']) && $_SESSION['admin']) {
                ?>
                <form method=post>
                    <input type=hidden name='id_user' value='<?php echo $pouz['ID'] ?>'>
                    <input type=submit id="del" name='del' value="Vymaž člena"
                           onclick="return confirm('Naozaj chcete vymazať používateľa?');">
                </form>
                <?php
            }
            ?>
        </div>
        <?php
    }

    static function vymaz_pouzivatela($ID)
    {
        $db = napoj_db();
        $sql = <<<EOF
      DELETE FROM Pouzivatelia WHERE id = $ID;
      DELETE FROM Prihlaseni WHERE id_pouz = $ID;
EOF;
        $ret = $db->exec($sql);
        if (!$ret) {
            echo $db->lastErrorMsg();
        } else {
            vymaz_obrazok($ID);
        }
        $db->close();
    }

    function uprav_pouzivatela($MENO, $PRIEZVISKO, $oddiel, $OS_I_C, $CHIP, $POZNAMKA, $uspech)
    {
        if (empty($oddiel)) $oddiel = null;
        $db = napoj_db();
        $MENO2 = $MENO;
        $PRIEZVISKO2 = $PRIEZVISKO;
        $OS_I_C2 = $OS_I_C;
        $CHIP2 = strtoupper($CHIP);
        $POZNAMKA2 = $POZNAMKA;
        $uspech2 = htmlentities($uspech, ENT_QUOTES, "UTF-8");
        $sql = <<<EOF
        UPDATE Pouzivatelia set meno = "$MENO2" WHERE id="$this->id";
        UPDATE Pouzivatelia set priezvisko = "$PRIEZVISKO2" WHERE id="$this->id";
        UPDATE Pouzivatelia set id_oddiel = "$oddiel" WHERE id="$this->id";
        UPDATE Pouzivatelia set os_i_c = "$OS_I_C2" WHERE id="$this->id";
        UPDATE Pouzivatelia set cip = "$CHIP2" WHERE id="$this->id";
        UPDATE Pouzivatelia set poznamka = "$POZNAMKA2" WHERE id="$this->id";
        UPDATE Pouzivatelia set uspech = "$uspech2" WHERE id="$this->id";
EOF;
        $ret = $db->exec($sql);
        if (!$ret) {
            echo $db->lastErrorMsg();
        }
        $db->close();
    }

    function uprav_kmenove_info($pohlavie, $narodenie, $krajina_narodenia, $statna_prislusnost, $krajina_trvaleho_pobytu, $ulica, $cislo_domu, $psc, $mesto, $telefon, $mail)
    {
        $db = napoj_db();
        $sql = <<<EOF
        UPDATE Kmenovi_clenovia set pohlavie = "$pohlavie" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set datum_narodenia = "$narodenie" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set krajina_narodenia = "$krajina_narodenia" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set statna_prislusnost = "$statna_prislusnost" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set krajina_trvaleho_pobytu = "$krajina_trvaleho_pobytu" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set ulica = "$ulica" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set cislo_domu = "$cislo_domu" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set psc = "$psc" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set mesto = "$mesto" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set telefon = "$telefon" WHERE id="$this->id_kmen_clen";
        UPDATE Kmenovi_clenovia set mail = "$mail" WHERE id="$this->id_kmen_clen";
EOF;
        $ret = $db->exec($sql);
        if (!$ret) {
            echo $db->lastErrorMsg();
        }
        $db->close();
    }
}

?>
