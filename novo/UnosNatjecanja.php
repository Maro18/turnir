<?php
session_start();

if (isset($_POST["naziv"])) {
    include '../functions.php';
    $con = getDB();
    //Prvenstvo (grupno)
    if ($_POST["tip"] == 1) {
        $naziv = $con->real_escape_string($_POST["naziv"]);
        $tip = $con->real_escape_string($_POST["tip"]);
        $brojNatjecatelja = $con->real_escape_string($_POST["broj_natjecatelja"]);
        $brojSusreta = $con->real_escape_string($_POST["broj_susreta"]);
        //$privatno=$con->real_escape_string($_POST["privatno"]);
        $sudionici = array();
        $id_sudionici = array();
        $bodoviOmjer = $con->real_escape_string($_POST["omjer_bodovi"]);

        //Ako je bodoviOmjer==1 znaci da je na bodove
        if ($bodoviOmjer == 1) {
            $bodPobjeda = $con->real_escape_string($_POST["bod_pobjeda"]);
            $bodNerijeseno = $con->real_escape_string($_POST["bod_nerijeseno"]);
            $bodPoraz = $con->real_escape_string($_POST["bod_poraz"]);

            $con->query("INSERT INTO turnir (id_turnir, admin, bod_nerijeseno, bod_pobjeda, bod_poraz, broj_susreta, broj_natjecatelja, datum_pocetka, naziv, privatno, tip, bodovi_omjer, aktivan, najbolji_od, zavrsen) 
            VALUES(null, " . $_SESSION['user'] . ", '$bodNerijeseno', '$bodPobjeda', '$bodPoraz', '$brojSusreta', '$brojNatjecatelja', NOW(), '$naziv', 2, '$tip','$bodoviOmjer',  1, null, 0)");
        } else {
            $con->query("INSERT INTO turnir (id_turnir, admin, broj_susreta, broj_natjecatelja, datum_pocetka, naziv, privatno, tip, bodovi_omjer, aktivan, najbolji_od, zavrsen)
            VALUES(null, " . $_SESSION['user'] . ", '$brojSusreta', '$brojNatjecatelja', NOW(), '$naziv', 2, '$tip', '$bodoviOmjer',  1, null, 0)");
        }
        //Dobivanje unešenog id_turnira
        $id_turnir = $con->insert_id;

        //INSERT grupe
        $con->query("INSERT INTO grupa VALUES(null, '$naziv', $id_turnir, 1)");
        //Dobivanje unešenog id_grupa
        $id_grupa = $con->insert_id;

        //Unos sudionika
        for ($i = 0; $i < count($_POST["sudionici"]); $i++) {
            $sudionici[$i] = $con->real_escape_string($_POST["sudionici"][$i]);
            $con->query("INSERT INTO sudionik VALUES(null, '$sudionici[$i]', 0, $id_grupa, 1, $id_turnir)");
            //Spremanje id-ja sudionika
            $id_sudionici[$i] = $con->insert_id;
        }

        //Unos mečeva
        $matchevi = array();
        $mat = array();

        for ($i = 0; $i < $brojNatjecatelja; ++$i) {
            for ($j = 0; $j < $brojNatjecatelja; ++$j) {
                $mat[$i][$j] = false;
            }
        }

        //Matrica je true samo za npr. [1][2] ali ne i za [2][1]. Tamo gdje je true unosi se u match sa sudionicima $i i $j
        for ($i = 0; $i < $brojNatjecatelja - 1; ++$i) {
            for ($j = 0; $j < $brojNatjecatelja; ++$j) {
                if ($i == $j)
                    continue;
                elseif ($mat[$j][$i] == false) {
                    $mat[$i][$j] = true;
                    for ($k = 0; $k < $brojSusreta; ++$k) {
                        // Zamijenjuju se gost i domaćin
                        if($k%2 == 0)
                            insertPrvenstvoMec("INSERT INTO mec VALUES(null, $id_turnir, $id_sudionici[$i], $id_sudionici[$j], null, null, null, NOW(), 1, 0)");
                        else
                            insertPrvenstvoMec("INSERT INTO mec VALUES(null, $id_turnir, $id_sudionici[$j], $id_sudionici[$i], null, null, null, NOW(), 1, 0)");
                    }
                }
            }
        }
    }
    //Knockout
    elseif ($_POST["tip"] == 2) {
        $naziv = $con->real_escape_string($_POST["naziv"]);
        $tip = $con->real_escape_string($_POST["tip"]);
        $ko_broj_natjecatelja = $con->real_escape_string($_POST["broj_natjecatelja"]);
        $bestOf = $con->real_escape_string($_POST["best_of"]);
        for ($i = 0; $i < count($_POST["sudionici"]); $i++) {
            $sudionici[$i] = $con->real_escape_string($_POST["sudionici"][$i]);
        }

        $con->query("INSERT INTO turnir (id_turnir, admin, bod_nerijeseno, bod_pobjeda, bod_poraz, broj_susreta, broj_natjecatelja, datum_pocetka, naziv, privatno, tip, bodovi_omjer, aktivan, najbolji_od, zavrsen) 
                VALUES(null, " . $_SESSION['user'] . ", null, null, null, null, $ko_broj_natjecatelja, NOW(), '$naziv', 2, '$tip', null, 1, $bestOf, 0)");

        //Dobivanje unešenog id_turnira
        $id_turnir = $con->insert_id;

        //Unos sudionika
        for ($i = 0; $i < count($_POST["sudionici"]); $i++) {
            $sudionici[$i] = $con->real_escape_string($_POST["sudionici"][$i]);
            $con->query("INSERT INTO sudionik VALUES(null, '$sudionici[$i]', 0, null , 1, $id_turnir)");
        }

        // Generiranje protivnika

        // Dobivanje sifri sudionika iz baze
        $result = getSudionici($id_turnir);
        $poljeSifSud = array();
        while ($sifSudionik = $result->fetch_assoc()) {
            array_push($poljeSifSud, $sifSudionik["id_sudionik"]);
        }

        // Generiranje random sifre sudinika i sprema u $randomSud
        $randomSud = array();

        while (count($randomSud) < count($poljeSifSud)) {
            $brojElemenata = count($poljeSifSud);
            $randBr = rand(0, $brojElemenata - 1);
            if (!in_array($poljeSifSud[$randBr], $randomSud))
                array_push($randomSud, $poljeSifSud[$randBr]);
        }

        //Unosenje u match tablicu, radi, potrebno pregled, provjera rezultata, upis suma pobjeda, iduca faza
        for ($i = 0; $i < count($randomSud); ++$i) {
            if ($i % 2 != 0)
                continue;
            $j = $i + 1;
            $con->query("INSERT INTO knockout VALUES(null, 0, 0, 1, $randomSud[$i], $randomSud[$j],0, $id_turnir)");
            $ko_id = $con->insert_id;
            $con->query("INSERT INTO mec VALUES(null, $id_turnir, $randomSud[$i], $randomSud[$j], null, null, $ko_id, NOW(), 1, 0)");

            ++$i;
        }

    }
    //Grupno-knockout
    else {
        $naziv = $con->real_escape_string($_POST["naziv"]);
        $tip = $con->real_escape_string($_POST["tip"]);
        $brojNatjecatelja = $con->real_escape_string($_POST["broj_natjecatelja"]);
        $brojSusreta = $con->real_escape_string($_POST["broj_susreta"]);
        //$privatno=$con->real_escape_string($_POST["privatno"]);
        $bestOf = $con->real_escape_string($_POST["best_of"]);
        $sudionici = array();
        $bodoviOmjer = $con->real_escape_string($_POST["omjer_bodovi"]);
        $brojGrupa = $con->real_escape_string($_POST["broj_grupa"]);

        //Ako je bodoviOmjer 1 znaci da je na bodove
        if ($bodoviOmjer == 1) {
            $bodPobjeda = $con->real_escape_string($_POST["bod_pobjeda"]);
            $bodNerijeseno = $con->real_escape_string($_POST["bod_nerijeseno"]);
            $bodPoraz = $con->real_escape_string($_POST["bod_poraz"]);

            $con->query("INSERT INTO turnir (id_turnir, admin, bod_nerijeseno, bod_pobjeda, bod_poraz, broj_susreta, broj_natjecatelja, datum_pocetka, naziv, privatno, tip, bodovi_omjer, aktivan, najbolji_od, zavrseb) 
            VALUES(null, " . $_SESSION['user'] . ", '$bodNerijeseno', '$bodPobjeda', '$bodPoraz', '$brojSusreta', '$brojNatjecatelja', NOW(), '$naziv', 2, '$tip','$bodoviOmjer',  1, $bestOf, 0)");
        } else {
            $con->query("INSERT INTO turnir (id_turnir, admin, broj_susreta, broj_natjecatelja, datum_pocetka, naziv, privatno, tip, bodovi_omjer, aktivan, najbolji_od, zavrsen) 
            VALUES(null, " . $_SESSION['user'] . ", '$brojSusreta', '$brojNatjecatelja', NOW(), '$naziv', 2, '$tip', '$bodoviOmjer',  1, $bestOf, 0)");
        }
        //Dobivanje unešenog id_turnira
        $id_turnir = $con->insert_id;

        $grupaBegin = 0;

        //INSERT grupe
        $oznaka = 'A';
        for ($i = 0; $i < $brojGrupa; $i++) {
            $con->query("INSERT INTO grupa VALUES(null, '$oznaka', '$id_turnir', 1)");
            if ($i == 0) {
                //Dobivanje prvog unešenog id_grupa
                $grupaBegin = $con->insert_id;
            }
            $oznaka++;
        }
        //Do kojeg id_grupe randomizira
        $grupaEnd = (int)$grupaBegin + (int)$brojGrupa;

        $poljeGrupa = array();

        //Unos sudionika
        for ($i = 0; $i < count($_POST["sudionici"]); $i++) {
            $grupa = rand($grupaBegin, $grupaEnd - 1);

            if (array_key_exists($grupa, $poljeGrupa)) {
                if ($poljeGrupa[$grupa] <= (int)(count($sudionici) / $brojGrupa)) {
                    $poljeGrupa[$grupa]++;
                } elseif (($poljeGrupa[$grupa] == (int)(count($sudionici) / $brojGrupa)) && ((count($sudionici) % $brojGrupa) != 0)) {
                    $poljeGrupa[$grupa]++;
                } else {
                    --$i;
                    continue;
                }
            } else {
                $poljeGrupa[$grupa] = 1;
            }
            $sudionici[$i] = $con->real_escape_string($_POST["sudionici"][$i]);
            $con->query("INSERT INTO sudionik VALUES(null, '$sudionici[$i]', 0, $grupa, 1, $id_turnir)");
        }
    }

    echo $id_turnir;
}
?>