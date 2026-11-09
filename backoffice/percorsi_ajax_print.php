<style>

  .accordion-button {
    padding: .5rem .75rem;
    /*font-size: 0.95rem;*/
}



.form-check-label {
    font-size: 1.5rem;
}

.form-check {
    margin-bottom: .25rem !important;
}
</style>

<?php

$gruppi = [];

while ($row = pg_fetch_assoc($result)) {

    $tipo = ($row['previsto'] == 1)
        ? 'previsti'
        : 'non_previsti';

    $fascia = $row['fascia'];

    $gruppi[$tipo][$fascia][] = $row;
}

$dt_print = DateTime::createFromFormat('d/m/Y', $data);

$giorni = [
    1 => 'LUN',
    2 => 'MAR',
    3 => 'MER',
    4 => 'GIO',
    5 => 'VEN',
    6 => 'SAB',
    7 => 'DOM'
];

$descrizione = $giorni[$dt_print->format('N')]
             . ' della settimana '
             . $dt_print->format('W')
             . ' del '
             . $dt_print->format('o');
/* =====================================================
 * PERCORSI PREVISTI
 * ===================================================== */
if (!empty($gruppi['previsti'])) {

    echo '
    <h4 class="mb-3">
        <i class="fa-solid fa-calendar-check text-success"></i>
        <i>Percorsi previsti ('.$descrizione.')</i>
    </h4>

    <div class="accordion mb-4" id="accordionPrevisti">
    ';

    $idx = 0;

    foreach ($gruppi['previsti'] as $fascia => $percorsi) {

        $collapseId = "previsti_" . $idx;

        echo '
        <div class="accordion-item">

            <h2 class="accordion-header" id="heading_' . $collapseId . '">
                <button class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#' . $collapseId . '"
                        aria-expanded="true"
                        aria-controls="' . $collapseId . '">

                    <strong>' . htmlspecialchars($fascia) . '</strong>

                    <span class="badge bg-success ms-2">
                        ' . count($percorsi) . '
                    </span>

                </button>
            </h2>

            <div id="' . $collapseId . '"
                 class="accordion-collapse collapse show"
                 aria-labelledby="heading_' . $collapseId . '">

                <div class="accordion-body">
        ';

        foreach ($percorsi as $row) {

            echo '
            <div class="form-check mb-2">

                <input class="form-check-input"
                       type="radio"
                       name="percorso"
                       id="' . $row['id_percorso'] . '"
                       value="' . $row['id_percorso'] . '"
                       onchange="scelto_percorso(this.value);">

                <label class="form-check-label"
                       for="' . $row['id_percorso'] . '">

                    <strong>' . htmlspecialchars($row['id_percorso']) . '</strong>
                    - ' . htmlspecialchars($row['desc_percorso']) . '

                </label>

            </div>';
        }

        echo '
                </div>
            </div>
        </div>
        ';

        $idx++;
    }

    echo '</div>';
}


/* =====================================================
 * PERCORSI NON PREVISTI
 * ===================================================== */
if (!empty($gruppi['non_previsti'])) {

    echo '
    <hr>

    <h4 class="mb-3 text-secondary">
        <i class="fa-solid fa-calendar-xmark"></i>
        <i>Percorsi non previsti</i>
    </h4>

    <div class="accordion mb-4" id="accordionNonPrevisti">
    ';

    $idx = 0;

    foreach ($gruppi['non_previsti'] as $fascia => $percorsi) {

        $collapseId = "nonprevisti_" . $idx;

        echo '
        <div class="accordion-item">

            <h2 class="accordion-header" id="heading_' . $collapseId . '">
                <button class="accordion-button"
                        style="background-color:aliceblue"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#' . $collapseId . '"
                        aria-expanded="false"
                        aria-controls="' . $collapseId . '">

                    <strong>' . htmlspecialchars($fascia) . '</strong>

                    <span class="badge bg-secondary ms-2">
                        ' . count($percorsi) . '
                    </span>

                </button>
            </h2>

            <div id="' . $collapseId . '"
                 class="accordion-collapse collapse show"
                 aria-labelledby="heading_' . $collapseId . '">

                <div class="accordion-body">
        ';

        foreach ($percorsi as $row) {

            echo '
            <div class="form-check mb-2">
                <input class="form-check-input"
                       type="radio"
                       name="percorso"
                       id="'.$row['id_percorso'] . '"
                       value="' . $row['id_percorso'] . '"
                       onchange="scelto_percorso(this.value);">

                <label class="form-check-label text-secondary"
                       for="' . $row['id_percorso'] . '">

                    <strong>' . htmlspecialchars($row['id_percorso']) . '</strong>
                    - ' . htmlspecialchars($row['desc_percorso']) . '

                </label>

            </div>';
        }

        echo '
                </div>
            </div>
        </div>
        ';

        $idx++;
    }

    echo '</div>';
}
?>