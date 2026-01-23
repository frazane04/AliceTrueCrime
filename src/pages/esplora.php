<?php
require_once __DIR__ . '/../db/funzioni_db.php';

$contenuto = loadTemplate('esplora');

// Recupero dati dal Database
$dbFunctions = new FunzioniDB();

// Recupero parametri di ricerca/filtro dalla query string
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$annoFiltro = isset($_GET['anno']) ? trim($_GET['anno']) : '';

// Verifica se ci sono filtri attivi
$filtriAttivi = !empty($searchQuery) || !empty($categoriaFiltro) || !empty($annoFiltro);

// Recupero categorie e anni per i select dei filtri
$categorie = $dbFunctions->getCategorie();
$anni = $dbFunctions->getAnniDisponibili();

// Genera le opzioni per il select delle categorie
$optionsCategorie = '';
foreach ($categorie as $cat) {
    $selected = ($categoriaFiltro === $cat['Tipologia']) ? 'selected' : '';
    $optionsCategorie .= '<option value="' . htmlspecialchars($cat['Tipologia']) . '" ' . $selected . '>'
                       . htmlspecialchars($cat['Tipologia']) . ' (' . $cat['conteggio'] . ')</option>';
}

// Genera le opzioni per il select degli anni
$optionsAnni = '';
foreach ($anni as $anno) {
    $selected = ($annoFiltro == $anno) ? 'selected' : '';
    $optionsAnni .= '<option value="' . $anno . '" ' . $selected . '>' . $anno . '</option>';
}

// Funzione Helper per generare l'HTML delle Card
function generaHtmlCards($listaCasi) {
    if (empty($listaCasi)) {
        return '<p class="no-results">Nessun caso trovato in questa categoria.</p>';
    }

    $html = '';
    foreach ($listaCasi as $caso) {
        $descrizione = htmlspecialchars(substr($caso['Descrizione'] ?? '', 0, 120)) . '...';

        // Genera metadata (data e/o luogo)
        $metaParts = [];
        if (!empty($caso['Data'])) {
            $dataFormattata = date('Y', strtotime($caso['Data']));
            $metaParts[] = '<span class="card-meta-item card-meta-year">' . $dataFormattata . '</span>';
        }
        if (!empty($caso['Luogo'])) {
            $metaParts[] = '<span class="card-meta-item card-meta-location">' . htmlspecialchars($caso['Luogo']) . '</span>';
        }
        $cardMeta = implode('<span class="card-meta-separator"></span>', $metaParts);

        $html .= renderComponent('card-caso-esplora', [
            'IMMAGINE'    => getImageUrl($caso['Immagine'] ?? null),
            'TITOLO'      => htmlspecialchars($caso['Titolo']),
            'DESCRIZIONE' => $descrizione,
            'TIPOLOGIA'   => htmlspecialchars($caso['Tipologia'] ?? ''),
            'CARD_META'   => $cardMeta,
            'LINK'        => getPrefix() . '/caso/' . urlencode(getSlugFromCaso($caso))
        ]);
    }

    return $html;
}

// Se ci sono filtri attivi, mostra i risultati della ricerca
if ($filtriAttivi) {
    $filtri = [
        'q' => $searchQuery,
        'categoria' => $categoriaFiltro,
        'anno' => $annoFiltro
    ];

    $risultatiRicerca = $dbFunctions->cercaCasiConFiltri($filtri, 50);
    $htmlRisultati = generaHtmlCards($risultatiRicerca);
    $numRisultati = count($risultatiRicerca);

    // Titolo dinamico in base al filtro
    $titoloRicerca = 'Risultati della ricerca';
    if (!empty($categoriaFiltro) && empty($searchQuery) && empty($annoFiltro)) {
        $titoloRicerca = htmlspecialchars($categoriaFiltro);
    }

    // Genera la sezione risultati
    $sezioneRisultati = '<section class="explore-section search-results-section">
        <div class="section-top">
            <h2>' . $titoloRicerca . '</h2>
            <span class="results-count">' . $numRisultati . ' caso/i trovato/i</span>
        </div>
        <div class="explore-grid">
            ' . $htmlRisultati . '
        </div>
    </section>';

    // Genera i tag dei filtri attivi
    $filtriAttiviHtml = '<div class="filtri-attivi">';
    $filtriAttiviHtml .= '<span class="filtri-label">Filtri attivi:</span>';

    if (!empty($searchQuery)) {
        $filtriAttiviHtml .= '<span class="filtro-tag">Ricerca: "' . htmlspecialchars($searchQuery) . '"</span>';
    }
    if (!empty($categoriaFiltro)) {
        $filtriAttiviHtml .= '<span class="filtro-tag">Categoria: ' . htmlspecialchars($categoriaFiltro) . '</span>';
    }
    if (!empty($annoFiltro)) {
        $filtriAttiviHtml .= '<span class="filtro-tag">Anno: ' . htmlspecialchars($annoFiltro) . '</span>';
    }

    $filtriAttiviHtml .= '</div>';

    // Sostituisci i placeholder per la modalità filtri attivi
    $contenuto = str_replace('{{SEZIONE_RISULTATI}}', $sezioneRisultati, $contenuto);
    $contenuto = str_replace('{{FILTRI_ATTIVI}}', $filtriAttiviHtml, $contenuto);

    // Rimuovi completamente le sezioni di default (tutto tra i marker)
    $contenuto = preg_replace('/\{\{SEZIONI_DEFAULT\}\}.*?\{\{\/SEZIONI_DEFAULT\}\}/s', '', $contenuto);

} else {
    // Nessun filtro attivo - mostra le sezioni di default
    $casiPiuLetti = $dbFunctions->getCasiPiuLetti(3);
    $serialKillers = $dbFunctions->getCasiPerCategoria('Serial killer', 6);
    $amoreTossico = $dbFunctions->getCasiPerCategoria('Amore tossico', 6);
    $celebrity = $dbFunctions->getCasiPerCategoria('Celebrity', 6);
    $casiItaliani = $dbFunctions->getCasiPerCategoria('Casi mediatici italiani', 6);

    // Conteggi per i link "Mostra tutti"
    $countSerialKiller = $dbFunctions->contaCasiPerCategoria('Serial killer');
    $countAmoreTossico = $dbFunctions->contaCasiPerCategoria('Amore tossico');
    $countCelebrity = $dbFunctions->contaCasiPerCategoria('Celebrity');
    $countCasiItaliani = $dbFunctions->contaCasiPerCategoria('Casi mediatici italiani');

    // Generazione dell'HTML per ogni sezione
    $htmlPiuLetti     = generaHtmlCards($casiPiuLetti);
    $htmlSerialKiller = generaHtmlCards($serialKillers);
    $htmlAmoreTossico = generaHtmlCards($amoreTossico);
    $htmlCelebrity    = generaHtmlCards($celebrity);
    $htmlCasiItaliani = generaHtmlCards($casiItaliani);

    // Sostituzione dei Placeholder nel template
    $contenuto = str_replace('{{GRID_PIU_LETTI}}', $htmlPiuLetti, $contenuto);
    $contenuto = str_replace('{{GRID_SERIAL_KILLER}}', $htmlSerialKiller, $contenuto);
    $contenuto = str_replace('{{GRID_AMORE_TOSSICO}}', $htmlAmoreTossico, $contenuto);
    $contenuto = str_replace('{{GRID_CELEBRITY}}', $htmlCelebrity, $contenuto);
    $contenuto = str_replace('{{GRID_CASI_ITALIANI}}', $htmlCasiItaliani, $contenuto);

    // Sostituzione conteggi
    $contenuto = str_replace('{{COUNT_SERIAL_KILLER}}', $countSerialKiller, $contenuto);
    $contenuto = str_replace('{{COUNT_AMORE_TOSSICO}}', $countAmoreTossico, $contenuto);
    $contenuto = str_replace('{{COUNT_CELEBRITY}}', $countCelebrity, $contenuto);
    $contenuto = str_replace('{{COUNT_CASI_ITALIANI}}', $countCasiItaliani, $contenuto);

    // Rimuovi placeholder vuoti e marker delle sezioni default
    $contenuto = str_replace('{{SEZIONE_RISULTATI}}', '', $contenuto);
    $contenuto = str_replace('{{FILTRI_ATTIVI}}', '', $contenuto);
    $contenuto = str_replace('{{SEZIONI_DEFAULT}}', '', $contenuto);
    $contenuto = str_replace('{{/SEZIONI_DEFAULT}}', '', $contenuto);
}

// Sostituzioni comuni
$contenuto = str_replace('{{SEARCH_VALUE}}', htmlspecialchars($searchQuery), $contenuto);
$contenuto = str_replace('{{OPTIONS_CATEGORIE}}', $optionsCategorie, $contenuto);
$contenuto = str_replace('{{OPTIONS_ANNI}}', $optionsAnni, $contenuto);
$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);

// Output finale tramite utils.php
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto);
?>
