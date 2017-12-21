<script
              src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
              integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g="
              crossorigin="anonymous">
            
</script>
<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
?>

<!DOCTYPE HTML>
<html onclick='klik()'>
<?php
hlavicka("Kmeňoví členovia");
if(isset($_POST["vymaz"])){
    if(vymaz_clena($_POST["id_clen"])){
        echo "<h4 align='center'>Člen bol odstrátený zo zoznamu kmeňových členov.</h4>";
    }
}

vypis_kmenovych_clenov();

paticka();
?>

</html>     



<?php
// === PHP Functions ===
function vypis_kmenovych_clenov(){

        ?>
        <div>
        <h1 style="text-align:center;">Kmeňoví členovia</h1>
        <table style="width:100%;" border=1 class="tabulkaVykonou" id="tabulkaKmenovychClenov">
        <tr>
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
    //POZNAMKA TU MA BYT?????
    $cesta_obrazok = vrat_cestu_obrazka($row['id']);
    echo "<tr>";
            
            echo "<td contenteditable>".$row['meno']."<span class='tooltiptext'><img src='".$cesta_obrazok."' alt='fotka' height='400' width='450'></span></td>";
            
            echo "<td contenteditable onclick='klik()' id='td_priezvisko'>".$row['priezvisko']."</td>";
            
            echo "<td contenteditable>".$row['pohlavie']."</td>";
            
            echo "<td contenteditable onclick='klik()' onkeypress='save(event, ".$row['meno'].",".$row['priezvisko'].",".$row['id_oddiel'].",".$row['os_i_c'].", ".$row['cip'].", ".$row['poznamka'].",".$row['uspech'].", ".$row['pohlavie'].", ".$row['datum_narodenia'].",".$row['krajina_narodenia'].",".$row['statna_prislusnost'].",".$row['krajina_trvaleho_pobytu'].",".$row['ulica'].",".$row['cislo_domu'].",".$row['psc'].",".$row['mesto'].",".$row['telefon'].",".$row['mail'].")'>".$row['datum_narodenia']."</td>";
            
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
    //echo "</div>";
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

?>

<script>


var timer = null;
$('#tabulkaKmenovychClenov').keydown(function(){
       clearTimeout(timer); 
       timer = setTimeout(doStuff, 1000)
});

function doStuff() {
    //alert('Databáza sa aktualizovala.');
}

function klik(){
            console.log('klikaaaam');
        };

function save(e, MENO, PRIEZVISKO, oddiel, OS_I_C, CHIP, POZNAMKA, uspech, pohlavie, narodenie,krajina_narodenia,statna_prislusnost,krajina_trvaleho_pobytu,ulica,cislo_domu,psc,mesto,telefon,mail){
            console.log('stacila som');
            if (e.keyCode == 13) {
                console.log('savujem');

            var table = document.getElementById('tabulkaKmenovychClenov');
            var t = document.getElementById('td_priezvisko');
            /*for (var r = 0, n = table.rows.length; r < n; r++) {
                for (var c = 0, m = table.rows[r].cells.length; c < m-1; c++) {
                    console.log(table.rows[r].cells[c].innerHTML);
                }
            }*/
            console.log(t.innerHTML);
            var x=document.getElementById('tabulkaKmenovychClenov');
            var c ='<?php 
            echo uprav_pouzivatel(MENO, PRIEZVISKO, oddiel, OS_I_C, CHIP, POZNAMKA, uspech);
            echo uprav_kmen_clen(pohlavie, narodenie,krajina_narodenia,statna_prislusnost,krajina_trvaleho_pobytu,ulica,cislo_domu,psc,mesto,telefon,mail); 
            ?>';
            alert(c);
            }
    }


</script>



