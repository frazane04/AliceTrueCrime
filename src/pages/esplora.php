<?php
require_once __DIR__ . '/../db/funzioni_db.php';

$contenuto = loadTemplate('esplora');

// Recupero dati dal Database
$dbFunctions = new FunzioniDB();

$casiPiuLetti = $dbFunctions->getCasiPiuLetti(3); // Top 3 più letti
$serialKillers = $dbFunctions->getCasiPerCategoria('Serial killer', 6);
$amoreTossico = $dbFunctions->getCasiPerCategoria('Amore tossico', 6);
$celebrity = $dbFunctions->getCasiPerCategoria('Celebrity', 6);
$casiItaliani = $dbFunctions->getCasiPerCategoria('Casi mediatici italiani', 6);

// Funzione Helper per generare l'HTML delle Card
function generaHtmlCards($listaCasi) {
    if (empty($listaCasi)) {
        return '<p class="no-results">Nessun caso trovato in questa categoria.</p>';
    }

    $html = '';
    foreach ($listaCasi as $caso) {
        $descrizione = htmlspecialchars(substr($caso['Descrizione'] ?? '', 0, 120)) . '...';

        $html .= renderComponent('card-caso-esplora', [
            'IMMAGINE'    => getImageUrl($caso['Immagine'] ?? null),
            'TITOLO'      => htmlspecialchars($caso['Titolo']),
            'DESCRIZIONE' => $descrizione,
            'TIPOLOGIA'   => htmlspecialchars($caso['Tipologia'] ?? ''),
            'LINK'        => getPrefix() . '/caso/' . urlencode(getSlugFromCaso($caso))
        ]);
    }

    return $html;
}

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

// Output finale tramite utils.php
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto);
?>