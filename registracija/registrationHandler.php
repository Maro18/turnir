<?php
include "../functions.php";
if(isset($_GET["user"]) && isset($_GET["email"]) && isset($_GET["pass1"]) && isset($_GET["pass2"])) {
    $con=getDB();
    $user=$con->real_escape_string($_GET["user"]);
    $email=$con->real_escape_string($_GET["email"]);
    $pass1=$con->real_escape_string($_GET["pass1"]);
    $pass2=$con->real_escape_string($_GET["pass2"]);

    $ok = 1;

    $userNames=getUsers("AND ime='$user'")->fetch_assoc();
    if($userNames["counter"]>0) {
        echo "<p>Postoji korisnik s tim imenom</p>";
        $ok=0;
    }

    $userEmails=getUsers("AND email='$email'")->fetch_assoc();
    if($userEmails["counter"]>0) {
        echo "<p>Postoji korisnik s tim emailom</p>";
        $ok=0;
    }

    if($pass1!=$pass2) {
        echo "<p>Lozinke nisu iste</p>";
        $ok=0;
    }

    if($ok==1) {
        $con->query("INSERT INTO korisnik VALUES(null, '$email', '$pass1', '$user', CURDATE(), 1)");
        echo 1;
    }


}