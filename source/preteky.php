<?php
/*
* Trieda PRETEK ktoru pouzijeme na udrzovanie udajov o pretekoch.
*/
class PRETEKY{
  public $ID;
  public $NAZOV;
  public $DATUM;
  public $DEADLINE;
  public $AKTIV;
  public $POZNAMKA;
  /**
  *Prida udaje objektu preteky do databazy
  */

  public function nacitaj($ID,$NAZOV,$DATUM,$DEADLINE,$AKTIV,$POZNAMKA){
    $this->ID = $ID;
    $this->NAZOV = $NAZOV;
    $this->DATUM = $DATUM;
    $this->DEADLINE = $DEADLINE;
    $this->AKTIV = $AKTIV;
    //$this->POZNAMKA = iconv('cp1252', 'UTF-8//TRANSLIT//IGNORE', html_entity_decode($POZNAMKA, ENT_QUOTES, 'cp1252'));
    $this->POZNAMKA = html_entity_decode($POZNAMKA);
  }

  public function pridaj_pretek($NAZOV, $DATUM, $DEADLINE, $POZNAMKA){
   $db = napoj_db();
   $NAZOV2 = htmlentities($NAZOV, ENT_QUOTES, 'UTF-8');
   $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
   $text = $POZNAMKA;
   if(preg_match($reg_exUrl, $text, $url) && !strpos($text, "</a>") && !strpos($text, "</A>") && !strpos($text, "HREF") && !strpos($text, "href")) {
      // make the urls hyper links
      $text = preg_replace($reg_exUrl, "<a href=".$url[0].">{$url[0]}</a> ", $text);
    }
    $POZNAMKA2 = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $sql =<<<EOF
      INSERT INTO Preteky (nazov,datum,deadline,aktiv,poznamka)
      VALUES ("$NAZOV2", "$DATUM", "$DEADLINE","1","$POZNAMKA2");
EOF;
    $ret = $db->exec($sql);
    $sql0 = "SELECT max(id) as maxId FROM Preteky";
    $ret0=$db->query($sql0);
    $row = $ret0->fetchArray(SQLITE3_ASSOC);
    $cislo = $row['maxId'];
    $db->close();
    return $cislo;
  }

/**
*upravi pretek v databaze podla aktualneho id objektu preteky
*/
  function uprav_pretek ($NAZOV, $DATUM, $DEADLINE,$POZNAMKA){
    if(!$this->ID){
      return false;
    }
    $NAZOV2 = htmlentities($NAZOV, ENT_QUOTES, 'UTF-8');
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $text = $POZNAMKA;
    if(preg_match($reg_exUrl, $text, $url) && !strpos($text, "</a>") && !strpos($text, "</A>") && !strpos($text, "HREF") && !strpos($text, "href")) {
      // make the urls hyper links
      $text = preg_replace($reg_exUrl, "<a href=".$url[0].">{$url[0]}</a> ", $text);
    }
    $POZNAMKA2 = htmlentities($text, ENT_QUOTES, 'UTF-8');
    $db = napoj_db();
    $sql =<<<EOF
       UPDATE Preteky set nazov = "$NAZOV2" where id="$this->ID";
       UPDATE Preteky set datum = "$DATUM" where id="$this->ID";
       UPDATE Preteky set deadline = "$DEADLINE" where id="$this->ID";
       UPDATE Preteky set poznamka = "$POZNAMKA2" where id="$this->ID";
       DELETE FROM Kategorie_pre WHERE id_pret = "$this->ID";
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
      echo $db->lastErrorMsg();
    }
   $db->close();
  }

/**
*odhlasi pouzivatela na pretek
*/
  static function odhlas_z_preteku($ID,$ID_pouz){
    $db = napoj_db();
    $sql =<<<EOF
      DELETE FROM Prihlaseni WHERE id_pouz = "$ID_pouz" AND id_pret="$ID";
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
      echo $db->lastErrorMsg();
    }
    $db->close();
  }

/**
*prihlasi pouzivatela na pretek
*/
  static function prihlas_na_pretek($id,$id_pouz,$id_kat,$poz){
    $db = napoj_db();
    $sql =<<<EOF
        INSERT INTO Prihlaseni (id_pouz,id_pret,id_kat,poznamka)
        VALUES ("$id_pouz","$id","$id_kat","$poz");
EOF;
    $ret = $db->exec($sql);
    if(!$ret){
      echo $db->lastErrorMsg();
    }
    $db->close();
}


/**
*vrati zoznam pouzivatelov pruhlasenych na pretek s duplicitnym chipom
*/

  public function vypis_prihlasenych_d_chip(){
    $db = napoj_db();
    $sql =<<<EOF
      CREATE TABLE temp
      (id INTEGER NOT NULL,
      meno              TEXT    NOT NULL,
      priezvisko        TEXT    NOT NULL,
      os_i_c            TEXT,
      cip               TEXT,
      id_kmen_clen      INTEGER,
      poznamka          TEXT,
      uspech            TEXT,
      id_kat            INTEGER,
      id_oddiel         INTEGER,
      poznamkaPouz      TEXT
      );
EOF;
    $db->exec($sql);
    $sql =<<<EOF
          INSERT INTO temp(id, id_kmen_clen, id_oddiel, meno, priezvisko, os_i_c, cip,  poznamkaPouz, uspech, id_kat, poznamka)
          SELECT Pouzivatelia.*, Prihlaseni.id_kat, Prihlaseni.poznamka FROM Pouzivatelia INNER JOIN Prihlaseni ON Pouzivatelia.id = Prihlaseni.id_pouz
          WHERE (Prihlaseni.id_pret = $this->ID);
EOF;
    $db->exec($sql);
    $sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.cip in (SELECT temp.cip from temp GROUP BY temp.cip HAVING COUNT (temp.cip) > 1) GROUP BY temp.id;
EOF;
    $ret = $db->query($sql);
    $sql =<<<EOF
         DROP TABLE temp;
EOF;
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "<tr>";
      echo '<td><input type="checkbox" name="incharge[]" value="'.$row['id'].'"/></td>';
      echo "<td class='fnt'><strong class=upozornenie>".$row['meno']."</strong></td>";
      echo "<td class='fnt'><strong class=upozornenie>".$row['priezvisko']."</strong></td>";
      echo "<td class='fnt'>".$row['id_kat']."</td>";
      echo "<td class='fnt'>".$row['os_i_c']."</td>";
      echo "<td class='fnt'><strong class=upozornenie>".$row['cip']."</strong></td>";
      echo "<td class='fnt'>".$row['poznamka']."</td>";
      echo "</tr> ";
    }
    // echo "Operation done successfully"."<br>";   ///////////////////
    $db->exec($sql);
    $db->close();
  }
/**
*vrati zoznam pouzivatelov pruhlasenych na pretek s unikatnym chipom
*/
  public function vypis_prihlasenych_u_chip(){
    $db = napoj_db();
    $sql =<<<EOF
      CREATE TABLE temp
      (id INTEGER NOT NULL,
      meno              TEXT    NOT NULL,
      priezvisko        TEXT    NOT NULL,
      os_i_c            TEXT,
      cip               TEXT,
      id_kmen_clen      INTEGER,
      poznamka          TEXT,
      uspech            TEXT,
      id_kat            INTEGER,
      id_oddiel         INTEGER,
      poznamkaPouz      TEXT
      );
EOF;
    $db->exec($sql);
    $sql =<<<EOF
          INSERT INTO temp(id, id_kmen_clen, id_oddiel, meno, priezvisko, os_i_c, cip,  poznamkaPouz, uspech, id_kat, poznamka)
          SELECT Pouzivatelia.*, Prihlaseni.id_kat, Prihlaseni.poznamka FROM Pouzivatelia INNER JOIN Prihlaseni ON Pouzivatelia.id = Prihlaseni.id_pouz
          WHERE (Prihlaseni.id_pret = $this->ID);
EOF;
    $db->exec($sql);
    $sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.cip in (SELECT temp.cip from temp GROUP BY temp.cip HAVING COUNT (temp.cip) = 1) GROUP BY temp.id;
EOF;
    $ret = $db->query($sql);
    $sql =<<<EOF
         DROP TABLE temp;
EOF;
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      //echo "pomocny vypis prihlasenych s unikatnym cipom";
      //echo $row['id'],$row['meno'],$row['priezvisko'],$row['os_i_c'],$row['cip'],$row['poznamka']."<br>";
      echo "<tr>";
      echo '<td><input type="checkbox" name="incharge[]" value="'.$row['id'].'"/></td>';
      echo "<td>".$row['meno']."</td>";
      echo "<td>".$row['priezvisko']."</td>";
      echo "<td>".$row['id_kat']."</td>";
      echo "<td>".$row['os_i_c']."</td>";
      echo "<td>".$row['cip']."</td>";
      echo "<td>".$row['poznamka']."</td>";
      echo "</tr> ";
    }
    $db->exec($sql);
    $db->close();
  }

/**
*vrati zoznam neprihlasenych
*/
  public function vypis_neprihlasenych(){
    $db = napoj_db();
    $sql =<<<EOF
      CREATE TABLE temp
      (id INTEGER NOT NULL,
      meno              TEXT    NOT NULL,
      priezvisko        TEXT    NOT NULL,
      os_i_c            TEXT,
      cip               TEXT,
      id_kmen_clen      INTEGER,
      uspech            TEXT,
      id_oddiel         INTEGER,
      poznamkaPouz      TEXT
      );
EOF;
    $db->exec($sql);
    $sql =<<<EOF
      INSERT INTO temp(id, id_kmen_clen, id_oddiel, meno, priezvisko, os_i_c, cip,  poznamkaPouz, uspech)
      SELECT Pouzivatelia.*
      FROM Pouzivatelia LEFT OUTER JOIN Prihlaseni ON Prihlaseni.id_pouz = Pouzivatelia.id
      WHERE Prihlaseni.id is null
      OR (Prihlaseni.id_pret <> $this->ID AND Prihlaseni.id_pouz NOT IN
         (SELECT Prihlaseni.id_pouz FROM Prihlaseni WHERE id_pret = $this->ID));
EOF;
    $db->exec($sql);
    $sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.id GROUP BY temp.id;
EOF;

    $ret = $db->query($sql);
    $sql =<<<EOF
         SELECT id_kat,nazov FROM Kategorie_pre JOIN Kategorie on Kategorie_pre.id_kat = Kategorie.id WHERE id_pret = $this->ID;
EOF;
    $result = $db->query($sql);
    $sql =<<<EOF
         DROP TABLE temp;
EOF;
    if(isset($_COOKIE['prihlaseni'])){
      $cookiesArray=explode(",",$_COOKIE['prihlaseni']);
    }
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      if ((isset($_GET['cookies'])&&!$_GET['cookies'])||(!isset($_COOKIE['prihlaseni']) || in_array($row['id'],$cookiesArray))){
        echo "<tr>";
        echo '<td><input type="checkbox" name="checked[]" value="'.$row['id'].'"/></td>';
        echo '<td><select name="incharge[]">';
        echo '<option value="-">-</option>';
        while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
          echo '<option value="'.$row1['id_kat'].':'.$row['id'].'" ';
           echo '>'.$row1['nazov'].'</option>';

        }
        echo "</select></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['id']."&amp;pr=".$_GET["id"]."'>".$row['meno']."</a></td>";
        echo "<td><a class='fntb' href='profil.php?id=".$row['id']."&amp;pr=".$_GET["id"]."'>".$row['priezvisko']."</a></td>";
        echo "<td>".$row['os_i_c']."</td>";
        echo "<td>".$row['cip']."</td>";
        echo "<td><input type='text' name=poznamka".$row['id']." size=10 value='";
        if (isset($_POST['poznamka'.$row['id']])){
          echo $_POST['poznamka'.$row['id']];
        }
        else{
          echo $row['poznamkaPouz'];
        }
        echo "'></td>";
        echo "<td>
        <a class='fntb' href='uprav.php?id=".$row['id']."&amp;pr=".$_GET["id"]."'>Uprav</a></td></tr>";
      }
    }
    ?>
    <tr>
    <td><input type="checkbox" name="posli"></td>
    <?php
    echo '<td><select name="kategoria">';
    echo '<option value="-">-</option>';
    while($row1 = $result->fetchArray(SQLITE3_ASSOC) ){
      echo '<option value="'.$row1['nazov'].'">'.$row1['nazov'].'</option>';
    }
    echo "</select></td>";
    ?>
    <td><input type="text" name="meno" id="meno" size="10" value=""></td>
    <td><input type="text" name="priezvisko" id="priezvisko" size="10" value=""></td>
    <td><input type="text" name="oscislo" id="oscislo" size="10" value=""></td>
    <td><input type="text" name="cip" id="cip" size="10" value=""></td>
    <td><input type="text" name="poznamka" id="poznamka" size="10" value=""></td>
    </tr>
    <?php
    $db->exec($sql);
    $db->close();
  }



/**
*nastavy parametre preteku podla zvoleneho id
*/
  static function vrat_pretek ($ID){
    $db = napoj_db();
    $sql =<<<EOF
         SELECT * from Preteky WHERE id=$ID;
EOF;
    $sql1 =<<<EOF
         SELECT * from Preteky WHERE id=$ID;
EOF;
    $count = 0;
    if(is_numeric($ID)){
      $ret = $db->query($sql);
      $ret2 = $db->query($sql1);
      $count = $ret2->fetchArray(PDO::FETCH_NUM);
    }
    if($count>0){
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        $pom = new PRETEKY();
        $pom->nacitaj($ID,$row['nazov'],$row['datum'],$row['deadline'], $row['aktiv'], $row['poznamka']);
      }
      // echo "Operation done successfully"."<br>";    //////////////
      $db->close();
      return $pom;
    }
      else{echo'Zvoleny pretek neexistuje';}
  }

/**
*vymaze pretek z DB podla id objektu PRETEKY
*/
  static function vymaz_pretek($ID){
    $db = napoj_db();
    $sql =<<<EOF
       DELETE FROM Preteky WHERE id = $ID;
       DELETE FROM Prihlaseni WHERE id_pret = $ID;
       DELETE FROM Kategorie_pre WHERE id_pret = $ID;
EOF;
    $ret = $db->exec($sql);
    echo'<tr><td><font color="green">Pretek bol zmazaný.</font></td></tr>';
    if(!$ret){
      echo $db->lastErrorMsg();
    }
    $db->close();
  }
  static function otoc_datum($datum){
    $datum = explode(' ',$datum);
    $dat = explode('-',$datum[0]);
    return $dat[2]."-".$dat[1]."-".$dat[0]." ".$datum[1];
  }

  /**

/**
*Vrati zoznam zoznam pretekov pre uzivatela
*/
static function vypis_zoznam(){
  $db = napoj_db();
  $sql =<<<EOF
    SELECT * from Preteky WHERE datetime(datum) > datetime('now','localtime') ORDER BY datum DESC;
EOF;
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $d1 = $row['deadline'];
    $d2 = $row['datum'];
    $d3 = new DateTime(date("Y-m-d H:i:s"));
    if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
      echo "<tr><td><a href='pretek.php?id=".$row['id']."' class='red'>".$row['nazov']."</a></td>";
    }
    if(strtotime($d1) > strtotime('1 days')){
      echo "<tr><td><a href='pretek.php?id=".$row['id']."' class='green'>".$row['nazov']."</a></td>";
    }
      echo "<td>".PRETEKY::otoc_datum($row['datum'])."</td>";
      echo "<td>".PRETEKY::otoc_datum($row['deadline'])."</td>";
      echo "</tr>";
   }
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}
/**
*Vrati zoznam zoznam pretekov pre admina
*/
static function vypis_zoznam_admin(){
    $db = napoj_db();
    $sql =<<<EOF
      SELECT * from Preteky WHERE datetime(datum) >= datetime('now','localtime') ORDER BY datum DESC;
EOF;
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      $d1 = $row['deadline'];
      $d2 = $row['datum'];
      $d3 = new DateTime(date("Y-m-d H:i:s"));
      if(strtotime($d1) < strtotime('1 days') && strtotime($d1) > strtotime('0 days')){
        echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'red'>".$row['nazov']."</a></td>";
      }
      if(strtotime($d1) > strtotime('1 days')){
        echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'green'>".$row['nazov']."</a></td>";
      }
      echo "<td>".PRETEKY::otoc_datum($row['datum'])."</td>";
      echo "<td>".PRETEKY::otoc_datum($row['deadline'])."</td>";
      echo "<td><a href='uprav_preteky.php?id=".$row['id']."'>Uprav</a></td>";

      echo "<form  action='' method='get'>
        <td><input type='submit' value='A/D' name='aktiv'>
            <input type='hidden' value=".$row['id']." name='id'>
        </td>
      </form>";
      echo "<form action='novy_pretek.php' method='get'>
      <td><input name='novy' type='submit' id='novy' value='Cc'>
          <input type='hidden' value=".$row['id']." name='id'>
      </td>
      </form>";
      echo "<form  action='' method='get'>
        <td><input type='submit' value='X' name='zmaz'>
            <input type='hidden' value=".$row['id']." name='id'>
        </td>
      </form>";
      echo "</tr>";
   }
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}
static function vypis_archiv(){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * from Preteky WHERE datetime(datum) < datetime('now','localtime') ORDER BY datum DESC;
EOF;
    $ret = $db->query($sql);
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      echo "<tr><td><a href='pretek.php?id=".$row['id']."&amp;ad=1' class = 'grey'>".$row['nazov']."</a></td>";
      echo "<td>".PRETEKY::otoc_datum($row['datum'])."</td>";
      echo "<td>".PRETEKY::otoc_datum($row['deadline'])."</td>";
      echo "<td><a href='uprav_preteky.php?id=".$row['id']."'>Uprav</a></td>";
      echo "<td><a href='vykon.php?id=". $row['id']."'>Osobný výkon</a></td>";
      echo "<td><a href='zhodnotenie.php?id=". $row['id']."'>Celkové hodnotenie</a></td>";

      echo "<form  action='' method='get'>
        <td><input type='submit' value='A/D' name='aktiv'>
            <input type='hidden' value=".$row['id']." name='id'>
        </td>
      </form>";
      echo "<form action='novy_pretek.php' method='get'>
      <td><input name='novy' type='submit' id='novy' value='Cc'>
          <input type='hidden' value=".$row['id']." name='id'>
      </td>
      </form>";
      echo "<form  action='' method='get'>
        <td><input type='submit' value='X' name='zmaz'>
            <input type='hidden' value=".$row['id']." name='id'>
        </td>
      </form>";
      echo "</tr>";
   }
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}
/**
*Aktivuje alebo deaktivuje pretek podla sucasneho stavu
*/
static function aktivuj($ID){
   $db = napoj_db();
   $pretek = new PRETEKY();
   $pretek = PRETEKY::vrat_pretek($ID);
   if($pretek->AKTIV=='0'){
   $sql =<<<EOF
      UPDATE Preteky set aktiv = "1" where id="$ID";
EOF;
    $ret = $db->exec($sql);
    echo'<tr><td><font color="green">Pretek bol aktivovaný</font></td></tr>';
  }
    else{
  $sql =<<<EOF
      UPDATE Preteky set aktiv = "0" where id="$ID";
EOF;
    $ret = $db->exec($sql);
    echo'<tr><td><font color="green">Pretek bol deaktivovaný</font></td></tr>';
 }
   $db->close();
}

static function deaktivuj($ID){
   $db = napoj_db();
   $sql =<<<EOF
      UPDATE Preteky set aktiv = "0" where id="$ID";
EOF;
   $ret = $db->exec($sql);
   //echo "Operation done successfully"."<br>";   ////////////////////////////////
   $db->close();
}

static function vypis_zoznam_oddiely(){
   $db = napoj_db();
   $sql =<<<EOF
      SELECT * from Oddiely;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['id'].'"/></td>';
    echo '<td>'.$row['id'].'</td><td>'.$row['nazov'] ."</td></tr>";
   }
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vypis_zoznam_kategorii(){
   $db = napoj_db();

   $pocet = 0;
   //select na zitenie poctu kategorii
   $sql =<<<EOF
      SELECT count(*) as poc from Kategorie;
EOF;
   $ret = $db->query($sql);
   while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $pocet= $row['poc'];
   }

   //select na vypisanie kategorii
   $sql =<<<EOF
      SELECT * from Kategorie;
EOF;
   $ret = $db->query($sql);

   $hlavicka_tab_kat = '<table border="1" style="width:100%">
          <tr>
            <td class="prvy"></td>
            <td class="prvy">ID kategórie</td>
            <td class="prvy">Názov</td>
          </tr>';

   //vypis laveho stlpca
   echo '<div id="kat_stl_1">';
   echo $hlavicka_tab_kat;
   $i=0;
   while($i<$pocet/2 and $row = $ret->fetchArray(SQLITE3_ASSOC) ){
      $i++;
     echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['id'].'"/></td>';
     echo '<td>'.$row['id'].'</td><td>'.$row['nazov'] ."</td></tr>";
   }
   echo '</table>';
   echo '</div>';

   //vypis praveho stlpca
   echo '<div id="kat_stl_2">';
   echo $hlavicka_tab_kat;
   $i=0;
   while($row = $ret->fetchArray(SQLITE3_ASSOC)){
      $i++;
     echo '<tr><td><input type="radio" name="incharge[]" value="'.$row['id'].'"/></td>';
     echo '<td>'.$row['id'].'</td><td>'.$row['nazov'] ."</td></tr>";
   }
   echo '</table>';
   echo '</div>';
   //echo "Operation done successfully"."<br>";
   $db->close();
}

static function vymaz_kategoriu($ID){
  $db = napoj_db();
  $sql =<<<EOF
     DELETE from Kategorie WHERE id = $ID;
EOF;
  $ret = $db->exec($sql);
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function vymaz_oddiel($ID){
  $db = napoj_db();
  $sql =<<<EOF
    DELETE from Oddiely WHERE id = $ID;
EOF;
  $ret = $db->exec($sql);
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function pridaj_kategoriu($nazov){
  $db = napoj_db();
  $sql =<<<EOF
    INSERT INTO Kategorie (nazov)
    VALUES ("$nazov");
EOF;
  $ret = $db->exec($sql);
  if(!$ret){
    echo $db->lastErrorMsg();
  }
  $db->close();
}

static function pridaj_oddiel($nazov){
  $db = napoj_db();
  $sql =<<<EOF
    INSERT INTO Oddiely (nazov)
    VALUES ("$nazov");
EOF;

  $ret = $db->exec($sql);
  if(!$ret){
    echo $db->lastErrorMsg();
  }
  $db->close();
}
//adept na vymazanie
static function vypis_zoznam_kategorii_table(){
  $db = napoj_db();
  $sql =<<<EOF
    SELECT * from Kategorie;
EOF;
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['nazov'].'"/></td>';
    echo '<td>'.$row['nazov'] ."</td></tr>";
  }
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function vypis_zoznam_pretek_table(){
  $db = napoj_db();
  $cislo = $_GET['id'];
  $sql =<<<EOF
      SELECT Kategorie.nazov,Kategorie.id from Kategorie_pre JOIN Kategorie ON Kategorie_pre.id_kat = Kategorie.id WHERE id_pret = "$cislo";
EOF;
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['id'].'" checked/></td>';
    echo '<td>'.$row['nazov'] ."</td></tr>";
  }
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function vypis_zoznam_ostatne_table(){
  $db = napoj_db();
  $cislo = $_GET['id'];
  $sql =<<<EOF
      SELECT * from Kategorie WHERE id NOT IN (SELECT id_kat FROM Kategorie_pre WHERE id_pret = "$cislo");
EOF;
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['id'].'"/></td>';
    echo '<td>'.$row['nazov'] ."</td></tr>";
  }
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function vypis_vsetky_kategorie_table(){
  $db = napoj_db();
  $sql =<<<EOF
    SELECT * from Kategorie;
EOF;
  $ret = $db->query($sql);
  while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    echo '<tr><td><input type="checkbox" name="incharge[]" value="'.$row['id'].'"/></td>';
    echo '<td>'.$row['nazov'] ."</td></tr>";
  }
  //echo "Operation done successfully"."<br>";
  $db->close();
}

static function pridaj_kat_preteku($id_pret, $id_kat){
  $db = napoj_db();
  $sql =<<<EOF
    INSERT INTO Kategorie_pre (id_pret, id_kat)
    VALUES ("$id_pret","$id_kat");
EOF;
  $ret = $db->exec($sql);
  if(!$ret){
    echo $db->lastErrorMsg();
  }
  $db->close();
}

static function zmaz_kat_preteku($id_pret){
  $db = napoj_db();
  $sql =<<<EOF
    DELETE FROM Kategorie_pre WHERE id_pret = "$id_pret";
EOF;
  $ret = $db->exec($sql);
  if(!$ret){
    echo $db->lastErrorMsg();
  }
  $db->close();
}


static function zapis_cas($ID_PRET,$ID_POUZ,$cas){
  $db = napoj_db();
  $sql =<<<EOF
    INSERT INTO Zhodnotenie (id_pret,id_pouz,cas) VALUES ("$ID_PRET","$ID_POUZ","$cas");
EOF;
  $db->exec($sql);
  $db->close();
}

static function uprav_cas($ID_PRET,$ID,$cas){
  $db = napoj_db();
  $sql =<<<EOF
    UPDATE Zhodnotenie set cas = "$cas" WHERE id = $ID;
EOF;
  $db->exec($sql);
  $db->close();
}

static function exportuj_zhodnotenie($id){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT *
      FROM Zhodnotenie JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id
                       JOIN Prihlaseni ON Prihlaseni.id_pouz=Pouzivatelia.id
                       JOIN Kategorie ON Zhodnotenie.id_kat = Kategorie.id
      WHERE Zhodnotenie.id_pret = $id
      ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
EOF;
  $ret = $db->query($sql);
  $myfile = fopen("zhodnotenie.csv", "w") or die("Unable to open file!");
    fputcsv($myfile, array("KATEGORIA","MENO","PRIEZVISKO","CAS"), ";");
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      fputcsv($myfile,array($row['nazov'],$row['meno'],$row['priezvisko'],$row['cas']),";");
    }
  echo '<meta http-equiv="refresh" content="0;URL=zhodnotenie.csv" />';
}

static function vypis_zhodnotenie($ID_PRET){
  $db = napoj_db();
    $sql =<<<EOF
      SELECT * FROM Zhodnotenie JOIN Prihlaseni ON Prihlaseni.id_pouz=Zhodnotenie.id_pouz AND Prihlaseni.id_pret=Zhodnotenie.id_pret
      JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id
      JOIN Kategorie ON Kategorie.id = Zhodnotenie.id_kat
      WHERE Zhodnotenie.id_pret = $ID_PRET
      ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
EOF;

  $ret = $db->query($sql);
  while ($row = $ret->fetchArray(SQLITE3_ASSOC)){
    ?>
    <tr><td><?php echo $row['nazov']?></td><td><?php echo $row["meno"] ?></td><td><?php echo $row["priezvisko"] ?></td><td><?php echo $row["cas"] ?></td></tr>
    <?php
  }
  if (isset($_SESSION["admin"]) && $_SESSION["admin"]){
    echo '<tr><td><form method="post"><input type="submit" name="export" value="Exportovať"></form></td><td></td><td><form method="post"><input type="submit" name="upravuj" value="Uprav"></form></td></tr>';
  }
  $db->close();
}

static function vypis_zhodnotenie_admin($ID_PRET){
  $db = napoj_db();
  $sql =<<<EOF
    SELECT * FROM Zhodnotenie JOIN Prihlaseni ON Prihlaseni.id_pouz=Zhodnotenie.id_pouz AND Prihlaseni.id_pret=Zhodnotenie.id_pret JOIN Pouzivatelia ON Zhodnotenie.id_pouz = Pouzivatelia.id WHERE Zhodnotenie.id_pret = $ID_PRET  ORDER BY Prihlaseni.id_kat,Zhodnotenie.cas ASC;
EOF;
  $ret = $db->query($sql);
  $i = 0;
  while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    echo "<tr>";
    echo "<td>".$row['id_kat']."</td>";
    echo "<td>".$row['meno']."</td>";
    echo "<td>".$row['priezvisko']."</td>";
    echo '<td><input type="text" name="cas'.$i.'" value = "';
    if (isset($_POST["cas".$i])){
      echo $_POST["cas".$i];
    }
    else{
      echo $row["cas"];
    }
    echo '" required/><input type="hidden" name="id'.$i.'" value="'.$row["id"].'"/></td>';
    echo "</tr>";
    $i++;
  }
  echo '<tr><td></td><td></td><td><input type="submit" name="uprav" value="Ulož zmeny"><input type="hidden" name="pocet" value="'.$i.'"></td></tr>';
  $db->close();
}

static function odstran_duplicity(){
  $db = napoj_db();
  $sql =<<<EOF
      CREATE TABLE duplicity
      (id INTEGER PRIMARY KEY   AUTOINCREMENT,
      id_pouz             INTEGER    NOT NULL,
      id_pret        INTEGER    NOT NULL,
      id_kat        INTEGER    NOT NULL,
      poznamka TEXT
      );
EOF;
  $db->exec($sql);
  $sql =<<<EOF
    INSERT INTO duplicity(id_pouz, id_pret, id_kat,poznamka) SELECT Prihlaseni.id_pouz, Prihlaseni.id_pret, Prihlaseni.id_kat, Prihlaseni.poznamka FROM Prihlaseni GROUP BY id_pret, id_pouz;
EOF;
  $db->exec($sql);
  $sql =<<<EOF
         DROP TABLE Prihlaseni;
EOF;
  $db->exec($sql);
  $sql =<<<EOF
         ALTER TABLE duplicity RENAME to Prihlaseni;
EOF;
  $db->exec($sql);
  $db->close();
  }

  static function updateExport($retazec){
    $db = napoj_db();
    $sql =<<<EOF
    UPDATE Exporty SET retazec = "$retazec";
EOF;
    $ret = $db->exec($sql);
    $db->close();
  }

  static function exportuj($id_pret){
    $prepis = array("meno"=>"MENO","priezvisko"=>"PRIEZVISKO","os_i_c"=>"OS.ČÍSLO","cip"=>"ČIP","nazov"=>"KATEGÓRIA","poznamka"=>"POZNÁMKA");
    $myfile = fopen("zoznam.txt", "w") or die("Nedá sa otvoriť súbor. Skontrolujte, či sa v priečinku source nachádza súbor zoznam.txt ak nie vytvorte ho.");
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename=zoznam.csv;');
    header('Content-Transfer-Encoding: binary');
    $myfilecsv = fopen('php://output', 'w');
    fputs($myfilecsv, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF)  ));
    $db = napoj_db();
    $sql =<<<EOF
    SELECT * FROM Exporty;
EOF;
    $ret = $db->query($sql);
    $vyber = "";
    if ($row = $ret->fetchArray(SQLITE3_ASSOC)){
      $vyber = $row['retazec'];
    }
    $stlpce = explode(",", $vyber);
    $hlavicka = array();
    foreach ($stlpce as $s) {
      array_push($hlavicka, $prepis[$s]);
    }
    fputcsv($myfile,$hlavicka,";");
    fputcsv($myfilecsv,$hlavicka,";");
    $sql =<<<EOF
      SELECT $vyber
      FROM Prihlaseni JOIN (SELECT id,meno,priezvisko,os_i_c,cip FROM Pouzivatelia) as pouz
      ON Prihlaseni.id_pouz = pouz.id
      JOIN Kategorie ON Prihlaseni.id_kat = Kategorie.id
      WHERE id_pret = $id_pret;
EOF;
    $ret = $db->query($sql);
    while ($row = $ret->fetchArray(SQLITE3_ASSOC)){
      fputcsv($myfile,$row,";");
      fputcsv($myfilecsv,$row,";");
    }
    exit;
    $db->close();
    fclose($myfile);
    fclose($myfilecsv);
  }
}
 ?>
