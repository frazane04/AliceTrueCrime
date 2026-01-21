<?php
require_once __DIR__ . '/funzioni_db.php';

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
    $prefix = getPrefix();
    
    foreach ($listaCasi as $caso) {
        // Gestione immagine
        $imgSrc = !empty($caso['Immagine']) 
            ? $prefix . '/' . htmlspecialchars($caso['Immagine'])
            : $prefix . '/assets/img/caso-placeholder.jpeg';
        
        // Sanitizzazione dati
        $titolo = htmlspecialchars($caso['Titolo']);
        $descrizione = htmlspecialchars(substr($caso['Descrizione'], 0, 120)) . '...';
        $tipologia = htmlspecialchars($caso['Tipologia']);

        $slug = !empty($caso['Slug']) 
            ? $caso['Slug'] 
            : strtolower(str_replace(' ', '-', $caso['Titolo']));
        
        $linkCaso = $prefix . '/caso/' . urlencode($slug);
        
        $html .= <<<HTML
        <article class="case-card" data-categoria="{$tipologia}">
            <div class="card-image">
                <img src="{$imgSrc}" alt="Copertina caso {$titolo}" loading="lazy" />
            </div>
            <div class="card-content">
                <h3>{$titolo}</h3>
                <p>{$descrizione}</p>
                <a href="{$linkCaso}" class="btn-card" aria-label="Leggi approfondimento su {$titolo}">
                    Leggi Caso
                </a>
            </div>
        </article>
    HTML;
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