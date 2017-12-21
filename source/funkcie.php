<?php
$heslo="olympiada";
date_default_timezone_set('UTC');
class MyDB extends SQLite3{
  function __construct(){
    $this->open('databaseNew.db');
  }
}  

function napoj_db(){
  $db = new MyDB();
  if(!$db){
    echo $db->lastErrorMsg();
    return false;
  } 
  else {
    return $db;
  }
}

function vymaz_obrazok($id){
  if(file_exists('pictures/'.$id.'.gif')){
    $subor = 'pictures/' . $id .'.gif';
    unlink($subor);
  }
  if(file_exists('pictures/'.$id.'.png')){
    $subor = 'pictures/' . $id .'.png';
    unlink($subor);
  }
  if(file_exists('pictures/'.$id.'.jpg')){
    $subor = 'pictures/' . $id .'.jpg';
    unlink($subor);
  }
  if(file_exists('pictures/'.$id.'.jpeg')){
    $subor = 'pictures/' . $id .'.jpeg';
    unlink($subor);
  }
}

function pridaj_obrazok($id){
  if(($_FILES['obrazok']['type'] != 'image/png') && ($_FILES['obrazok']['type'] != 'image/jpg') && ($_FILES['obrazok']['type'] != 'image/jpeg')
  && ($_FILES['obrazok']['type'] != 'image/gif') && ($_FILES['obrazok']['type'] != 'image/bmp')) { }
  else{
    $type = $_FILES['obrazok']['type'];
    $pripona = explode("/",$type);
    if(file_exists('pictures/'.$id.'.png')){
      $subor = 'pictures/' . $id .'.png';
      unlink($subor);
      pridaj_obrazok($id);
    }
    else if(file_exists('pictures/'.$id.'.jpg')){
      $subor = 'pictures/' . $id .'.jpg';
      unlink($subor);
      pridaj_obrazok($id);
    }
    else if(file_exists('pictures/'.$id.'.jpeg')){
      $subor = 'pictures/' . $id .'.jpeg';
      unlink($subor);
      pridaj_obrazok($id);
    }
    else if(file_exists('pictures/'.$id.'.gif')){
      $subor = 'pictures/' . $id .'.gif';
      unlink($subor);
      pridaj_obrazok($id);
    }
    else{
      if (isset($_FILES['obrazok'])) {
        $novy_nazov = '';
        if ($_FILES['obrazok']['error'] == UPLOAD_ERR_OK) {
          if (is_uploaded_file($_FILES['obrazok']['tmp_name'])) {
            $novy_nazov = 'pictures/' . $id .'.'.$pripona[1].'';
            $podarilosa = move_uploaded_file($_FILES['obrazok']['tmp_name'], $novy_nazov);
            if ($podarilosa) { }
            $novy_nazov = '';
          }
        }
      }
    }
  }
}

function pridaj_kmenovy_clen($id){
$db = napoj_db();
$sql =<<<EOF
          INSERT INTO Kmenovi_clenovia DEFAULT VALUES;
EOF;
$id=$_GET['id'];
$db->exec($sql);
$sql =<<<EOF
          SELECT last_insert_rowid() AS id;
EOF;

$ret = $db->query($sql);
$row = $ret->fetchArray(SQLITE3_ASSOC);
$kmenovi_id = $row['id'];
$db->exec($sql);
$sql =<<<EOF
          UPDATE Pouzivatelia SET id_kmen_clen = $kmenovi_id WHERE id = $id;
EOF;
$db->exec($sql);
$db->close();
}

function je_kmenovy($id){
$db = napoj_db();
$sql =<<<EOF
          SELECT count(id) as c FROM Pouzivatelia WHERE id = $id AND id_kmen_clen NOT NULL;
EOF;
$ret = $db->query($sql);
$row = $ret->fetchArray(SQLITE3_ASSOC);
$p=$row['c'];
$db->exec($sql);
if ($p > 0) return true;
return false;
}

function zobraz_obrazok($id){
  if(file_exists('pictures/'.$id.'.gif')){
    $subor = 'pictures/' . $id .'.gif';
    echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
    ?>
    <label for="obrazok">Pridaj foto:</label>
    <br>
    <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
    <input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
    <input type="submit" name="posli3" value="Zmeň foto"> <br>
    <?php
  }
  else  if(file_exists('pictures/'.$id.'.png')){
    $subor = 'pictures/' . $id .'.png';
    echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
    ?>
    <label for="obrazok">Pridaj foto:</label>
    <br>
    <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
    <input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
    <input type="submit" name="posli3" value="Zmeň foto"> <br>
    <?php
  }
  else  if(file_exists('pictures/'.$id.'.jpg')){
    $subor = 'pictures/' . $id .'.jpg';
    echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
    ?>
    <label for="obrazok">Pridaj foto:</label>
    <br>
    <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
    <input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
    <input type="submit" name="posli3" value="Zmeň foto"> <br>
    <?php
  }
  else  if(file_exists('pictures/'.$id.'.jpeg')){
    $subor = 'pictures/' . $id .'.jpeg';
    echo'<a href="'.$subor.'" class="thumbnail" ><img class="img" src="'.$subor.'" alt="" /></a>';
    ?>
    <label for="obrazok">Pridaj foto:</label>
    <br>
    <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
    <input type="submit" name="vymaz" onclick="return confirm('Naozaj chcete vymazať fotku?');" value="Vymaž foto"><br>
    <input type="submit" name="posli3" value="Zmeň foto"> <br>
    <?php
  }
  else{
    echo'<img src="pictures/no_photo.jpg" alt="" />';?>
    <label for="obrazok">Pridaj foto:</label>
    <br>
    <input type="file" name="obrazok" id="obrazok" accept="image/png, image/jpg, image/gif, image/jpeg"><br>
    <input type="submit" name="posli3" value="Pridaj"><br> <?php
  }
}


function vrat_cestu_obrazka($id){
  if(file_exists('pictures/'.$id.'.gif')){
    return 'pictures/' . $id .'.gif';
  }
  else  if(file_exists('pictures/'.$id.'.png')){
    return 'pictures/' . $id .'.png';
  }
  else  if(file_exists('pictures/'.$id.'.jpg')){
    return 'pictures/' . $id .'.jpg';
  }
  else  if(file_exists('pictures/'.$id.'.jpeg')){
    return 'pictures/' . $id .'.jpeg';
  }
  else{
    return "pictures/no_photo.jpg";
  }
}

function over($text){
  return strlen($text) >0;
}

function hlavicka($meno=""){
  if (isset($_GET['odhlas'])){
    $_SESSION['admin']=0;
    echo '<meta http-equiv="refresh" content="0; URL=index.php">';
  }
  ?>
  <head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title><?php echo $meno?></title>
  <link rel="stylesheet" href="styl/styly.css">
  <link rel="stylesheet" href="sorter/themes/blue/style.css">
  <link rel="stylesheet" href="thumbnailviewer.css">
  <script type="text/javascript" src="js/script.js"></script>
  </head>
  <body>
  <header>
  <h1><?php echo $meno?></h1>
  <nav>
  <a href="index.php">Domov</a>
  <?php
  if (isset($_SESSION["admin"]) && $_SESSION["admin"]){
    ?>
    <a href="archiv.php">Archív</a>
    <a href="kmenovi_clenovia.php">Kmeňoví členovia</a>
    <a href="?odhlas=1">Odhlásenie</a>
    <?php
  }
  ?>
  </nav>
  </header>
<?php
}

function paticka(){ ?>
  <footer>
  <div id="footer"><A HREF="prihlasenie.php">TIS</A> - projekt 2015 pre ŠK Sandberg</div>
  </footer>
  </body>
<?php
}


////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// pouzit na export
/*function vypis_db(){
    $db = napoj_db();
    $sql =<<<EOF
           CREATE TABLE temp
      (ID INTEGER NOT NULL,
      MENO              VARCHAR    NOT NULL,
      PRIEZVISKO        VARCHAR    NOT NULL,
      OS_I_C            VARCHAR,
      CHIP              INT,
      POZNAMKA          VARCHAR,
      USPECH            VARCHAR,
      oddiel            INTEGER
      );
EOF;
$id=$_GET['id'];
$db->exec($sql);
$sql =<<<EOF
          INSERT INTO temp(ID, MENO, PRIEZVISKO, OS_I_C, CHIP, POZNAMKA, USPECH,oddiel) SELECT POUZIVATELIA.* FROM POUZIVATELIA INNER JOIN PRIHLASENY ON POUZIVATELIA.ID = PRIHLASENY.ID_POUZ  WHERE (PRIHLASENY.ID_PRET = $id);
EOF;
$db->exec($sql);
$sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) > 1);
EOF;
$ret = $db->query($sql);


    $myfile = fopen("zoznam.txt", "w") or die("Unable to open file!");
//    fputcsv($myfile, array("MENO","PRIEZVISKO","OSOBNE CISLO","CIP","POZNAMKA"), ";");
//    Changed by RB @ Sep 19 2017: new format for IS SZOS:
    fputcsv($myfile, array("OSOBNE CISLO","Kategoria?","CIP","PRIEZVISKO","MENO","POZNAMKA"), ";");
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
      fputcsv($myfile,array($row['OS_I_C'],$row['CHIP'],$row['CHIP'],$row['PRIEZVISKO'],$row['MENO'],$row['POZNAMKA']),";");
    }
      $sql =<<<EOF
         SELECT temp.* FROM temp WHERE temp.CHIP in (SELECT temp.CHIP from temp GROUP BY temp.CHIP HAVING COUNT (temp.CHIP) = 1);
EOF;

$ret = $db->query($sql);
      while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
        fputcsv($myfile,array($row['MENO'],$row['PRIEZVISKO'],$row['OS_I_C'],$row['CHIP'],$row['POZNAMKA']),";");
    }
       // echo "Operation done successfully"."<br>";      ////////////////////////////
      $sql =<<<EOF
         DROP TABLE TEMP;
EOF;

       $db->exec($sql);
       fclose($myfile);
       $db->close();
  }
*/
?>