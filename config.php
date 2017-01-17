<?php
//Spajanje na bazu i postavljanje UTF-8
function getDB() {
    $localhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "turnir_projekt";

    $conn = mysqli_connect($localhost, $dbuser, $dbpass, $dbname) or die("Couldn't connect to a server, " . mysqli_error($conn));
    mysqli_set_charset($conn, "utf8");
    return $conn;
}
?>