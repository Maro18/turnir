<?php
session_start();
include '../functions.php';

if(isset($_POST["email"]) && isset($_POST["password"])) {
    $result = validacija($_POST["email"], $_POST["password"]);

    //Provjera jeli ima takav u bazi (ako vrati vise od 0 redaka)
    if($result->num_rows>0) {
        $row=$result->fetch_assoc();
        $_SESSION["user"]=$row["id_korisnik"];
        header("Location: /");
    }
    else {
        header("Location: /prijava?warning=true");
    }

}