<?php
include 'config.php';

function sessionValidation() {
    session_start();
    if(!isset($_SESSION["user"])) {
        header("Location: /prijava");
        die();
    }
}

function getUsers($cond="") {
    $con=getDB();
    return $con->query("SELECT COUNT(*) AS counter FROM korisnik WHERE aktivan=1 $cond");
}

function validacija($email, $pass) {
    $con=getDB();
    $email=$con->real_escape_string($email);
    $pass=$con->real_escape_string($pass);
    return $con->query("SELECT * FROM korisnik WHERE aktivan=1 AND email='$email' AND lozinka='$pass';");
}
function getTournaments() {
    $con=getDB();
    return $con->query("SELECT * FROM turnir WHERE aktivan=1 AND admin=".$_SESSION["user"]);
}
function getTypeOfTournament($type) {
    switch($type) {
        case "1":
            return "Prvenstvo";
        case "2":
            return "Knockout";
        case "3":
            return "Grupno+knockout";
        default:
            return "Nepoznato";
    }
}
//DATE CONVERTION
function dateToDB($date) {
    $date = DateTime::createFromFormat('d.m.Y. H:i:s', $date);
    return $date->format('Y-m-d H:i:s');
}
function dateToUser($date) {
    $date = DateTime::createFromFormat('Y-m-d H:i:s', $date);
    return $date->format('d.m.Y H:i:s');
}

function deleteRow($table, $id) {
    $key="id_".$table;
    $con=getDB();
    $con->query("UPDATE ".$table. " SET aktivan=0 WHERE ".$key."=".$con->real_escape_string($id));
}

function getSudionici($idTurnir) {
    $con = getDB();
    $idTurnir = $con->real_escape_string($idTurnir);
    return $con->query("SELECT id_sudionik FROM sudionik WHERE id_turnir=$idTurnir");
}

function getMaxIdKo() {
    $con = getDB();
    return $con->query("SELECT MAX(id_knockout) AS max_id_ko FROM knockout");
}

function insertPrvenstvoMec($q) {
    $con = getDB();
    $con->query($q);
}

function getMatches($id) {
    $con=getDB();
    return $con->query( "SELECT m.id_mec AS id_mec, m.id_turnir AS id_turnir, m.id_domacin AS id_domacin, ".
                          "m.id_gost AS id_gost, m.rez_domacin AS rez_domacin, m.rez_gost AS rez_gost, ".
                          "m.id_knockout AS id_knockout, m.datum AS datum, m.aktivan AS aktivan, m.zavrsen AS zavrsenMec, ".
                          "t.broj_susreta AS broj_susreta, t.zavrsen AS zavrsen ".
                          "FROM mec m ".
                          "JOIN turnir t ON m.id_turnir=t.id_turnir ".
                          "WHERE t.id_turnir=".$con->real_escape_string($id).
                          " ORDER BY id_mec ASC" );
}
function getParticipant($id) {
    $con=getDB();
    return $con->query("SELECT * FROM sudionik WHERE aktivan=1 AND id_sudionik=".$con->real_escape_string($id))->fetch_assoc();
}
function getParticipantsFromTournament($id) {
    //TODO doraditi ovu funkciju
    $con=getDB();
    return $con->query("SELECT * FROM sudionik WHERE aktivan=1 AND id_turnir=".$con->real_escape_string($id));
}
function saveMatchResults($id, $home, $away) {
    $con=getDB();
    $id=$con->real_escape_string($id);
    $home=$con->real_escape_string($home);
    $away=$con->real_escape_string($away);
    $con->query("UPDATE mec SET rez_domacin=$home, rez_gost=$away, zavrsen=1 WHERE id_mec=$id");
}
function saveKnockoutMatchResults($id, $sud1, $sud2, $id_turnir, $id_mec) {
    $con=getDB();
    $id=$con->real_escape_string($id);
    $sud1=$con->real_escape_string($sud1);
    $sud2=$con->real_escape_string($sud2);
    $id_turnir=$con->real_escape_string($id_turnir);
    $id_mec=$con->real_escape_string($id_mec);
    $arr = array();

    //Update meca
    saveMatchResults($id_mec, $sud1, $sud2);

    //Inkrement pobjednika u knockoutu
    if($sud1>$sud2)
        $cond="pobjeda_sudionik1=pobjeda_sudionik1+1";
    else
        $cond="pobjeda_sudionik2=pobjeda_sudionik2+1";
    $con->query("UPDATE knockout SET $cond WHERE id_knockout=$id");

    //Select pobjeda sudionika iz knockouta i bestof broja
    $wins=$con->query("SELECT k.*, t.* FROM knockout k 
                       JOIN turnir t ON t.id_turnir = k.id_turnir 
                       WHERE k.id_knockout=$id")->fetch_assoc();

    /**
     *  RETURN
     *  MEC_KNOCKOUT => 0 AKO JE STVOREN NOVI MEC
     *  MEC_KNOCKOUT => 1 AKO JE STVOREN NOVI KNOCKOUT
     *  MEC_KNOCKOUT => 2 AKO JE KNOCKOUT VEC POSTOJAO I STVOREN JE NOVI MEC U NJEMU
    */

    //Ako je netko pobijedio (best dostignut)
    if((int)$wins["pobjeda_sudionik1"]==ceil($wins["najbolji_od"]/2)) {
        $q = "UPDATE knockout SET zavrsen=1 WHERE id_knockout=$id";
        $con->query($q);

        /* Odredi broj sljedeceg KO */
        $result = findMinKo($id_turnir);
        $row = $result->fetch_assoc();
        $min_ko = $row["min_id"];

        $result = getSudionici($id_turnir);
        $max_ko = (int)$result->num_rows / 2; // pola od broja sudionika

        $koNumber = (int)$id - $min_ko + 1; //redni broj ko-a
        //Finale
        if($koNumber == (int)$result->num_rows-1) {
            $winner=$con->query("SELECT naziv FROM sudionik WHERE id_sudionik=".$wins["id_sudionik1"])->fetch_assoc();
            return $arr = ["mec_knockout"=>-1, "winner"=>$winner["naziv"]];
        }

        $sljedeci_ko = -1;
        $brojac = 1;
        for($i=$max_ko; $i>0; $brojac++) {
            if($koNumber == $brojac) {
                if ($brojac % 2 == 0) {
                    $sljedeci_ko = $koNumber + $i - 1;
                    break;
                } else {
                    $sljedeci_ko = $koNumber + $i;
                    break;
                }
            }
            if($brojac%2==0)
                $i--;
        }

        //provjeri jeli $sljedeci_ko napravljen
        $next=$sljedeci_ko + $min_ko - 1; //id sljedeceg ko-a
        $result = checkIfKoExists($next);
        $row = $result->fetch_assoc();
        if($result->num_rows == 0 || !$result->num_rows || !$row["id_knockout"]) {
            //ako ne postoji INSERT
            $q = "INSERT INTO knockout VALUES(null, 0, 0, 1, ".$wins["id_sudionik1"].", null, 0, $id_turnir)";
            $con->query($q);

            //Sudionik 1 je pobjedio, vraćam da sam napravio novi knockout (mec_knockout=1)
            return $arr = ["mec_knockout"=>1, "id"=>$con->insert_id];
        }
        else {
            //ako postoji: sudionik2 se stavlja u sljedeci knockout
            $q = "UPDATE knockout SET id_sudionik2=".$wins["id_sudionik1"]. " WHERE id_knockout=$next";
            $con->query($q);
            //Select id sudionika1 iz knockouta
            $sudionik=$con->query("SELECT id_sudionik1 FROM knockout where id_knockout=$next")->fetch_assoc();
            $sudionik=$sudionik["id_sudionik1"];
            //Unos meča
            $m = "INSERT INTO mec VALUES(null, ".$wins["id_turnir"].", ".$wins["id_sudionik1"].", $sudionik, 0, 0, $next, NOW(), 1, 0)";
            $con->query($m);

            //Sudionik 1 je pobjedio, vraćam da sam napravio novi mec u novom knockoutu(mec_knockout=2)
            return $arr = ["mec_knockout"=>2, "id"=>$con->insert_id];
        }
    }
    elseif ((int)$wins["pobjeda_sudionik2"]==ceil($wins["najbolji_od"]/2)) {
        $q = "UPDATE knockout SET zavrsen=1 WHERE id_knockout=$id";
        $con->query($q);

        /* Odredi broj sljedeceg KO */
        $result = findMinKo($id_turnir);
        $row = $result->fetch_assoc();
        $min_ko = $row["min_id"]; // id najmanjeg ko

        $result = getSudionici($id_turnir);
        $max_ko = (int)$result->num_rows / 2; //broj ukupnih KO na pocetku turnira

        $koNumber = (int)$id - $min_ko + 1; // broj ovog ko-a
        //Finale
        if($koNumber == (int)$result->num_rows-1) {
            $winner=$con->query("SELECT naziv FROM sudionik WHERE id_sudionik=".$wins["id_sudionik2"])->fetch_assoc();
            return $arr = ["mec_knockout"=>-1, "winner"=>$winner["naziv"]];
        }

        $sljedeci_ko = -1;
        $brojac = 1;
        for($i=$max_ko; $i>0; $brojac++) {
            if($koNumber == $brojac) {
                if ($brojac % 2 == 0) {
                    $sljedeci_ko = $koNumber + $i - 1;
                    break;
                } else {
                    $sljedeci_ko = $koNumber + $i;
                    break;
                }
            }
            if($brojac%2==0)
                $i--;
        }

        //provjeri jeli $sljedeci_ko napravljen
        $next=$sljedeci_ko + $min_ko - 1;
        $result = checkIfKoExists($next);
        $row = $result->fetch_assoc();
        //check
        if($result->num_rows == 0 || !$result->num_rows || !$row["id_knockout"]) {
            //ako ne postoji INSERT
            $q = "INSERT INTO knockout VALUES(null, 0, 0, 1, ".$wins["id_sudionik2"].", null, 0, $id_turnir)";
            $con->query($q);

            //Sudionik 2 je pobjedio, vraćam da sam napravio novi knockout (mec_knockout=1)
            return $arr = ["mec_knockout"=>1, "id"=>$con->insert_id];
        }
        else {
            //ako postoji: UPDATE
            $q = "UPDATE knockout SET id_sudionik2=".$wins["id_sudionik2"]. " WHERE id_knockout=$next";
            $con->query($q);
            //Select id sudionika1 iz knockouta
            $sudionik=$con->query("SELECT id_sudionik1 FROM knockout where id_knockout=$next")->fetch_assoc();
            $sudionik=$sudionik["id_sudionik1"];
            //Unos meča
            $m = "INSERT INTO mec VALUES(null, ".$wins["id_turnir"].", ".$wins["id_sudionik2"].", $sudionik, 0, 0, $next, NOW(), 1, 0)";
            $con->query($m);

            //Sudionik 2 je pobjedio, vraćam da sam napravio novi mec u novom knockoutu(mec_knockout=2)
            return $arr = ["mec_knockout"=>2, "id"=>$con->insert_id];
        }
    }
    else {
        //stvori novi meč u taj knockout i vrati mec_knockout 0
        $con->query("INSERT INTO mec VALUES(null, ".$wins["id_turnir"].", ".$wins["id_sudionik1"].", ".$wins["id_sudionik2"].", 0, 0 , $id, NOW(), 1, 0)");
        return $arr=["mec_knockout"=>0, "id"=>$con->insert_id];
    }
}

function checkIfKoExists($id_ko) {
    $con=getDB();
    $q = "SELECT id_knockout FROM knockout WHERE id_knockout=$id_ko";
    return $con->query($q);
}
function findMinKo($id) {
    $q="SELECT MIN(id_knockout) AS min_id FROM knockout WHERE id_turnir=$id";
    $con=getDB();
    return $con->query($q);
}
function getMatchesFromKo($idKo) {
    $con=getDB();
    return $con->query("SELECT * FROM mec WHERE id_knockout=$idKo");
}
function finishTournament($id) {
    $con=getDB();
    $con->query("UPDATE turnir SET zavrsen=1 WHERE id_turnir=".$con->real_escape_string($id));
}
function getKnockouts($id_turnir) {
    $con=getDB();
    return $con->query("SELECT *, k.zavrsen AS zavrsenKnockout FROM knockout k                     
                        JOIN turnir t ON t.id_turnir=k.id_turnir
                        WHERE t.id_turnir=$id_turnir
                        GROUP BY k.id_knockout");
}
function getParticipantsFromKO($id) {
    $con=getDB();
    return $con->query("SELECT k.id_sudionik1 AS id_sud1, k.id_sudionik2 AS id_sud2, s1.naziv AS sudionik1, s2.naziv AS sudionik2, m.rez_domacin AS rezDomacin, m.rez_gost AS rezGost 
                        FROM knockout k 
                        LEFT OUTER JOIN sudionik s1 ON s1.id_sudionik=k.id_sudionik1
                        LEFT OUTER JOIN sudionik s2 ON s2.id_sudionik=k.id_sudionik2
                        LEFT OUTER JOIN mec m ON m.id_knockout=k.id_knockout  
                        WHERE k.id_knockout=".$con->real_escape_string($id))->fetch_assoc();
}
function getTipTurnira($id_turnir) {
    $con = getDB();
    $q = "SELECT tip, najbolji_od, zavrsen FROM turnir WHERE id_turnir=$id_turnir";
    return $con->query($q)->fetch_assoc();
}
function getBodovi($match_id) {
    $con = getDB();
    $q = "SELECT t.bod_pobjeda AS pob, t.bod_nerijeseno AS ner, t.bod_poraz AS por, ".
    "m.id_gost AS id_gost, m.id_domacin AS id_domacin ".
    "FROM turnir t JOIN mec m ON m.id_turnir = t.id_turnir WHERE m.id_mec = $match_id";
    return $con->query($q)->fetch_assoc();
}
function updatePoints($match_id, $rez_domacin, $rez_gost) {
    $con = getDB();
    $match_id = $con->real_escape_string($match_id);
    $result = getBodovi($match_id);
    $pob = $result["pob"];
    $ner = $result["ner"];
    $por = $result["por"];
    $id_domacin = $result["id_domacin"];
    $id_gost = $result["id_gost"];

    if($rez_domacin > $rez_gost) {
        $qUpdateDoma = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $pob ".
        "WHERE id_sudionik = $id_domacin";
        $qUpdateGost = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $por ".
        "WHERE id_sudionik = $id_gost";
    }
    else if($rez_domacin < $rez_gost) {
        $qUpdateDoma = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $por ".
        "WHERE id_sudionik = $id_domacin";
        $qUpdateGost = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $pob ".
        "WHERE id_sudionik = $id_gost";
    }
    else {
        $qUpdateDoma = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $ner ".
        "WHERE id_sudionik = $id_domacin";
        $qUpdateGost = "UPDATE sudionik SET bodovi_grupe = bodovi_grupe + $ner ".
        "WHERE id_sudionik = $id_gost";
    }
     $con->query($qUpdateDoma);
     $con->query($qUpdateGost);
}
function getBodOmjerIzMeca($id_mec) {
    $con=getDB();
    $id_mec=$con->real_escape_string($id_mec);
    $q = "SELECT t.bodovi_omjer AS bodovi_omjer FROM turnir t ".
    "JOIN mec m ON t.id_turnir=m.id_turnir ".
    "WHERE m.id_mec=$id_mec";
    return $con->query($q)->fetch_assoc();
}
function saveKnockoutBestOf2($id, $sud1, $sud2, $id_turnir, $id_mec, $id_sud1, $id_sud2) {
    $con=getDB();
    $id=$con->real_escape_string($id);
    $sud1=$con->real_escape_string($sud1);
    $sud2=$con->real_escape_string($sud2);
    $id_turnir=$con->real_escape_string($id_turnir);
    $id_mec=$con->real_escape_string($id_mec);
    $id_sud1=$con->real_escape_string($id_sud1);
    $id_sud2=$con->real_escape_string($id_sud2);
    $arr = array();


    saveMatchResults($id_mec, $sud1, $sud2);

    $q= "SELECT COUNT(*) AS br_meceva FROM mec WHERE id_knockout=$id AND zavrsen=1";
    $result = $con->query($q)->fetch_assoc();

    if($result["br_meceva"] == 2) {

        //zbrajanje golova sud1
        $q1 = "SELECT rez_domacin FROM mec WHERE id_knockout=$id AND id_domacin=$id_sud1";
        $result1 = $con->query($q1)->fetch_assoc();
        $sud1_goloviD = (int)$result1["rez_domacin"];

        $q2 = "SELECT rez_gost FROM mec WHERE id_knockout=$id AND id_gost=$id_sud1";
        $result2 = $con->query($q2)->fetch_assoc();
        $sud1_goloviG = (int)$result2["rez_gost"];

        //zbrajanje golova sud2
        $q3 = "SELECT rez_domacin FROM mec WHERE id_knockout=$id AND id_domacin=$id_sud2";
        $result3 = $con->query($q3)->fetch_assoc();
        $sud2_goloviD = (int)$result3["rez_domacin"];

        $q4 = "SELECT rez_gost FROM mec WHERE id_knockout=$id AND id_gost=$id_sud2";
        $result4 = $con->query($q4)->fetch_assoc();
        $sud2_goloviG = (int)$result4["rez_gost"];

        // prvi uvjet, suma golova
        if(($sud1_goloviD+$sud1_goloviG) > ($sud2_goloviD+$sud2_goloviG)) {
            $pobjednik = $id_sud1;
            $gubitnik = $id_sud2;
        }
        elseif (($sud1_goloviD+$sud1_goloviG) < ($sud2_goloviD+$sud2_goloviG)) {
            $pobjednik = $id_sud2;
            $gubitnik = $id_sud1;
        }

        else { //drugi uvjet, usporedba golova u gostima
            if($sud1_goloviG > $sud2_goloviG) {
                $pobjednik = $id_sud1;
                $gubitnik = $id_sud2;
            }
            elseif ($sud1_goloviG < $sud2_goloviG){
                $pobjednik = $id_sud2;
                $gubitnik = $id_sud1;
            }
        }
        //echo "pobjednik:$pobjednik, sud1_goloviD:$sud1_goloviD, sud1_goloviG:$sud1_goloviG, sud2_goloviD:$sud2_goloviD, sud2_goloviG:$sud2_goloviG";
        $q = "UPDATE knockout SET zavrsen=1 WHERE id_knockout=$id";
        $con->query($q);

        /* Odredi broj sljedeceg KO */
        $result = findMinKo($id_turnir);
        $row = $result->fetch_assoc();
        $min_ko = $row["min_id"]; // najmanji id knockouta iz turnira

        $result = getSudionici($id_turnir);
        $max_ko = (int)$result->num_rows / 2; // pola od broja sudionika

        $koNumber = (int)$id - $min_ko + 1; //redni broj ko-a

        //Finale ako je(br_knockouta == br_sudionika-1)
        if ($koNumber == (int)$result->num_rows - 1) {
            // nemam pojma zasto je $gubitnik
            $winner = $con->query("SELECT naziv FROM sudionik WHERE id_sudionik=".$gubitnik)->fetch_assoc();
            return $arr = ["mec_knockout" => -1, "winner" => $winner["naziv"]];
        }

        $sljedeci_ko = -1;
        $brojac = 1;
        for ($i = $max_ko; $i>0; $brojac++) {
            if ($koNumber == $brojac) {
                if ($brojac % 2 == 0) {
                    $sljedeci_ko = $koNumber + $i - 1;
                    break;
                } else {
                    $sljedeci_ko = $koNumber + $i;
                    break;
                }
            }
            if ($brojac % 2 == 0)
                $i--;
        }

        //provjeri jeli $sljedeci_ko napravljen
        $next = $sljedeci_ko + $min_ko - 1; //id sljedeceg ko-a
        $result = checkIfKoExists($next);
        $row = $result->fetch_assoc();
        //echo "next: $next, Sljedeci_ko: $sljedeci_ko";
        if ($result->num_rows == 0 || !$result->num_rows || !$row["id_knockout"]) {
            //ako ne postoji INSERT
            $q = "INSERT INTO knockout VALUES(null, 0, 0, 1, " . $pobjednik . ", null, 0, $id_turnir)";
            $con->query($q);

            //Sudionik 1 je pobjedio, vraćam da sam napravio novi knockout (mec_knockout=1)
            return $arr = ["mec_knockout" => 1, "id" => $con->insert_id];
        } else {
            //ako postoji: sudionik2 se stavlja u sljedeci knockout
            $q = "UPDATE knockout SET id_sudionik2=" . $pobjednik . " WHERE id_knockout=$next";
            $con->query($q);
            //Select id sudionika1 iz knockouta
            $sudionik = $con->query("SELECT id_sudionik1 FROM knockout where id_knockout=$next")->fetch_assoc();
            $sudionik = $sudionik["id_sudionik1"];
            //Unos meča
            $m = "INSERT INTO mec VALUES(null, " . $id_turnir . ", " . $pobjednik . ", $sudionik, 0, 0, $next, NOW(), 1, 0)";
            $con->query($m);

            //Sudionik 1 je pobjedio, vraćam da sam napravio novi mec u novom knockoutu(mec_knockout=2)
            return $arr = ["mec_knockout" => 2, "id" => $con->insert_id];
        }

    }
    elseif ($result["br_meceva"] < 2) {
        //stvori novi meč u taj knockout i vrati mec_knockout 0
        $con->query("INSERT INTO mec VALUES(null, $id_turnir, $id_sud2, $id_sud1, 0, 0 , $id, NOW(), 1, 0)");
        return $arr=["mec_knockout"=>0, "id"=>$con->insert_id];

    }
    else {
    }

}