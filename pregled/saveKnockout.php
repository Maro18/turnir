<?php
if(isset($_GET["id"]) and isset($_GET["sud1"]) and isset($_GET["sud2"])) {
    include '../functions.php';
    $array=array();

    if($_GET["bestOf"]==2) {
        //gledaj ko ima vise bodova iz rez_domacin/Gost u 2 meca, ako je isto onda gledaj ko je zabio vise u gostima
        $array=saveKnockoutBestOf2($_GET["id"], $_GET["sud1"], $_GET["sud2"], $_GET["id_turnir"], $_GET["id_mec"], $_GET["id_sud1"], $_GET["id_sud2"]);
        echo json_encode($array);
    }
    //1, 3, 5, 7
    else {
        //Sejvanje pobjede sudionika
        $array=saveKnockoutMatchResults($_GET["id"], $_GET["sud1"], $_GET["sud2"], $_GET["id_turnir"], $_GET["id_mec"]);
        echo json_encode($array);
    }
}