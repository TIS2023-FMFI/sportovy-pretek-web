<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
$navodik = false;
$pretekId = $_GET['id'];

//vymazanie cookies typu s nazvom kat_pretekar, posledni_prihlaseni ak by mal niekto zapamatane v prehliadaci z predchadzajucej verzie
//ale s tym uz nepracujeme tak to asi ani neni treba
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        if (strpos($name, 'kat_pretekar') !== false  || strpos($name, 'posledni_prihlasni') !== false){
          setcookie($name, '', time() - 3600, '/');
        }
    }
}


//export
if(isset($_POST['export'])){
  PRETEKY::exportuj($pretekId);
}
if (isset($_POST['prihlas'])&&isset($_POST['checked'])){
    $selected = array();
    if(is_array($_POST['incharge'])){
      $prihlaseni = array();
      foreach($_POST['incharge'] as $val){
        if($val!="-"){
          $pieces = explode(":", $val);
          $id_kat=$pieces[0];
          $id_pouz=$pieces[1];
          array_push($selected, $id_pouz);
          if(in_array($id_pouz, $_POST['checked'])){
            $p = POUZIVATELIA::vrat_pouzivatela($id_pouz);
            array_push($prihlaseni, $p->meno." ".$p->priezvisko);
            if (isset($_COOKIE['prihlaseni'])){
              $cookies_prihlaseni=$_COOKIE['prihlaseni'].','.$id_pouz;
            }
            else{
              $cookies_prihlaseni=$id_pouz;
            }
            setcookie("prihlaseni", $cookies_prihlaseni, time() + (86400 * 366),"/");
            if (isset($_POST['poznamka'.$id_pouz])){
              $poznamka=$_POST['poznamka'.$id_pouz];
            }
            else{
              $poznamka="";
            }
            PRETEKY::prihlas_na_pretek($_GET["id"], $id_pouz, $id_kat,$poznamka);
          }
        }
      }
      foreach ($_POST['checked'] as $ch) {
        if(!in_array($ch, $selected)){
           $_SESSION['error_kat'] = true;
           break;
        }
      }
    }
    $_SESSION['prihlaseni'] = implode(", ", $prihlaseni);
}

if (isset($_POST['odhlas'])){
  if(is_array($_POST['incharge'])){
    foreach($_POST['incharge'] as $val){
      PRETEKY::odhlas_z_preteku($_GET["id"], $val);
    }
  }
}

if (isset($_POST['del'])){
  if(is_array($_POST['checked'])){
    foreach($_POST['checked'] as $val){
      $po = new POUZIVATELIA();
      $po->vymaz_pouzivatela($val);
      unset($po);
    }
  }
}

$po = new POUZIVATELIA();
if (isset ($_POST['posli'])&&over($_POST['meno'])&&over($_POST['priezvisko'])){
  $rovnaky = $po->over_pouzivatela($_POST['meno'], $_POST['priezvisko']);
  if($rovnaky == ""){
    if ((isset($_POST['kategoria'])) && ($_POST['kategoria']!='-')){
      $id_novy = $po->pridaj_pouzivatela ($_POST['meno'], $_POST['priezvisko'],"", $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'],"");
      $po->nacitaj($id_novy,$_POST['meno'], $_POST['priezvisko'],"", $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'],"");
      PRETEKY::prihlas_na_pretek_noveho($_GET["id"], $id_novy,$_POST['kategoria'],$_POST['poznamka']);
      if (isset($_COOKIE['prihlaseni'])){
        setcookie("prihlaseni", $_COOKIE['prihlaseni'].','.$id_novy, time() + (86400 * 366),"/");
      }
      else{
        setcookie("prihlaseni", $id_novy, time() + (86400 * 366),"/");
      }
      $_SESSION['novy_pouz'] = $po->meno." ".$po->priezvisko;
    }
    else{
       $_SESSION['error_kat'] = true;
    }
    unset($po);
  }
  else{
    $_SESSION['rovnaky'] = $rovnaky;
  }
}

if(isset($_POST['navodik'])){
  $navodik = true;
}

if(isset($_POST['skry'])){
  $navodik = false;
}

?>
<!DOCTYPE HTML>
<html>
<?php
  $pr = new PRETEKY();
  $pr=PRETEKY::vrat_pretek($_GET["id"]);
  hlavicka($pr->NAZOV);
  unset($pr);
?>

<section id=pretekSection>
  <?php
  // vypis detailu preteku
  $pr = new PRETEKY();
  $pr=PRETEKY::vrat_pretek($_GET["id"]); ?>
  <form method="post">
    <?php if(!$navodik){ ?>
    <div id="navod"> <input name="navodik" type="submit" id="navodik" value="Návod"> </div>
    <?php }else { ?>
    <div id="skry"> <input name="skry" type="submit" id="skry" value="Skryť"> </div><br>
    <?php }
    if($navodik){?>
      <div id="navod">
        <table class="n">
          <tr>
            <td style="font-weight:bold;">Návod<BR></td>
          </tr>
          <tr>
            <td>
              V ľavom stĺpci je zoznam prihlásených, v pravom si vyberiete koho prihlasujete. <br>
              Ak si vás počítač zapamätal, tak tam máte tých, čo ste prihlasovali minule.<br>
              Ak sa nenájdete v pravom stĺpci, stlačte VIAC POUŽÍVATEĽOV, zoznam sa rozbalí.<br>
              Ak sa ani tak nenájdete (ste tu prvý raz), vyplňte položky v prázdnom riadku, zakliknite štvorček a stlačte PRIHLÁSIŤ NA PRETEKY. <br>
              Ak potrebujete poradiť, alebo sa chcete prihlásiť mailom, napíšte na adresu balogh@elf.stuba.sk
            </td>
          </tr>
        </table>
      </div>
  <?php }
  if(over($pr->POZNAMKA)){ ?>
    <br>
    <div id="upozornenie">
      <table class="u">
        <tr><td style="font-weight:bold;">Pokyny</td></tr>
        <tr><td><?php echo $pr->POZNAMKA; ?></td></tr>
      </table>
    </div>
    <?php
   }
   ?>
  <div id="prihlaseny">
    <h2>Zoznam prihlásených</h2>
    <p>
    <?php
      $pr = new PRETEKY();
      $pr=PRETEKY::vrat_pretek($_GET["id"]);
      $deadline = new DateTime($pr->DEADLINE);
      $now = new DateTime(date("Y-m-d H:i:s"));
    if((isset($_SESSION['admin']) && $_SESSION['admin'] ==1 || ($now < $deadline && $pr->AKTIV == 1)) ) {?>
      <input name="odhlas" type="submit" id="odhlas" value="Odhlásiť z pretekov" style="margin-bottom: 1em;">
      <?php
    }
    ?>
    <input name="export" type="submit" id="export" value="Export do súboru">
    <?php
    if(isset($_SESSION['admin'])&&$_SESSION['admin'] ==1 ){
      if(isset($_GET['exp']) && $_GET['exp'] == 1){
        echo "<a href='pretek.php?id=".$pretekId."&exp=0'>Skryť nastavenie exportu &#x23EB;</a>";
        ?>
        <div id="formaExportu" style="border: solid 2px #4169e1; padding-left: 20px; width: 280px;">
          <h3>Forma exportu</h3>
          <?php 
          $forma = PRETEKY::exportForm();
          echo "Aktuálna forma exportu: ".$forma;
          ?>
          <table id="table_export">
            <tr>
              <th>Stĺpec</th><th>Poradie</th>
            </tr>
            <tr>
              <td>Meno</td>
              <td><select name = "menoS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6.</option>
              </select></td>
            </tr>
            <tr>
              <td>Priezvisko</td>
              <td><select name = "priezviskoS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6.</option>
              </select></td>
            </tr>
            <tr>
              <td>Kategória</td>
              <td><select name="nazovS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6</option>
              </select></td>
            </tr>
            <tr>
              <td>Osobné číslo</td>
              <td><select name="os_i_cS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6.</option>
              </select></td>
            </tr>
            <tr>
              <td>Čip</td>
              <td><select name="cipS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6.</option>
              </select></td>
            </tr>
            <tr>
              <td>Poznámka</td>
              <td><select name="poznamkaS">
                <option value="0">-</option>
                <option value="1">1.</option>
                <option value="2">2.</option>
                <option value="3">3.</option>
                <option value="4">4.</option>
                <option value="5">5.</option>
                <option value="6">6.</option>
              </select></td>
            </tr>
          </table>
          <?php

          /*v map je poradie nazvov stlpcov, ktore chcem vytiahnut s tabulky pouzivatelia JOIN kategorie*/
          if(isset($_POST['ulozFormu'])){
            $ok = true;
            $map = array();
            $por = array();
            $posty = ['menoS','priezviskoS','nazovS','os_i_cS','cipS','poznamkaS'];
            foreach($posty as $stlpec){
              $val = $_POST[$stlpec];
              if($val != 0){
                if(in_array($val, $por)){
                  $ok= false;
                }
                else{
                  array_push($por, $val);
                  $map[$val] = substr($stlpec, 0, -1);
                }
              }
            }
            if($ok){
              if(sizeof($map) == 0){
                echo "<span style='color:red;'>Nebol vybraný žiadny stĺpec!</span>";
              }
              else{
                ksort($map);
                echo "<span>Zvolené: ";
                echo implode(',',$map);
                echo "</span>";
                PRETEKY::updateExport(implode(",",$map));
              }
            }
            else{
              echo "<span style='color:red;'>Vybrané poradové číslo sa opakuje viackrát!</span>";
            }
          }
          ?>
          
          <p><input name="ulozFormu" type="submit" id="ulozFormu" value="Ulož formu exportu"></p>
        </div><br>
        <?php
      }
      else{
        echo "<a href='pretek.php?id=".$pretekId."&exp=1'>Nastavenie exportu &#x23EC;</a>&nbsp;&nbsp; <A HREF='zoznam.txt'>export.txt</A></p>";
      }
    }?>
    <?php
    if(isset($_SESSION['prihlaseni']) && $_SESSION['prihlaseni'] !== ""){?>
      <p style="color:green;"><?php echo $_SESSION['prihlaseni']; ?> prihlásený/í</p>
      <?php
      unset($_SESSION['prihlaseni']);
      }?>
      <table id="myTable" class="tablesorter">
        <col class="col3" >
        <col class="col10" >
        <col class="col14" >
        <col class="col15" >
        <col class="col15" >
        <col class="col15" >
        <col class="col14" >
        <thead>
        <tr>
          <th></th>
          <th class="prvy">Meno</th>
          <th class="prvy" id="priezvisko_button">Priezvisko</th>
          <th class="prvy">Kategória</th>
          <th class="prvy">Osobné číslo</th>
          <th class="prvy">Čip</th>
          <th class="prvy">Poznámka</th>
        </tr>
        </thead>
        <tbody>
          <?php
          PRETEKY::odstran_duplicity();
          $pr->vypis_prihlasenych_d_chip();
          $pr->vypis_prihlasenych_u_chip();
          ?>
        </tbody>
      </table>

      <br><br><br>
    </div>
    <div id="odhlaseny">
      <h2>Neprihlásení používatelia</h2>
    <?php
      if((isset($_SESSION['admin'])&&$_SESSION['admin']==1)||(isset($pr->AKTIV)&&$pr->AKTIV==1&&isset($pr->DEADLINE))&&$deadline>$now){
        echo''; ?>
        <p><input name="prihlas" type="submit" id="prihlas" value="Prihlásiť na preteky" style="margin-bottom: 1em">
        <?php
        if (!isset($_GET['cookies'])){?>
          <?php $link="'pretek.php?id=".$_GET['id']."&cookies=0'"; ?>
          <input onclick="window.location.href =<?php echo $link;?>" type='button' value='Viac používateľov'>
          <?php
        }
        else{
          $link="'pretek.php?id=".$_GET['id']."'"; ?>
          <input onclick="window.location.href =<?php echo $link;?>" type='button' value='Menej používateľov'>
          <?php
        }
      }
      if (isset($_SESSION['admin'])&&$_SESSION['admin']){
        ?>
        <input name="del" type="submit" id="del" onclick="return confirm('Naozaj chcete vymazať používateľov?');" value="Vymazať používateľa"></p> <!-- aj v admine kde su vsetci pouzivatelia-->
        <?php
      }
      if(isset($_SESSION['error_kat'])){ ?>
        <p style="color:red">Treba zvoliť kategóriu!</p>
        <?php
        unset($_SESSION['error_kat']);
      }
      else if (isset($_SESSION['rovnaky'])){ ?>
        <p style="color:red">Používateľ <?php echo $_SESSION['rovnaky'];?> je už zaregistrovaný!</p>
        <?php
        unset($_SESSION['rovnaky']);
      }
      else if(isset($_SESSION['novy_pouz'])) {?>
        <p style="color:green">Používateľ <?php echo $_SESSION['novy_pouz'];?> bol zaregistrovaný a prihlásený.</p>
        <?php
        unset($_SESSION['novy_pouz']);
      }

    if((isset($_SESSION['admin']) && $_SESSION['admin']==1) || $deadline > $now){ ?>
      <table  id="myTable2" class="tablesorter">
        <col class="col10" >
        <col class="col15" >
        <col class="col14" >
        <col class="col15" >
        <col class="col15" >
        <col class="col15" >
        <col class="col9" >
        <col class="col3" >
        <thead>
        <tr>
        <th class="prvy"></th>
        <th class="prvy">Kategória</th>
        <th class="prvy">Meno</th>
        <th class="prvy">Priezvisko</th>
        <th class="prvy">Osobné číslo</th>
        <th class="prvy">Čip</th>
        <th class="prvy">Poznámka</th>
        <th class="prvy"></th>
        </tr>
        </thead>
        <tbody>
          <?php
          $pr->vypis_neprihlasenych();
          ?>
        </tbody>
      </table>
      <?php
    }
    else{
      echo "<p>Prihlasovanie na tento tréning bolo uzatvorené</p>";
    } ?>
    </div>
    <br><br>
  </form>

</section>

<script type="text/javascript" src="sorter/jquery-latest.js"></script>
<script type="text/javascript" src="sorter/jquery.tablesorter.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
     $("#myTable").tablesorter({sortList: [[2,0]]});
  });
</script>
<script type="text/javascript">
  $(document).ready(function(){
    $("#myTable2").tablesorter();
  });
</script>

<?php
 unset($pr);
 paticka();
 PRETEKY::exportujTxt($pretekId);
?>

