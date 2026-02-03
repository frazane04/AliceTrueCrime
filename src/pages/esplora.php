<?php
// Dashboard esplora casi

require_once __DIR__ . '/../db/funzioni_db.php';
require_once __DIR__ . '/../helpers/utils.php';

$contenuto = loadTemplate('esplora');
$dbFunctions = new FunzioniDB();

function generaHtmlCardsDashboard($listaCasi)
{
    if (empty($listaCasi))
        return '<p class="no-results">Nessun caso disponibile.</p>';
        
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

$casiMain = $dbFunctions->getCasiRecenti(4);
$casiPiuLetti = $dbFunctions->getCasiPiuLetti(4);
$casiRecenti = $dbFunctions->getCasiRecenti(4);

$htmlMain = generaHtmlCardsDashboard($casiMain);
$htmlPiuLetti = generaHtmlCardsDashboard($casiPiuLetti);
$htmlRecenti = generaHtmlCardsDashboard($casiRecenti);

$mainContentHtml = '
<section class="explore-section">
    <div class="section-top">
        <h2>Esplora tutti i casi</h2>
        <a href="' . getPrefix() . '/esplora-tutti" class="view-all-link">Mostra archivio completo</a>
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
</section>';

$contenuto = str_replace('{{CONTENT}}', $mainContentHtml, $contenuto);
$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);

$descrizioneSEO = "Dashboard di AliceTrueCrime: scopri i casi di cronaca nera più letti, le ultime inchieste e i nuovi arrivi nel nostro archivio.";
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto, $descrizioneSEO);
?>