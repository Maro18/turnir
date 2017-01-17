<?php
include 'functions.php';
session_start();
$title="Pregled";
if(isset($_SESSION["user"])) {
    include 'header.php';
}
else {
    include 'header-gost.php';
}
?>
<div class="title-container center">
    <h1 class="title-text">Moji turniri</h1>
</div>
<div class="turniri row center">
    <div class="container">
        <?php
        $turniri=getTournaments();
        while($turnir=$turniri->fetch_assoc()) {
            $tip=getTypeOfTournament($turnir["tip"]);
            $date=dateToUser($turnir["datum_pocetka"]);
            ?>
            <ul class="collection">
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

                    <a class="secondary-content delete-item"><i class="material-icons">delete</i></a>
                </li>
            </ul>
        <?php
        }
        ?>
    </div>

</div>

<script>
    $(".coll-div").on("click", function() {
        var id = $(this).parent().attr("rel");
        window.location.href="pregled/?id="+id;
    });

    $(".delete-item").on("click", function () {
        var me=$(this);
        swal({
                title: "Jeste li sigurni da želite izbrisati ovaj turnir?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "lightblue",
                confirmButtonText: "Izbriši!",
                cancelButtonText: "Otkaži",
                allowEscapeKey: true,
                closeOnConfirm: true },
            function() {
                $.ajax("novo/delete.php?id=" + me.parent().attr("rel")).done(function (data) {
                    me.parent().remove();
                    Materialize.toast('Izbrisano', 2000);
                });
            });
    });
</script>

<?php
include 'footer.php';
?>

