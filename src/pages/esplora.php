<?php
require_once __DIR__ . '/../db/funzioni_db.php';

$contenuto = loadTemplate('esplora');

// Recupero dati dal Database
$dbFunctions = new FunzioniDB();

// Recupero parametri di ricerca/filtro dalla query string
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$annoFiltro = isset($_GET['anno']) ? trim($_GET['anno']) : '';
$view = isset($_GET['view']) ? trim($_GET['view']) : '';

// Verifica se ci sono filtri attivi o se siamo in vista "all"
$filtriAttivi = !empty($searchQuery) || !empty($categoriaFiltro) || !empty($annoFiltro) || $view === 'all';

// Componente Barra di Ricerca
$searchBarHtml = '';

if ($filtriAttivi) {
    // === VISTA RICERCA / TUTTI ===

    // Carica componente search bar
    $searchBarHtml = loadTemplate('../components/search_bar');

    // Popola placeholder search bar
    $categorie = $dbFunctions->getCategorie();
    $anni = $dbFunctions->getAnniDisponibili();

    $optionsCategorie = '';
    foreach ($categorie as $cat) {
        $selected = ($categoriaFiltro === $cat['Tipologia']) ? 'selected' : '';
        $optionsCategorie .= '<option value="' . htmlspecialchars($cat['Tipologia']) . '" ' . $selected . '>'
            . htmlspecialchars($cat['Tipologia']) . ' (' . $cat['conteggio'] . ')</option>';
    }

    $optionsAnni = '';
    foreach ($anni as $anno) {
        $selected = ($annoFiltro == $anno) ? 'selected' : '';
        $optionsAnni .= '<option value="' . $anno . '" ' . $selected . '>' . $anno . '</option>';
    }

    $searchBarHtml = str_replace('{{SEARCH_VALUE}}', htmlspecialchars($searchQuery), $searchBarHtml);
    $searchBarHtml = str_replace('{{OPTIONS_CATEGORIE}}', $optionsCategorie, $searchBarHtml);
    $searchBarHtml = str_replace('{{OPTIONS_ANNI}}', $optionsAnni, $searchBarHtml);
    $searchBarHtml = str_replace('{{PREFIX}}', getPrefix(), $searchBarHtml);

    // Genera filtri attivi HTML
    $filtriAttiviHtml = '';
    if (!empty($searchQuery) || !empty($categoriaFiltro) || !empty($annoFiltro)) {
        $filtriAttiviHtml = '<div class="filtri-attivi"><span class="filtri-label">Filtri attivi:</span>';
        if (!empty($searchQuery))
            $filtriAttiviHtml .= '<span class="filtro-tag">Ricerca: "' . htmlspecialchars($searchQuery) . '"</span>';
        if (!empty($categoriaFiltro))
            $filtriAttiviHtml .= '<span class="filtro-tag">Categoria: ' . htmlspecialchars($categoriaFiltro) . '</span>';
        if (!empty($annoFiltro))
            $filtriAttiviHtml .= '<span class="filtro-tag">Anno: ' . htmlspecialchars($annoFiltro) . '</span>';
        $filtriAttiviHtml .= '</div>';
    }
    $searchBarHtml = str_replace('{{FILTRI_ATTIVI}}', $filtriAttiviHtml, $searchBarHtml);
    // === VISTA RISULTATI RICERCA ===
    $filtri = [
        'q' => $searchQuery,
        'categoria' => $categoriaFiltro,
        'anno' => $annoFiltro
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
    // === VISTA DASHBOARD ESTESA ===
    // Sezione 1: "Esplora tutti i casi" - Mostra gli ultimi 4 inseriti (o ordine per data)
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

// Helper Cards
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

// Replace Placeholders
$contenuto = str_replace('{{SEARCH_BAR}}', $searchBarHtml, $contenuto);
$contenuto = str_replace('{{CONTENT}}', $mainContentHtml, $contenuto);
$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);


// Output JSON per AJAX
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    // Se siamo qui è probabile che siamo nella vista ricerca
    echo json_encode([
        'html_risultati' => $htmlRisultati ?? '',
        'num_risultati' => $numRisultati ?? 0,
        'titolo_ricerca' => $titoloSezione ?? 'Risultati',
    ]);
    exit;
}

$contenuto .= '<script src="' . getPrefix() . '/js/esplora.js"></script>';

echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto);
?>