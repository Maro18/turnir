<?php
if(isset($_GET["id"])) {
    include '../functions.php';
    sessionValidation();
    $title = "Pregled turnira";
    include '../header.php';

    $con = getDB();
    $id_turnir = $con->real_escape_string($_GET["id"]);
    $rez = getTipTurnira($id_turnir);
    $tip = $rez["tip"];
    $bestOf = $rez["najbolji_od"];
    $matches = getMatches($id_turnir);
    $numberOfMatches = (int)$matches->num_rows;
    $i = 0;
    $brojSusreta = 1;
    $matchArr = array();

    ?>
    <div id="tournament" class="row" rel="<?=$id_turnir?>">
    <?php
    //Prvenstvo
    if($tip == 1) {
        while ($match = $matches->fetch_assoc()) {
            $matchArr[$i] = $match;
            //Broj susreta
            if ($i == 0) {
                $brojSusreta = $match["broj_susreta"];
            }
            $i++;
        }
        //Prva petlja ide po redu od 0 do brojSusreta
        for ($i = 0; $i < $numberOfMatches; $i++) {
            if ($i < $brojSusreta) {
                $domacin = getParticipant($matchArr[$i]["id_domacin"]);
                $gost = getParticipant($matchArr[$i]["id_gost"]);
                $match_id = $matchArr[$i]["id_mec"];
                $domacinRez = $matchArr[$i]["rez_domacin"];
                $gostRez = $matchArr[$i]["rez_gost"];
                $zavrsen = $matchArr[$i]["zavrsenMec"];
                ?>
                <div class="valign-wrapper col m6 offset-m3" rel="<?= $match_id ?>">
                    <div class="col s6 m3 offset-m2 valign left-align">
                        <span><?= $domacin["naziv"] ?></span> : <span><?= $gost["naziv"] ?></span>
                    </div>
                    <div class="col s2 valign">
                        <input type="number" max="999" min="0" name="domacin_bodovi"
                               value="<?= $domacinRez != null ? $domacinRez : "" ?>" class="rezultat-input"
                        <?php if($zavrsen==1) echo 'readonly';?>>
                    </div>
                    <span class="col s1 valign">:</span>
                    <div class="col s2 valign">
                        <input type="number" max="999" min="0" name="gost_bodovi"
                               value="<?= $gostRez != null ? $gostRez : "" ?>" class="rezultat-input"
                            <?php if($zavrsen==1) echo 'readonly';?>>
                    </div>
                    <?php if($zavrsen!=1){?>
                    <div class="col s12 m2"><button id="spremi-btn" class="btn btn-block spremi-btn">Spremi</button></div>
                    <?php } ?>
                </div>
                <?php
                //Druga petlja ide od brojaSusreta+1 do broja mečeva
                for ($j = $brojSusreta + $i; $j < $numberOfMatches; $j += $brojSusreta) {
                    $domacin = getParticipant($matchArr[$j]["id_domacin"]);
                    $gost = getParticipant($matchArr[$j]["id_gost"]);
                    $match_id = $matchArr[$j]["id_mec"];
                    $domacinRez = $matchArr[$j]["rez_domacin"];
                    $gostRez = $matchArr[$j]["rez_gost"];
                    $zavrsen = $matchArr[$j]["zavrsenMec"];
                    ?>
                    <div class="valign-wrapper col m6 offset-m3" rel="<?= $match_id ?>">
                        <div class="col s6 m3 offset-m2 valign left-align">
                            <span><?= $domacin["naziv"] ?></span> : <span><?= $gost["naziv"] ?></span>
                        </div>
                        <div class="col s2 valign">
                            <input  type="number" max="999" min="0" name="domacin_bodovi"
                                   value="<?= $domacinRez != null ? $domacinRez : "" ?>" class="rezultat-input"
                                <?php if($zavrsen==1) echo 'readonly';?>>
                        </div>
                        <span class="col s1 valign">:</span>
                        <div class="col s2 valign">
                            <input type="number" max="999" min="0" name="gost_bodovi"
                                   value="<?= $gostRez != null ? $gostRez : "" ?>" class="rezultat-input"
                                <?php if($zavrsen==1) echo 'readonly';?>>
                        </div>
                        <?php if($zavrsen!=1){?>
                            <div class="col s12 m2"><button id="spremi-btn" class="spremi-btn btn btn-block">Spremi</button></div>
                        <?php } ?>
                    </div>
                    <?php
                }
            }
        }
        ?>
        <script>
            $(document).ready(function () {


                //Spremi tipka
                $(".spremi-btn").on("click", function() {
                    var me = $(this);
                    var container = me.parent().parent();
                    var id_match = container.attr("rel");
                    var domacinRez = container.find("input[name=domacin_bodovi]").val();
                    var gostRez = container.find("input[name=gost_bodovi]").val();
                    if(domacinRez=="" || !domacinRez || gostRez=="" || !gostRez) {
                         swal({
                            title: "Pogrešan unos",
                            text: "Rezultat mora biti popunjen",
                            type: "error",
                            timer: 1600,
                            showConfirmButton: false
                         });
                    }
                    else if (domacinRez<0 ||  gostRez<0) {
                        swal({
                            title: "Pogrešan unos",
                            text: "Rezultat mora biti pozitivan",
                            type: "error",
                            timer: 1600,
                            showConfirmButton: false
                        });
                    }
                    else {
                        $.ajax("savePrvenstvo.php?id="+id_match+"&domacin="+domacinRez+"&gost="+gostRez).done(function(data) {
                            if(data == 1) {
                                swal({
                                    title: "Spremljeno!",
                                    type: "success",
                                    timer: 1600,
                                    showConfirmButton: false
                                });
                                me.remove();
                                container.find("input").attr("readonly", true);
                            }
                        });
                    }
                }); // kraj spremi tipke

            });
        </script>
        <?php
    }
    //Knockout
    else if($tip == 2) {
        //Dohvat broja knockouta
        $numberKnockouts = $con->query("SELECT COUNT(k.id_knockout) AS countKO, k.id_knockout AS id_knockout, t.najbolji_od 
            FROM knockout k JOIN turnir t ON t.id_turnir=k.id_turnir 
            WHERE k.id_turnir=$id_turnir")->fetch_assoc();
        $numberOfKO = $numberKnockouts["countKO"];

        //Dohvat svih knockouta
        $knockouts=getKnockouts($id_turnir);
        ?>
        <?php
        while($knockout=$knockouts->fetch_assoc()) {
            $participants = getParticipantsFromKO($knockout["id_knockout"]);
            ?>
            <div class="col s12 m4 l3 knockout" rel="<?=$knockout["id_knockout"]?>" best-of="<?=$bestOf?>" participant1="<?=$participants["rezDomacin"]?>" participant2="<?=$participants["rezGost"]?>">
                <div class="card">
                    <div class="card-content center">
                        <span class="card-title"><?=$participants["sudionik1"]?> vs <?=$participants["sudionik2"]?$participants["sudionik2"]:"Ceka se"?></span>
                        <?php
                        $matchesInKo=getMatchesFromKo($knockout["id_knockout"]);
                         $i = 0;
                         $j = 0;
                        while($matchInKo = $matchesInKo->fetch_assoc()) {
                            if($bestOf==2) {
                                if($i==0)
                                    echo '<div class="domaci center"><span>domacin</span> : <span>gost</span></div>';
                                else
                                    echo '<div class="domaci center"><span>gost</span> : <span>domacin</span></div>';
                            } ?>
                            <p class="match-result" match="<?=$matchInKo["id_mec"]?>"
                                <?php if($matchInKo["zavrsen"]==1)
                                    echo "rel='complete'>";
                                else
                                    echo "rel='not-complete' sud2=".$participants["id_sud2"]." sud1=".$participants["id_sud1"].">";

                                    if($bestOf==2 && $i==1) { ?>
                                     <input type="number" max="999" min="0" name="gost_rez" <?=$matchInKo["zavrsen"]==1?"readonly":""?> value="<?=$matchInKo["rez_gost"]?>" class="rezultat-input">
                                     :
                                     <input type="number" max="999" min="0" name="domacin_rez" <?=$matchInKo["zavrsen"]==1?"readonly":""?> value="<?=$matchInKo["rez_domacin"]?>" class="rezultat-input">

                                    <?php } else { ?>
                                      <input type="number" max="999" min="0" name="domacin_rez" <?=$matchInKo["zavrsen"]==1?"readonly":""?> value="<?=$matchInKo["rez_domacin"]?>" class="rezultat-input">
                                       :
                                      <input type="number" max="999" min="0" name="gost_rez" <?=$matchInKo["zavrsen"]==1?"readonly":""?> value="<?=$matchInKo["rez_gost"]?>" class="rezultat-input">
                                    <?php } ?>
                            </p>
                        <?php $i++; } ?>
                    </div>
                    <?php if($knockout["zavrsenKnockout"]!=1) { ?>
                    <div class="card-action center">
                        <a class="save">Spremi</a>
                    </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <script>
        $(document).ready(function() {
            var idTournament=$("#tournament").attr("rel");

            $(".save").on("click", function() {
                var me=$(this);
                var id_knockout=me.parent().parent().parent().attr("rel");
                var participant1=me.parent().parent().find("p[rel=not-complete] input[name=domacin_rez]").val();
                var participant2=me.parent().parent().find("p[rel=not-complete] input[name=gost_rez]").val();
                var bestOf=me.parent().parent().parent().attr("best-of");
                var id_match=me.parent().parent().find(".match-result[rel=not-complete]").attr("match");
                var id_sud1=me.parent().parent().find("p[rel=not-complete]").attr("sud1");
                var id_sud2=me.parent().parent().find("p[rel=not-complete]").attr("sud2");

                var completeCount = me.parent().parent().find("p[rel=complete]").length;
                var p1 = me.parent().parent().find("p[rel=complete] input[name=domacin_rez]").val();
                var p2 = me.parent().parent().find("p[rel=complete] input[name=gost_rez]").val();

                if(participant2 == participant1 && bestOf != 2) {
                    swal({
                        title: "Nije moguce unijeti izjednačen rezultat",
                        text: "U knockout tipu turnira meč mora imati pobjednika",
                        type: "error",
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
                else if(participant1=="" || participant2=="" || !participant1 || !participant2) {
                    swal({
                        title: "Pogrešan unos",
                        text: "Potrebno je unjeti sva polja.",
                        type: "error",
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
                else if(participant1<0 || participant2<0) {
                    swal({
                        title: "Pogrešan unos",
                        text: "Nije moguce unijeti negativnu vrijednost u rezultata",
                        type: "error",
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
                else if(bestOf==2 && completeCount>0 && (p1==participant1 && p2==participant2)) {
                    swal({
                        title: "Pogrešan unos",
                        text: "Nema pobjednika",
                        type: "error",
                        timer: 1400,
                        showConfirmButton: false
                    });
                }
                else {
                    $.ajax("saveKnockout.php?id="+id_knockout+"&sud1="+participant1+"&sud2="+participant2+"&bestOf="+bestOf+"&id_turnir="+idTournament+"&id_mec="+id_match+"&id_sud1="+id_sud1+"&id_sud2="+id_sud2).done(function(data) {
                        var json = JSON.parse(data);
                        if(json.mec_knockout==0) {
                            //Stavi uneseni meč na complete
                            me.parent().parent().find("p[rel=not-complete] input").attr("readonly", true);
                            me.parent().parent().find("p[rel=not-complete]").attr("rel","complete");

                            //Stvori novi meč
                            matchHTML = "";
                            <?php if($bestOf != 2) { ?>
                            matchHTML += '<p class="match-result" rel="not-complete" match="'+json.id+'"><input type="number" max="999" min="0" name="domacin_rez" class="rezultat-input"> : <input type="number" max="999" min="0" name="gost_rez" class="rezultat-input"></p>';
                            <?php } else { ?>
                            matchHTML += '<div class="domaci center"><span>gost</span> : <span>domacin</span></div>';
                            matchHTML += ' <p class="match-result" rel="not-complete" match="'+json.id+'" sud2="'+id_sud2+'" sud1="'+id_sud1+'">';
                            matchHTML += '<input type="number" max="999" min="0" name="gost_rez" class="rezultat-input">:<input type="number" max="999" min="0" name="domacin_rez" class="rezultat-input"></p>';
                            <?php } ?>
                            me.parent().prev().append(matchHTML);
                        }
                        else if(json.mec_knockout==-1) {
                            me.parent().fadeOut();
                            $.ajax("novo/delete.php?id=" + me.parent().attr("rel")).done(function (data) {

                            });
                            //Gotov je turnir --- Prikazi swal pobjednika
                            swal({
                                title: json.winner+" je pobjednik turnira!",
                                type: "success",
                                showConfirmButton: true,
                                allowEscapeKey: true,
                                closeOnConfirm: true
                            });
                        }
                        else {
                            //stvori novi KO
                            location.reload();
                        }
                    });
                }
            });

        });
        </script>
    <?php
    }
}
include '../footer.php';
?>