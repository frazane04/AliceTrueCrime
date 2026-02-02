<?php
require_once __DIR__ . '/../db/funzioni_db.php';

$contenuto = loadTemplate('esplora');

$dbFunctions = new FunzioniDB();

$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

$view = isset($_GET['view']) ? trim($_GET['view']) : '';

$filtriAttivi = !empty($searchQuery) || !empty($categoriaFiltro) || $view === 'all';

// Costruzione Search Bar
$searchBarHtml = loadTemplate('../components/search_bar');

$categorie = $dbFunctions->getCategorie();


$optionsCategorie = '';
foreach ($categorie as $cat) {
    $selected = ($categoriaFiltro === $cat['Tipologia']) ? 'selected' : '';
    $optionsCategorie .= '<option value="' . htmlspecialchars($cat['Tipologia']) . '" ' . $selected . '>'
        . htmlspecialchars($cat['Tipologia']) . ' (' . $cat['conteggio'] . ')</option>';
}



$searchBarHtml = str_replace('{{SEARCH_VALUE}}', htmlspecialchars($searchQuery), $searchBarHtml);
$searchBarHtml = str_replace('{{OPTIONS_CATEGORIE}}', $optionsCategorie, $searchBarHtml);

$searchBarHtml = str_replace('{{PREFIX}}', getPrefix(), $searchBarHtml);

// Filtri Attivi
$filtriAttiviHtml = '';
if (!empty($searchQuery) || !empty($categoriaFiltro)) {
    $filtriAttiviHtml = '<div class="filtri-attivi"><span class="filtri-label">Filtri attivi:</span>';
    if (!empty($searchQuery))
        $filtriAttiviHtml .= '<span class="filtro-tag">Ricerca: "' . htmlspecialchars($searchQuery) . '"</span>';
    if (!empty($categoriaFiltro))
        $filtriAttiviHtml .= '<span class="filtro-tag">Categoria: ' . htmlspecialchars($categoriaFiltro) . '</span>';

    $filtriAttiviHtml .= '</div>';
}
$searchBarHtml = str_replace('{{FILTRI_ATTIVI}}', $filtriAttiviHtml, $searchBarHtml);

if ($filtriAttivi) {
    // Vista Ricerca
    $filtri = [
        'q' => $searchQuery,
        'categoria' => $categoriaFiltro
    ];

    $risultatiRicerca = $dbFunctions->cercaCasiConFiltri($filtri, 50);
    $htmlRisultati = generaHtmlCards($risultatiRicerca);
    $numRisultati = count($risultatiRicerca);

    $titoloSezione = 'Esplora tutti i casi';
    if (!empty($categoriaFiltro))
        $titoloSezione = $categoriaFiltro;
    if (!empty($searchQuery))
        $titoloSezione = 'Risultati ricerca: "' . htmlspecialchars($searchQuery) . '"';

    $mainContentHtml = '<section class="explore-section search-results-section">
        <div class="section-top">
            <h2>' . $titoloSezione . '</h2>
            <span class="results-count">' . $numRisultati . ' risultati</span>
        </div>
        <div class="explore-grid">
            ' . $htmlRisultati . '
        </div>
    </section>';

} else {
    // Dashboard

    $casiMain = $dbFunctions->getCasiRecenti(4);

    $casiPiuLetti = $dbFunctions->getCasiPiuLetti(4);
    $casiRecenti = $dbFunctions->getCasiRecenti(4);

    $htmlMain = generaHtmlCards($casiMain);
    $htmlPiuLetti = generaHtmlCards($casiPiuLetti);
    $htmlRecenti = generaHtmlCards($casiRecenti);

    $mainContentHtml = '
    <section class="explore-section">
        <div class="section-top">
            <h2>Esplora tutti i casi</h2>
            <a href="' . getPrefix() . '/esplora?view=all" class="view-all-link">Mostra archivio completo</a>
        </div>
        <div class="explore-grid">
            ' . $htmlMain . '
        </div>
    </section>

    <section class="explore-section">
        <div class="section-top">
            <h2>Casi più letti</h2>
            <span class="section-badge">Popolari</span>
        </div>
        <div class="explore-grid">
            ' . $htmlPiuLetti . '
        </div>
    </section>
    
     <section class="explore-section">
        <div class="section-top">
            <h2>Nuovi arrivi</h2>
             <span class="section-badge">Recenti</span>
        </div>
        <div class="explore-grid">
            ' . $htmlRecenti . '
        </div>
    </section>
    ';
}



function generaHtmlCards($listaCasi)
{
    if (empty($listaCasi))
        return '<p class="no-results">Nessun caso trovato.</p>';
    $html = '';
    foreach ($listaCasi as $caso) {
        $descrizione = htmlspecialchars(substr($caso['Descrizione'] ?? '', 0, 100)) . '...';

        $metaParts = [];
        if (!empty($caso['Data']))
            $metaParts[] = '<span class="card-meta-item">' . date('Y', strtotime($caso['Data'])) . '</span>';
        if (!empty($caso['Luogo']))
            $metaParts[] = '<span class="card-meta-item">' . htmlspecialchars($caso['Luogo']) . '</span>';
        $cardMeta = implode('<span class="card-meta-separator"></span>', $metaParts);

        $tipologia = $caso['Tipologia'] ?? '';
        $tipologieEn = ['Serial killer', 'Cold case', 'Celebrity'];
        $tipologiaLang = in_array($tipologia, $tipologieEn) ? ' lang="en"' : '';

        $html .= renderComponent('card-caso-esplora', [
            'IMMAGINE' => getImageUrl($caso['Immagine'] ?? null),
            'TITOLO' => htmlspecialchars($caso['Titolo']),
            'DESCRIZIONE' => $descrizione,
            'TIPOLOGIA' => htmlspecialchars($tipologia),
            'TIPOLOGIA_LANG' => $tipologiaLang,
            'CARD_META' => $cardMeta,
            'LINK' => getPrefix() . '/esplora/' . urlencode(getSlugFromCaso($caso))
        ]);
    }
    return $html;
}

$contenuto = str_replace('{{SEARCH_BAR}}', $searchBarHtml, $contenuto);
$contenuto = str_replace('{{CONTENT}}', $mainContentHtml, $contenuto);
$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);


if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'html_risultati' => $htmlRisultati ?? '',
        'num_risultati' => $numRisultati ?? 0,
        'titolo_ricerca' => $titoloSezione ?? 'Risultati',
    ]);
    exit;
}

$contenuto .= '<script src="' . getPrefix() . '/js/esplora.js"></script>';

$descrizioneEsplora = "Archivio completo dei casi di AliceTrueCrime. " . ($categoriaFiltro ? "Esplora la categoria $categoriaFiltro." : "Cerca tra centinaia di fascicoli di cronaca nera.");
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto, $descrizioneEsplora);
?>