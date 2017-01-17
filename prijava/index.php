<?php
if (isset($_SESSION["user"]))
    header("Location: /index.php");
?>
<html lang="hr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <link rel="shortcut icon" href="">
    <title>Prijava</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/css/login.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>
<body>
<div class="background"></div>
<div class="container">

    <div class="row center">
        <div id="box1" class="box blurred-bg tinted">
            <div class="content">
                <form class="col s12 m8 offset-m2" method="POST" action="authentification.php">
                    <div class="input-field col s12">
                        <input name="email" id="email" type="email" class="validate" required >
                        <label style="color: white;" data-error="Email nije ispravan" for="email">Email</label>
                    </div>
                    <div class="input-field col s12">
                        <input name="password" id="password" type="password" class="validate" required autocomplete="off" >
                        <label style="color: white;" for="password">Lozinka</label>
                    </div>
                    <div class="input-field col s12 m6 offset-m3">
                        <button  class="btn waves-effect waves-light" type="submit">Prijava</button>
                    </div>
                    <br>
                </form>
                <?php if(isset($_GET["warning"]) AND $_GET["warning"]=="true") {?>
                    <p class="col s12 m8 offset-m2" style="color: white;">Krivo unesen email ili lozinka.</p>
                <?php } ?>
            </div>
        </div>
    </div>

</div>

<!--  Scripts-->
<script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
<script src="/js/materialize.js"></script>
<script>
    $(document).ready(function() {

    });
</script>
</body>