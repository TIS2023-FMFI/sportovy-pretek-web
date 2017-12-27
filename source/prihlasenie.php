<?php
include('funkcie.php');
include('pouzivatelia.php');
include('preteky.php');
session_start();
?>

<!DOCTYPE HTML>
<html>
<?php
hlavicka("Prihlásenie administrátora");

if (isset($_POST['ajax_heslo'])) {
   posli_heslo($_POST['ajax_heslo'], $_POST['od'], $_POST['komu']);
}

if (isset($_POST['heslo'])&&$_POST['heslo']==$heslo){
  $_SESSION['admin']=1;
  echo "<META http-equiv='refresh' content='0;URL=index.php'>";
}
else{
?>
  <form method="post">
    <div id="prihlasenieAdministratora">
      <h2>Prihlásenie administrátora</h2>
      <table style="width:100%;">
        <tr>
          <td><label for="heslo">Heslo:</label></td>
          <td><input type=password name="heslo" id="heslo"></td>
        </tr>
        <?php
        if(isset($_POST["heslo"])){?>
          <tr>
            <td></td>
            <td class="upozornenie">Ľutujeme, zadali ste zlé heslo</td>
          </tr>
        <?php } ?>
        <tr>
          <!--bude to treba lepsie vymysliet..taktov noci mi napada md5, ale mozno by bolo lepsie si to neposielat ako parameter-->
          <td><div id="poslatHeslo" onclick="posli('<?php echo $heslo; ?>', '<?php echo $mail_od; ?>', '<?php echo $mail_komu; ?>')">Zabudol som heslo</div></td>
          <td><input type=submit name="prihlas" id="prihlas" value="Prihlásiť"></td>
        </tr>
      </table>
    </div>
</form>
<?php
}
paticka();
?>

</html>

<script>
/*
  $('#poslatHeslo').click(function(){
    console.log('klik');

        var ajaxurl = 'ajax.php',
        data =  {action: 'ahoj'};

        $.ajax({
          url: ajaxurl,
          type: "post",
          cache: "false",
          data: {action: 'ahoj'},
          success: function(data) {
               alert("Heslo bolo poslané administrátorovi!");
          },
          error: function(){
              alert("Heslo sa nepodarilo poslat!");
          }
        });

    });*/
</script>
