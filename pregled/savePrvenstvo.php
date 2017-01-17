<?php
if(isset($_GET["id"]) and isset($_GET["domacin"]) and isset($_GET["gost"])) {
    include '../functions.php';
    saveMatchResults($_GET["id"], $_GET["domacin"], $_GET["gost"]);

    $result = getBodOmjerIzMeca($_GET["id"]);
    $bod_omjer = $result["bodovi_omjer"];

    if($bod_omjer == 1)
    	updatePoints($_GET["id"], $_GET["domacin"], $_GET["gost"]);

    echo 1;
}