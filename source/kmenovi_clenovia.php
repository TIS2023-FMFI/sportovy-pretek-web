<script
        src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g="
        crossorigin="anonymous"></script>

<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
?>

<!DOCTYPE HTML>
<html>
<?php
hlavicka("Kmeňoví členovia");
if(isset($_POST["vymaz"])){
    if(vymaz_clena($_POST["id_clen"])){
        echo "<h4 align='center'>Člen bol odstrátený zo zoznamu kmeňových členov.</h4>";
    }
}

if ((isset($_POST['id'])) && !empty($_POST['id']) && (isset($_POST['obsah'])) && !empty($_POST['obsah']) && (isset($_POST['stlpec'])) && !empty($_POST['stlpec'])) {
  $id = $_POST['id'];
  $obsah = $_POST['obsah'];
  $stlpec = $_POST['stlpec'];

  echo uprav($id, $obsah, $stlpec);
}

vypis_kmenovych_clenov();



paticka();
?>


</html>


<?php
// === PHP Functions ===
$obsah = "";
$stlpec = "";
$id = "";
function vypis_kmenovych_clenov(){

        ?>
        <div>
        <h1 style="text-align:center;">Kmeňoví členovia</h1>
        <table style="width:100%;" border=1 class="tabulkaVykonou">
        <tr>
                <th class="prvy"></th>
                <th class="prvy">Meno</th>
                <th class="prvy">Priezisko</th>
                <th class="prvy">Pohlavie</th>
                <th class="prvy">Dátum narodenia</th>
                <th class="prvy">Krajina narodenia</th>
                <th class="prvy">Štátna príslušnosť</th>
                <th class="prvy">Krajina trvalého bydliska</th>
                <th class="prvy">Ulica</th>
                <th class="prvy">Číslo domu</th>
                <th class="prvy">PSČ</th>
                <th class="prvy">Mesto</th>
                <th class="prvy">Telefón</th>
                <th class="prvy">Mail</th>
                <th class="prvy">Číslo čipu</th>
                <th class="prvy">Registračné číslo</th>
                <th class="prvy"></th>
            </tr>
    <?php
    $db = napoj_db();
    $sql =<<<EOF
         SELECT * from Kmenovi_clenovia AS k JOIN Pouzivatelia AS p ON p.id_kmen_clen = k.id ORDER BY p.priezvisko ASC;
EOF;
    $ret = $db->query($sql);
     
    while($row = $ret->fetchArray(SQLITE3_ASSOC) ){
    $cesta_obrazok = vrat_cestu_obrazka($row['id']);
    $id_kmen = $row['id_kmen_clen'];
    $datum = 'priezvisko';

    echo "<tr>";
            echo "<td><span class='tooltiptext'><img src='".$cesta_obrazok."' alt='fotka' height='400' width='450'></span><a class='fntb' href='profil.php?id=".$row['id']."'>Profil</a></td>";

            echo "<td contenteditable name='meno' id='meno".$row['id']."' onkeyup='save2(event, this,".$row['id'].")'>".$row['meno']."</td>";
           
            echo "<td contenteditable name='priezvisko' id='priezvisko".$row['id']."' onkeyup='save2(event, this,".$row['id'].")'>".$row['priezvisko']."</td>";
            
            echo "<td contenteditable name='pohlavie' id='pohlavie".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['pohlavie']."</td>";
            
            echo "<td contenteditable name='datum_narodenia' id='datum_narodenia".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['datum_narodenia']."</td>";
            
            echo "<td contenteditable name='krajina_narodenia' id='krajina_narodenia".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['krajina_narodenia']."</td>";
            
            echo "<td contenteditable name='statna_prislusnost' id='statna_prislusnost".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['statna_prislusnost']."</td>";
            
            echo "<td contenteditable name='krajina_trvaleho_pobytu' id='krajina_trvaleho_pobytu".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['krajina_trvaleho_pobytu']."</td>";
            
            echo "<td contenteditable name='ulica' id='ulica".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['ulica']."</td>";
            
            echo "<td contenteditable name='cislo_domu' id='cislo_domu".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['cislo_domu']."</td>";
            
            echo "<td contenteditable name='psc' id='psc".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['psc']."</td>";
            
            echo "<td contenteditable name='mesto' id='mesto".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['mesto']."</td>";
            
            echo "<td contenteditable name='telefon' id='telefon".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['telefon']."</td>";
            
            echo "<td contenteditable name='mail' id='mail".$row['id']."' onkeyup='save2(event, this,".$row['id_kmen_clen'].")'>".$row['mail']."</td>";
            
            echo "<td contenteditable name='cip' id='cip".$row['id']."' onkeyup='save2(event, this,".$row['id'].")'>".$row['cip']."</td>";
            
            echo "<td contenteditable name='os_i_c' id='os_i_c".$row['id']."' onkeyup='save2(event, this,".$row['id'].")'>".$row['os_i_c']."</td>";
            
            echo "<td><form method='post'><input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'><input type='submit' name='vymaz' value='Vymaž'></form></td>";
    echo "</tr>";
    
    } 
    // echo "Operation done successfully"."<br>";   ///////////////////
    $db->exec($sql);
    $db->close();
    
    ?>

        </table>

        </div><?php

        return;

}

function vymaz_clena($id){
    $db = napoj_db();

    if ($db) {
        $sql = <<<EOF
          DELETE FROM Kmenovi_clenovia WHERE id = $id;
EOF;
        $db->exec($sql);
        $sql1 = <<<EOF
          UPDATE Pouzivatelia SET id_kmen_clen = NULL WHERE id_kmen_clen = $id;
EOF;
        $db->exec($sql1);
        $db->close();

        return true;
    }
    return false;

}



function uprav($id, $obsah, $stlpec){
  $db = napoj_db();
  if ($stlpec == "meno" || $stlpec == "priezvisko" || $stlpec == "os_i_c" || $stlpec == "cip"){
    $sql = <<<EOF
          UPDATE Pouzivatelia SET $stlpec = '$obsah' WHERE id = $id; 
EOF;
  } 
  else{
    $sql = <<<EOF
          UPDATE Kmenovi_clenovia SET $stlpec = '$obsah' WHERE id = $id; 
EOF;
  }
  if ($db) {
        
        $db->exec($sql);
        $db->close();
  }
  return true;
       
}


?>


