console.log("loog");
function save2(event, item, idcko){
  var i = $(item).attr("id");
  var s = $(item).attr("name");
  var o = document.getElementById(i).innerHTML;
  $.ajax({
    url: "kmenovi_clenovia.php",
    type: "post",
    cache: "false",
    data: {obsah: o,
        stlpec: s,
        id: idcko
        },
    success: function(data) {

    },
    error: function(){

    }
  });

}

function posli(){

    var ajaxurl = 'prihlasenie.php';

    $.ajax({
      url: ajaxurl,
      type: "post",
      cache: "false",
      data: {ajax_heslo: true},
      success: function(data) {
        if(data.search('Warning') == -1){ //ak nenastala chyba pri posielani
          alert("Heslo bolo poslané na mail administrátora!")
        } else{
          alert("Heslo sa nepodarilo poslat!");
        }
      },
      error: function(){
          alert("Heslo sa nepodarilo poslat!");
      }
    });

}
