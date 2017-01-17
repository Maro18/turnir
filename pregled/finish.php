<?php
if(isset($_GET["id"])) {
    include '../functions.php';
    finishTournament($_GET["id"]);
    echo 1;
}