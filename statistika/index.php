<?php
include '../functions.php';
sessionValidation();
$title = "Statistika";
include '../header.php';
?>
<div class="container">
<?php
//Prikaz statistike  turnira ili odabira turnira
if(isset($_GET["id"])) {
    include 'statistikaPrvenstvo.php';
}
else { ?>
    <div class="turniri row center">
        <div class="container">
            <h3 class="flow-text">Odaberi turnir</h3>
            <?php
            $turniri=getTournaments();
            while($turnir=$turniri->fetch_assoc()) {
                $tip=getTypeOfTournament($turnir["tip"]);
                $date=dateToUser($turnir["datum_pocetka"]);
                ?>
                <ul class="collection" id="<?=$turnir["id_turnir"]?>">
                    <li class="collection-item avatar" rel="<?=$turnir["id_turnir"]?>">
                        <div class="coll-div">
                            <i class="material-icons circle turnir-ikona">assignment_ind</i>
                            <span class="title"><?=$turnir["naziv"]?></span>
                            <p>
                                <?=$tip?>
                                <br>
                                <?=$date?>
                            </p>
                        </div>
                    </li>
                </ul>
                <?php
            }
            ?>
        </div>
    </div>
    <script>
        $(".collection").on("click", function() {
            window.location.href = '/statistika/?id='+$(this).attr("id");
        });
    </script>
<?php
}
?>
</div>
<?php
include '../footer.php';
?>