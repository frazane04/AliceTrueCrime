<?php
// Archivio completo casi con ricerca

require_once __DIR__ . '/../db/funzioni_db.php';
require_once __DIR__ . '/../helpers/utils.php';

$dbFunctions = new FunzioniDB();

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$filtri = ['q' => $searchQuery, 'categoria' => $categoriaFiltro];
$risultatiRicerca = $dbFunctions->cercaCasiConFiltri($filtri, 100);
$numRisultati = count($risultatiRicerca);

$titoloMostrato = $searchQuery ? 'Risultati per: "' . htmlspecialchars($searchQuery) . '"' : 'Archivio Completo dei Casi';
$sottotitolo = "Sfoglia tutti i fascicoli presenti nel nostro database.";

$htmlRisultati = generaHtmlCards($risultatiRicerca);

// AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'html_risultati' => $htmlRisultati,
        'num_risultati' => $numRisultati,
        'titolo_ricerca' => $titoloMostrato
    ]);
    exit;
}

$searchBarHtml = loadTemplate('../components/search_bar');
$categorie = $dbFunctions->getCategorie();

$optionsCategorie = '';
foreach ($categorie as $cat) {
    $selected = ($categoriaFiltro === $cat['Tipologia']) ? 'selected' : '';
    $optionsCategorie .= '<option value="' . htmlspecialchars($cat['Tipologia']) . '" ' . $selected . '>'
        . htmlspecialchars($cat['Tipologia']) . ' (' . $cat['conteggio'] . ')</option>';
}

$searchBarHtml = str_replace([
    '{{SEARCH_VALUE}}',
    '{{OPTIONS_CATEGORIE}}',
    '{{PREFIX}}',
    'action="' . getPrefix() . '/esplora"',
    'data-url="' . getPrefix() . '/esplora"',
    '{{FILTRI_ATTIVI}}'
], [
    htmlspecialchars($searchQuery),
    $optionsCategorie,
    getPrefix(),
    'action="' . getPrefix() . '/esplora-tutti"',
    'data-url="' . getPrefix() . '/esplora-tutti"',
    ''
], $searchBarHtml);

$htmlPagina = '
<div class="sezione-stretta">
    <section class="explore-section">
        <div class="section-top">
            <h1>' . $titoloMostrato . '</h1>
            <p class="section-subtitle">' . $sottotitolo . '</p>
            <span class="section-badge">' . $numRisultati . ' Casi</span>
        </div>

        ' . $searchBarHtml . '

        <div id="esplora-content">
            <div id="results-grid" class="explore-grid">
                ' . $htmlRisultati . '
            </div>
        </div>
    </section>
</div>';

$contenuto = $htmlPagina;
$contenuto .= '<script src="' . getPrefix() . '/js/esplora.js"></script>';

$descrizioneSEO = "Sfoglia l'archivio completo dei casi di cronaca nera di AliceTrueCrime. Trovati $numRisultati fascicoli.";
echo getTemplatePage("Archivio Casi - AliceTrueCrime", $contenuto, $descrizioneSEO);