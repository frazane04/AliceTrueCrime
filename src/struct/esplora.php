<?php
// src/struct/esplora.php
require_once __DIR__ . '/funzioni_db.php';


// 1. Carica il template HTML di base
$templatePath = __DIR__ . '/../template/esplora.html'; // Assicurati che la cartella sia templates (plurale)

if (!file_exists($templatePath)) {
    die("Errore: Template esplora.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// 2. Simulazione dati dal Database (Array di oggetti)
// In futuro qui farai: $casi = $db->getCasesByCategory('serial_killer');

$dbFunctions = new FunzioniDB();

$casiPiuLetti = $dbFunctions->getCasiPiuLetti(3); // Top 3 più letti
$serialKillers = $dbFunctions->getCasiPerCategoria('Serial killer', 6);
$amoreTossico = $dbFunctions->getCasiPerCategoria('Amore tossico', 6);
$celebrity = $dbFunctions->getCasiPerCategoria('Celebrity', 6);
$casiItaliani = $dbFunctions->getCasiPerCategoria('Casi mediatici italiani', 6);


// ... Puoi creare altri array per le altre categorie ...

// 3. Funzione Helper per generare l'HTML delle Card
// Rispetta la sintassi XML (chiusura tag img />)
function generaHtmlCards($listaCasi) {
    if (empty($listaCasi)) {
        return '<p class="no-results">Nessun caso trovato in questa categoria.</p>';
    }
    $html = '';
    $prefix = getPrefix(); // Funzione presa da utils.php
    foreach ($listaCasi as $caso) {

        $imgSrc = !empty($caso['Immagine']) 
            ? $prefix . '/' . htmlspecialchars($caso['Immagine'])
            : $prefix . '/assets/img/caso-placeholder.jpeg';
        
        $titolo = htmlspecialchars($caso['Titolo']);
        $descrizione = htmlspecialchars(substr($caso['Descrizione'], 0, 120)) . '...';
        $nCaso = (int)$caso['N_Caso'];
        $tipologia = htmlspecialchars($caso['Tipologia']);

        $html .= <<<HTML
        <article class="case-card" data-categoria="{$tipologia}">
            <div class="card-image">
                <img src="{$imgSrc}" alt="Copertina caso {$titolo}" loading="lazy" />
            </div>
            <div class="card-content">
                <h3>{$titolo}</h3>
                <p>{$descrizione}</p>
                <a href="{$prefix}/caso?id={$nCaso}" class="btn-card" aria-label="Leggi approfondimento su {$titolo}">
                    Leggi Caso
                </a>
            </div>
        </article>
    HTML;
    }
    
    return $html;
}

// 4. Generazione dell'HTML per ogni sezione
$htmlPiuLetti     = generaHtmlCards($casiPiuLetti);
$htmlSerialKiller = generaHtmlCards($serialKillers);
$htmlAmoreTossico = generaHtmlCards($amoreTossico); // Esempio vuoto
$htmlCelebrity    = generaHtmlCards($celebrity); // Esempio vuoto
$htmlCasiItaliani = generaHtmlCards($casiItaliani); // Esempio vuoto

// 5. Sostituzione dei Placeholder nel template
$contenuto = str_replace('{{GRID_PIU_LETTI}}', $htmlPiuLetti, $contenuto);
$contenuto = str_replace('{{GRID_SERIAL_KILLER}}', $htmlSerialKiller, $contenuto);
$contenuto = str_replace('{{GRID_AMORE_TOSSICO}}', $htmlAmoreTossico, $contenuto);
$contenuto = str_replace('{{GRID_CELEBRITY}}', $htmlCelebrity, $contenuto);
$contenuto = str_replace('{{GRID_CASI_ITALIANI}}', $htmlCasiItaliani, $contenuto);

// 6. Output finale tramite utils.php
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto);
?>