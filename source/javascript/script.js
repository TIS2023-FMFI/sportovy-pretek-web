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