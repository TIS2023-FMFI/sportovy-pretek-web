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
//doplnit else
vypis_kmenovych_clenov();

paticka();
?>

</html>     

<?php
// === PHP Functions ===
function vypis_kmenovych_clenov(){
        // onclick="klik()"
        ?>
        <div>
        <h1 style="text-align:center;">Kmeňoví členovia</h1>
        <table style="width:100%;" border=1 class="tabulkaVykonou" id="tabulkaKmenovychClenov">
        <tr>
                <th class="prvy">Meno</th>
                <th class="prvy ">Priezisko</th>
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
     
    while($row = $ret->fetchArray(SQLITE3_ASSOC)){
    //POZNAMKA TU MA BYT????
    
    $cesta_obrazok = vrat_cestu_obrazka($row['id']);
    $id_kmen = $row['id_kmen_clen'];
    $datum = 'datum_narodenia';
    
    echo "<tr>";
    
            echo "<td contenteditable
              ><a class='fntb' href='profil.php?id=".$row['id']."'>".$row['meno']."</a><span class='tooltiptext'><img src='".$cesta_obrazok."' alt='fotka' height='400' width='450'></span></td>";
            
            echo "<td contenteditable>".$row['priezvisko']."</td>";
            
            echo "<td contenteditable>".$row['pohlavie']."</td>";
            
            echo "<td contenteditable class='datum_narodenia' onclick='save2(event,".$datum.",".$id_kmen.")'>".$row['datum_narodenia']."</td>";

            echo "<td contenteditable>".$row['krajina_narodenia']."</td>";
            
            echo "<td contenteditable>".$row['statna_prislusnost']."</td>";
            
            echo "<td contenteditable>".$row['krajina_trvaleho_pobytu']."</td>";
            
            echo "<td contenteditable>".$row['ulica']."</td>";
            
            echo "<td contenteditable>".$row['cislo_domu']."</td>";
            
            echo "<td contenteditable>".$row['psc']."</td>";
            
            echo "<td contenteditable>".$row['mesto']."</td>";
            
            echo "<td contenteditable>".$row['telefon']."</td>";
            
            echo "<td contenteditable>".$row['mail']."</td>";
          
            echo "<td contenteditable>".$row['cip']."</td>";
            
            echo "<td contenteditable>".$row['os_i_c']."</td>";
            
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

function vyprint(){
    $db = napoj_db();
    if ($db) {
        $sql = <<<EOF
          DELETE FROM Kmenovi_clenovia WHERE id = 1;
EOF;
        $db->exec($sql);
        $db->close();
        }
    return true;
}

function uprav($obsah, $stlpec, $id){
  $db = napoj_db();
    if ($db) {
        $sql = <<<EOF
          UPDATE Kmenovi_clenovia SET $stlpec = $obsah WHERE id = $id;
EOF;
        $db->exec($sql);
        $db->close();
        }
    return true;
}

?>

<script>
/*console.log();
console.log("cakam");
var timer = null;
$('#tabulkaKmenovychClenov').keydown(function(){
       clearTimeout(timer); 
       timer = setTimeout(doStuff, 100);
});

function doStuff() {
    klik();
}

function klik(){
    console.log('klikaaaam');
}


function save(e, id_pouz, id_kmen, MENO=null, PRIEZVISKO=null, oddiel=null, OS_I_C=null, CHIP=null, POZNAMKA="", uspech="", pohlavie = null, narodenie = null,krajina_narodenia  = null,statna_prislusnost  = null,krajina_trvaleho_pobytu  = null,ulica  = null,cislo_domu  = null,psc  = null,mesto  = null,telefon  = null,mail){
            mail = (typeof mail !== 'undefined') ?  mail : null;
            console.log('stacila som');
            
            var table = document.getElementById('tabulkaKmenovychClenov');
            var t = document.getElementById('td_priezvisko');
            console.log(t.innerHTML);
            var x=document.getElementById('tabulkaKmenovychClenov');
            var c ='<?php 
            echo $po = new POUZIVATELIA();
            echo $po->nacitaj(id, MENO, PRIEZVISKO, oddiel, OS_I_C, CHIP, POZNAMKA, uspech);
            echo $po->nacitaj_kmenoveho(pohlavie, narodenie,krajina_narodenia,statna_prislusnost,krajina_trvaleho_pobytu,ulica,cislo_domu,psc,mesto,telefon,mail, id_kmen_clen);
            echo $po->uprav_pouzivatela(MENO, PRIEZVISKO, oddiel, OS_I_C, CHIP, POZNAMKA, uspech);
            echo $po->uprav_kmenove_info(pohlavie, narodenie,krajina_narodenia,statna_prislusnost,krajina_trvaleho_pobytu,ulica,cislo_domu,psc,mesto,telefon,mail); 
            ?>';
            alert(c);
            
}*/
function save2(e, stlpec, id){
  var table = document.getElementById('stlpec');
  var cont = table.innerHTML;
  var ex = '<?php 
    echo uprav(cont, stlpec, id);
  ?>';
  alert(ex);
}


</script>

