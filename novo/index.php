<?php
include '../functions.php';
sessionValidation();
$title = "Novo natjecanje";
include '../header.php';
?>
<div class="container">
    <div class="row" id="prvi">
        <div class="input-field col s12 m6 l4 offset-m3 offset-l4" id="vrh">
            <input type="text" length="30" id="naziv">
            <label for="naziv">Naziv natjecanja</label>
        </div>

        <div class="input-field col s12 m12 l12 radioArea center">
            <input name="group" type="radio" id="grupno"/>
            <label for="grupno">Prvenstvo</label>

            <input name="group" type="radio" id="knockout"/>
            <label for="knockout">Knock out</label>

            <input name="group" type="radio" id="grupno-knockout"/>
            <label for="grupno-knockout">Grupno + Knock out</label>
        </div>
        <br>
        <div class="col s12">
            <hr>
        </div>
        <br>
        <div class="input-field col s12 m6 l2 none" id="momcadiDiv">
            <input type="number" name="broj_natjecatelja" id="broj_natjecatelja" class="validate" min="2" max="64"
                   required>
            <label for="broj_natjecatelja">Broj natjecatelja:</label>
        </div>
        <div class="input-field col s12 m6 l2 none" id="koMomcadiDiv">
            <select name="ko_broj_natjecatelja" id="ko_broj_natjecatelja" required>
                <option value="4">4</option>
                <option value="8">8</option>
                <option value="16">16</option>
                <option value="32">32</option>
                <option value="64">64</option>
            </select>
            <label for="ko_broj_natjecatelja">Broj natjecatlja:</label>
        </div>

        <div class="input-field col s12 m6 l2 none" id="susretiDiv">
            <input id="broj_susreta" type="number" name="broj_susreta" class="validate" min="1" max="4" required>
            <label for="broj_susreta">Broj susreta:</label>
        </div>

        <div class="input-field col s12 m6 l2 none" id="brojgrupaDiv">
            <input id="broj_grupa" type="number" name="broj_grupa" class="validate" min="2" max="32" required>
            <label for="broj_grupa">Broj grupa:</label>
        </div>

        <div class="input-field col s12 m6 l2 none" id="bestDiv">
            <select name="best-of" id="best-of" required>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="5">5</option>
                <option value="7">7</option>
            </select>
            <label for="best-of">Najbolji od:</label>
        </div>

        <div class="input-field col s12 m6 l2 none" id="rangDiv">
            <div class="center">
                <p class="tip-natjecanja">Rangiranje:</p>
            </div>
            <input name="rangiranje" type="radio" id="bodovi" value="1"/>
            <label for="bodovi">Bodovi</label>
            <input name="rangiranje" type="radio" id="omjer" value="0" checked="checked"/>
            <label for="omjer">Omjer</label>
        </div>

        <div class="bodovi-field col s12 m6 l2 none" id="bodoviDiv">
            <div class="input-field">
                <input class="validate" name="pobjeda" type="number" id="pobjeda" min="1" max="100" required/>
                <label for="pobjeda" class="active">Pobjeda</label>
                <br>
            </div>
            <div class="input-field">
                <input class="validate" name="nerijeseno" type="number" id="nerijeseno" min="0" max="100" required/>
                <label for="nerijeseno" class="active">Neriješeno</label>
                <br>
            </div>
            <div class="input-field">
                <input class="validate" name="poraz" type="number" id="poraz" min="-100" max="100" required/>
                <label for="poraz" class="active">Poraz</label>
            </div>
        </div>

        <div class="tipka col s12 center none" id="btnDiv">
            <button class="btn-large waves-dark" onClick="return provjera()">Dalje</button>
        </div>
        <div id="errori" class="col s12 center"></div>
    </div>

    <div class="none center" id="drugi">
        <div class="row" id="unosSudionika">
        </div>
        <br>
        <div id="errori2" class="col s12 center"></div>
        <br>
        <div class="col s6 m6 l4 offset-s2 offset-m4 offset-l4">
            <button class="btn-large waves-effect waves-dark" onclick="Unos()">Novo natjecanje</button>
        </div>
    </div>
</div>
<?php
include '../footer.php';
?>
