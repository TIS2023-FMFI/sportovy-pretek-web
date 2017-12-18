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
        <table style="width:100%;" border=1 class="tabulkaVykonou">
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
            
            echo "<td>".$row['meno']."<span class='tooltiptext'><img src='".$cesta_obrazok."' alt='fotka' height='400' width='450'></span></td>";
           
            echo "<td>".$row['priezvisko']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td>".$row['pohlavie']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['datum_narodenia']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['krajina_narodenia']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['statna_prislusnost']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['krajina_trvaleho_pobytu']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['ulica']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['cislo_domu']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['psc']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['mesto']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['telefon']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['mail']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['cip']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
            echo "<td contenteditable>".$row['os_i_c']."<input type='hidden' name='id_clen' value='".$row['id_kmen_clen']."'></td>";
            
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

/*echo "<script>
   $('.profilovka').hover(function(){
        $(this).find('.tooltip').stop().fadeIn();

    },
    function(){
        $(this).find('.tooltip').stop().fadeOut();
    })
</script>";*/


?>
<script src="http://code.jquery.com/jquery-1.11.3.min.js">
$(function () { $("td").dblclick(function () { 
log("double"); 
 var OriginalContent = $(this).text();
 $(this).addClass("cellEditing"); 
 $(this).html("<input type='text' value='" + OriginalContent + "' />"); 
 $(this).children().first().focus(); 
 

 $(this).children().first().keypress(function (e) { 
    if (e.which == 13) { var newContent = $(this).val(); 
        $(this).parent().text(newContesnt); $(this).parent().removeClass("cellEditing"); } }); 
        $(this).children().first().blur(function(){ 
            $(this).parent().text(OriginalContent); 
            $(this).parent().removeClass("cellEditing"); }); 
        }); 
    });
</script>
