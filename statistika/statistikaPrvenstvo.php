
<table class="table-main display" id="results-table" data-page-length='100'>
    <thead>
    <tr>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Redni broj">#</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Naziv sudionika">Sudionik</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Odigrani susreti">OS</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Pobjede">P</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Neriješeni">N</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Porazi">I</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Rang">G</th>
        <th class="tooltipped" data-position="top" data-delay="50" data-tooltip="Bodovi/omjer">B</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $participants=getParticipantsFromTournament($_GET["id"]);
    $counter=1;
    while($participant=$participants->fetch_assoc()) {
        ?>
        <tr rel="<?=$participant["id_sudionik"]?>">
            <td><?=$counter?></td>
            <td><?=$participant["naziv"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
            <td><?=$participant["id_sudionik"]?></td>
        </tr>
        <?php
        $counter++;
    }
    ?>
    </tbody>
</table>

<script>
    $('#results-table').DataTable({
        paging: false,
        columnDefs: [
            {
                "targets": [1],
                "searchable": true
            },
            {
                "targets": [0,2,3,4,5,6],
                "searchable": false
            }
        ],
        oLanguage: {
            "sSearch": "<span>Pretraga:</span> _INPUT_", //search
            "sLengthMenu": "Prikaz _MENU_ podataka",
            "sEmptyTable": "Nemaš niti jedan turnir",
            "sZeroRecords": "Nema podataka",
            "sInfoEmpty": "Nema podataka za prikazati",
            "oPaginate": {
                "sNext": "Sljedeće",
                "sPrevious": "Prethodno"
            },
            "sInfo": "Ukupno sudionika: _TOTAL_"
        },
        aLengthMenu: [[50, 100, 250, 500], [50, 100, 250, 500]]
    });
</script>