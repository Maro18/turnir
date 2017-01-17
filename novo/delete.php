<?php
if(isset($_GET["id"])) {
    include '../functions.php';
    deleteRow("turnir", $_GET["id"]);
}