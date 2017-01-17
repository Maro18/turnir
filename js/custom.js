var tip = -1;
var brMomcadi = 0;
var pob = 0;
var ner = 0;
var por = 0;

$(document).ready(function () {

    $(".button-collapse").sideNav();
    $('select').material_select();

    $("#bodovi, #omjer").on("change", function () {
        if ($('#bodovi').is(':checked')) {
            $(".bodovi-field").css("display", "inline-block");
        }
        else {
            $(".bodovi-field").css("display", "none");
        }
    });


    $("#grupno, #knockout, #grupno-knockout").on("change", function () {

        if ($('#grupno').is(':checked')) {
            $("#rangDiv").css("display", "inline-block");
            $("#susretiDiv").css("display", "inline-block");
            $("#momcadiDiv").css("display", "inline-block");
            $("#bestDiv").css("display", "none");
            $("#btnDiv").css("display", "inline-block");
            $("#vidDiv").css("display", "inline-block");
            $("#brojgrupaDiv").css("display", "none");
            $("#koMomcadiDiv").css("display", "none");
            tip = 1;
        }
        else if ($('#knockout').is(':checked')) {
            $("#rangDiv").css("display", "none");
            $("#susretiDiv").css("display", "none");
            $("#bestDiv").css("display", "inline-block");
            $("#bodoviDiv").css("display", "none");
            $("#momcadiDiv").css("display", "none");
            $("#koMomcadiDiv").css("display", "inline-block");
            $("#btnDiv").css("display", "inline-block");
            $("#vidDiv").css("display", "inline-block");
            $("#brojgrupaDiv").css("display", "none");
            tip = 2;
        }
        else if ($('#grupno-knockout').is(':checked')) {
            $("#rangDiv").css("display", "inline-block");
            $("#susretiDiv").css("display", "inline-block");
            $("#bestDiv").css("display", "inline-block");
            $("#momcadiDiv").css("display", "inline-block");
            $("#btnDiv").css("display", "inline-block");
            $("#vidDiv").css("display", "inline-block");
            $("#brojgrupaDiv").css("display", "inline-block");
            $("#koMomcadiDiv").css("display", "none");
            tip = 3;
        }
    });
});


function provjera() {

    var ok = 1;

    if ($('#bodovi').is(':checked')) {

        pob = $('#pobjeda').val();
        ner = $('#nerijeseno').val();
        por = $('#poraz').val();

        if (pob > ner && ner > por) {
            document.getElementById('errori').innerHTML = '';
            ok = 1;
        }
        else {
            document.getElementById('errori').innerHTML = '<p> Pobjeda mora biti veća vrijednost od neriješenog i ' +
                'neriješeno mora biti veća vrijednost od poraza. </p>';
            ok = 0;
        }

    }
    else if (!$('#naziv').val()) {
        document.getElementById('errori').innerHTML = '<p> Morate unijeti ime natjecanja. </p>';
        ok = 0;
    }

    else if ($('#grupno-knockout').is(':checked')) {

        if (!$('#broj_natjecatelja').val() || $('#broj_natjecatelja').val() < 2 || $('#broj_natjecatelja').val() > 64) {
            document.getElementById('errori').innerHTML = '<p> Morate unijeti broj natjecatelja između 2 i 64. </p>';
            ok = 0;
        }
        else if (!$('#broj_susreta').val() || $('#broj_susreta').val() < 1 || $('#broj_susreta').val() > 4) {
            document.getElementById('errori').innerHTML = '<p> Morate unijeti broj međusobnih susreta između 1 i 4. </p>';
            ok = 0;
        }
        else if (!$('#broj_grupa').val() || $('#broj_grupa').val() < 1 || $('#broj_grupa').val() > 32 || $('#broj_grupa').val() > $('#broj_natjecatelja').val()) {
            document.getElementById('errori').innerHTML = '<p> Morate unijeti broj grupa između 2 i 32 i broj grupa mora biti manji od broja natjecatelja. </p>';
            ok = 0;
        }
    }
    else if ($('#grupno').is(':checked')) {
        if (!$('#broj_natjecatelja').val() || $('#broj_natjecatelja').val() < 2 || $('#broj_natjecatelja').val() > 64) {
            document.getElementById('errori').innerHTML = '<p> Morate unijeti broj natjecatelja između 2 i 64. </p>';
            ok = 0;
        }
        else if (!$('#broj_susreta').val() || $('#broj_susreta').val() < 1 || $('#broj_susreta').val() > 4) {
            document.getElementById('errori').innerHTML = '<p> Morate unijeti broj međusobnih susreta između 1 i 4. </p>';
            ok = 0;
        }
    }

    if (ok == 1) {
        $("#prvi").css("display", "none");
        if (tip != 2)
            brMomcadi = $('#broj_natjecatelja').val();
        else
            brMomcadi = $('#ko_broj_natjecatelja').val();

        document.getElementById('unosSudionika').innerHTML = '';

        for (var i = 0; i < brMomcadi; ++i) {
            document.getElementById('unosSudionika').innerHTML += '<div class="col s12 m6 l3 input-field">' +
                '<input type="text" maxlength="30" id="sudionik' + i + '"> </input>' + '<label for="sudionik' + i + '">' + parseInt(i + 1) + '. sudionik</label>' +
                '</div>';
        }

        $("#drugi").css("display", "block");
    }
}
function Unos() { //potrebno zabraniti unos sudionika s istim imenom
    var array_natjecatelji = [];
    var ok = 1;
    for (var i = 0; i < brMomcadi; ++i) {
        if (($('#sudionik' + i).val() == '') || ($.inArray($('#sudionik' + i).val(), array_natjecatelji) > -1)) {
            ok = 0;
            break;
        }
        else {
            array_natjecatelji.push($('#sudionik' + i).val());
        }
    }
    if (ok == 0) {
        document.getElementById('errori2').innerHTML = '<p> Morate unijeti sve sudionike s jedinstvenim imenima. </p>';
    }
    else {
        //Grupno
        if ($('#grupno').is(':checked')) {
            if ($("#omjer").is(":checked")) {
                $.post("unosNatjecanja.php", {
                    naziv: $('#naziv').val(),
                    tip: tip,
                    broj_natjecatelja: $('#broj_natjecatelja').val(),
                    broj_susreta: $('#broj_susreta').val(),
                    //privatno: $('#vidljivostSel').val(),
                    sudionici: array_natjecatelji,
                    omjer_bodovi: 0
                }, function (data) {
                    window.location.replace("/pregled/?id="+data);
                });
            }
            else {
                $.post("unosNatjecanja.php", {
                    naziv: $('#naziv').val(),
                    tip: tip,
                    broj_natjecatelja: $('#broj_natjecatelja').val(),
                    broj_susreta: $('#broj_susreta').val(),
                    //privatno: $('#vidljivostSel').val(),
                    bod_pobjeda: pob,
                    bod_nerijeseno: ner,
                    bod_poraz: por,
                    sudionici: array_natjecatelji,
                    omjer_bodovi: 1
                }, function (data) {
                    window.location.replace("/pregled/?id="+data);
                });
            }

        }
        //Knockout
        else if ($('#knockout').is(':checked')) {
            $.post("unosNatjecanja.php", {
                naziv: $('#naziv').val(),
                tip: tip,
                broj_natjecatelja: $('#ko_broj_natjecatelja').val(),
                //privatno: $('#vidljivostSel').val(),
                best_of: $('#best-of').val(),
                sudionici: array_natjecatelji
            }, function (data) {
                window.location.replace("/pregled/?id="+data);
            });
        }
        //Grupno-knockout
        else if ($('#grupno-knockout').is(':checked')) {
            if ($("#omjer").is(":checked")) {
                $.post("unosNatjecanja.php", {
                    naziv: $('#naziv').val(),
                    tip: tip,
                    broj_natjecatelja: $('#broj_natjecatelja').val(),
                    broj_susreta: $('#broj_susreta').val(),
                    //privatno: $('#vidljivostSel').val(),
                    best_of: $('#best-of').val(),
                    sudionici: array_natjecatelji,
                    omjer_bodovi: 0,
                    broj_grupa: $("#broj_grupa").val()
                }, function (data) {
                    window.location.replace("/pregled/?id="+data);
                });
            }
            else {
                $.post("unosNatjecanja.php", {
                    naziv: $('#naziv').val(),
                    tip: tip,
                    broj_natjecatelja: $('#broj_natjecatelja').val(),
                    broj_susreta: $('#broj_susreta').val(),
                    //privatno: $('#vidljivostSel').val(),
                    bod_pobjeda: pob,
                    bod_nerijeseno: ner,
                    bod_poraz: por,
                    best_of: $('#best-of').val(),
                    sudionici: array_natjecatelji,
                    omjer_bodovi: 1,
                    broj_grupa: $("#broj_grupa").val()
                }, function (data) {
                    window.location.replace("/pregled/?id="+data);
                });
            }
        }
    }
}
