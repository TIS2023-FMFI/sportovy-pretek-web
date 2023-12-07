function save2(event, item, idcko) {
    const i = $(item).attr("id");
    const s = $(item).attr("name");
    const element = document.getElementById(i);
    let o = element.innerText || element.textContent;
    o = o.trim();
    $.ajax({
        url: "kmenovi_clenovia.php",
        type: "post",
        cache: "false",
        data: {
            obsah: o,
            stlpec: s,
            id: idcko
        },
        success: function (data) {

        },
        error: function () {

        }
    });

}

function posli() {
    const ajaxurl = 'prihlasenie.php';
    $.ajax({
        url: ajaxurl,
        type: "post",
        cache: "false",
        data: {ajax_heslo: true},
        success: function (data) {
            if (data.search('Warning') === -1) { //ak nenastala chyba pri posielani
                alert("Heslo bolo poslané na mail administrátora!")
            } else {
                alert("Heslo sa nepodarilo poslat!");
            }
        },
        error: function () {
            alert("Heslo sa nepodarilo poslat!");
        }
    });
}

addEventListener("DOMContentLoaded", (event) => {
    let obrazok = document.getElementById('obrazok');
    obrazok.onchange = () => {
        if (obrazok.files[0].size >= 2_000_000) {
            alert("Vybraný obrázok je príliš veľký!");
            obrazok.value = null;
        }
    };
});

