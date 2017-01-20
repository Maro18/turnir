<!DOCTYPE html>
<html lang="hr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0"/>
    <title><?=isset($title) ? $title : "Turnir"?> Projekt</title>

    <!-- CSS  -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="/css/materialize.min.css" type="text/css" rel="stylesheet" media="screen,projection"/>
    <link href="/fonts/font-awesome-4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- Scripts -->
    <script src="/js/jquery-3.1.1.min.js"></script>
    <script src="/js/materialize.js"></script>
    <script src="/js/custom.js"></script>

    <?php if(!isset($noSwal)) { ?>
    <!-- Sweet Alert -->
    <link href="/css/sweetalert.css" rel="stylesheet">
    <script src="/js/sweetalert.min.js"></script>
    <?php } ?>

	<?php if(!isset($noDataTable)) { ?>
    <!-- DataTable -->
	<link href="/css/jquery.dataTables.css" rel="stylesheet">
    <script src="/js/jquery.dataTables.js"></script>
    <?php } ?>

    <!-- Custom CSS -->
    <link href="/css/style.css" type="text/css" rel="stylesheet" media="screen,projection"/>
</head>

<body>
<header>
<nav>
    <div class="nav-wrapper blue darken-2">
        <a class="brand-logo" href="/"><img src="/images/logo.png" class="responsive-img" style="height: 55px;" alt="ScoreSheet"></a>
        <a href="#" data-activates="mobile-demo" class="button-collapse"><i class="material-icons">menu</i></a>
        <ul class="right hide-on-med-and-down">
            <li><a href="/">Pregled natjecanja</a></li>
            <li><a href="/novo">Novo natjecanje</a></li>
            <li><a href="/statistika">Statistika</a></li>
            <li><a href="/odjava.php">Odjava</a></li>
        </ul>
        <ul class="side-nav" id="mobile-demo">
            <li><a href="/">Pregled natjecanja</a></li>
            <li><a href="/novo">Novo natjecanje</a></li>
            <li><a href="/statistika">Statistika</a></li>
            <li><a href="/odjava.php">Odjava</a></li>
        </ul>
    </div>
</nav>
</header>
<main>
