<?php
session_start();
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
$navodik = false;


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
if(isset($_POST['export'])){?>
  <meta http-equiv="refresh" content="0;URL=zoznam.txt" />
  <?php
}

if (isset($_POST['prihlas'])&&isset($_POST['incharge'])){
  echo "bolo stlacene prihlas<br>";
  if(is_array($_POST['incharge'])){
    foreach($_POST['incharge'] as $val){
      if($val!="-"){
        $pieces = explode(":", $val);
        $id_kat=$pieces[0];
        echo "id_kat:".$id_kat;
        $id_pouz=$pieces[1];
        if (isset($_COOKIE['prihlaseni'])){
          $cookies_prihlaseni=$_COOKIE['prihlaseni'].','.$id_pouz;
        }
        else{
          $cookies_prihlaseni=$id_pouz;
        }
        setcookie("prihlaseni", $cookies_prihlaseni, time() + (86400 * 366),"/");
        echo $cookies_prihlaseni."<br>";
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
}

if (isset($_POST['odhlas'])){
  if(is_array($_POST['incharge'])){
    foreach($_POST['incharge'] as $val){
      PRETEKY::odhlas_z_preteku($_GET["id"], $val);
    }
  }
}

if (isset($_POST['del'])){
  if(is_array($_POST['incharge2'])){
    foreach($_POST['incharge2'] as $val){
      $po = new POUZIVATELIA();
      $po->vymaz_pouzivatela($val);
      unset($po);
    }
  }
}

$po = new POUZIVATELIA();
if (isset ($_POST['posli'])&&over($_POST['meno'])&&over($_POST['priezvisko'])){
  $id_novy=$po->pridaj_pouzivatela ($_POST['meno'], $_POST['priezvisko'],"", $_POST['oscislo'], $_POST['cip'], $_POST['poznamka'],"");
  if ($id_novy>-1 && isset($_POST['kategoria']) && $_POST['kategoria']!='-'){
    PRETEKY::prihlas_na_pretek($_GET["id"], $id_novy,$_POST['kategoria'],$_POST['poznamka']);
    if (isset($_COOKIE['prihlaseni'])){
      setcookie("prihlaseni", $_COOKIE['prihlaseni'].','.$id_novy, time() + (86400 * 366),"/");
    }
    else{
      setcookie("prihlaseni", $id_novy, time() + (86400 * 366),"/");
    }
  }
  unset($po);
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
              Ak sa ani tak nenájdete (ste tu prvý raz), vyplňte položky v prázdnom riadku, zakliknite štvorček a stlačte PRIHLÁSIŤ NA TRÉNING. <br>
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
    <?php } ?>
    <div id="prihlaseny">
      <h2>Zoznam prihlásených</h2>
      <p><input name="odhlas" type="submit" id="odhlas" value="Odhlásiť z tréningu"></p> <br>
      <table id="myTable" class="tablesorter" border="1" >
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

      <p><input name="odhlas" type="submit" id="odhlas" value="Odhlásiť z tréningu"></p> <br> 
      <p><input name="export" type="submit" id="export" value="Export do súboru"></p>
      <?php 
      if(isset($_SESSION['admin'])&&$_SESSION['admin'] ==1 ){ ?>
        <p><input name="export" type="submit" id="export" value="Export do súboru"></p>
        <?php 
      } ?> 

      <br> <br> <br>
    </div>  
    <div id="odhlaseny">
      <h2>Neprihlásení používatelia</h2>
    <?php

    $d1 = new DateTime($pr->DEADLINE);
    $d2 = new DateTime(date("Y-m-d H:i:s"));
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
      if((isset($_SESSION['admin'])&&$_SESSION['admin']==1)||(isset($pr->AKTIV)&&$pr->AKTIV==1&&isset($pr->DEADLINE))&&$d1>$d2){ 
        echo''; ?>
        <input name="prihlas" type="submit" id="prihlas" value="Prihlásiť na tréning">
        <?php 
      } 
      if (isset($_SESSION['admin'])&&$_SESSION['admin']){
        ?>
        <input name="del" type="submit" id="del" onclick="return confirm('Naozaj chcete vymazať používateľov?');" value="Vymazať používateľa"> <!-- aj v admine kde su vsetci pouzivatelia-->
        <?php
      }
    if((isset($_SESSION['admin'])&&$_SESSION['admin']==1) || $d1 > $d2){ ?>
      <br><br><br>
      <table  id="myTable2" class="tablesorter" border="1"> 
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
?>

