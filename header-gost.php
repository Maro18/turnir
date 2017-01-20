<!DOCTYPE html>
<html lang="hr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title><?= isset($title) ? $title : "Turnir" ?> Projekt</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/css/materialize.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/fonts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!--  Scripts-->
    <script src="https://code.jquery.com/jquery-2.1.1.min.js"></script>
    <script src="/js/materialize.js"></script>
    <script src="/js/custom.js"></script>
</head>

<body>
<header>
<nav>
    <div class="nav-wrapper blue darken-2">
        <a class="brand-logo"><img src="/images/logo.png" class="responsive-img" style="height: 55px;" alt="ScoreSheet"></a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
            <li><a href="/">Pregled natjecanja</a></li>
            <li><a href="/statistika">Statistika</a></li>
            <li><a href="/prijava">Prijava</a></li>
            <li><a href="/registracija">Registracija</a></li>
        </ul>
        <ul class="side-nav" id="mobile-demo">
            <li><a href="/">Pregled natjecanja</a></li>
            <li><a href="/statistika">Statistika</a></li>
            <li><a href="/odjava.php">Prijava</a></li>
            <li><a href="/novo">Registracija</a></li>
        </ul>
    </div>
</nav>
</header>
<main>