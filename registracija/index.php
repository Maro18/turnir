<?php
if (isset($_SESSION["user"]))
    header("Location: /index.php");
?>
<html lang="hr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <link rel="shortcut icon" href="">
    <title>Registracija</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/css/register.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
<div class="background"></div>
<div class="container">
    <div class="row center">
        <form>
            <div class="input-field col s6 m6 offset-m3">
                <input id="ime" type="text" class="validate" required>
                <label for="ime">Korisničko ime</label>
            </div>
            <div class="input-field col s6 m6 offset-m3">
                <input id="lozinka1" type="password" class="validate" required>
                <label for="lozinka1">Lozinka</label>
            </div>
            <div class="input-field col s6 m6 offset-m3">
                <input id="lozinka2" type="password" class="validate" required>
                <label for="lozinka2">Potvrdi lozinku</label>
            </div>
            <div class="input-field col s6 m6 offset-m3">
                <input id="email" type="email" class="validate" required>
                <label for="email">Email</label>
            </div>
            <div class="input-field col s6 m6 offset-m5">
                <button type="button" class="btn btn-block" id="reg">Registracija</button>
            </div>

            <div id="errori"></div>
        </form>
    </div>
</div>

<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="/js/materialize.js"></script>
<script>
    $("#reg").on("click", function() {
        $.ajax("registrationHandler.php?user="+$("#ime").val()+"&email="+$("#email").val()+"&pass1="+$("#lozinka1").val()+"&pass2="+$("#lozinka2").val()).done( function(data) {
            $("#errori").empty();
            $("#errori").append(data);
            if(data==1) {
                window.location.href="/prijava";
            }
        })
    });
</script>

</body>
</html>
