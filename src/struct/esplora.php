<?php
// src/struct/esplora.php

// 1. Carica il template HTML di base
$templatePath = __DIR__ . '/../template/esplora.html'; // Assicurati che la cartella sia templates (plurale)

if (!file_exists($templatePath)) {
    die("Errore: Template esplora.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// 2. Simulazione dati dal Database (Array di oggetti)
// In futuro qui farai: $casi = $db->getCasesByCategory('serial_killer');

$casiPiuLetti = [
    ['titolo' => 'Il Mostro di Firenze', 'desc' => 'Otto duplici omicidi avvenuti fra il 1968 e il 1985.', 'img' => 'landing.svg'],
    ['titolo' => 'Il Delitto di Cogne', 'desc' => 'La tragica morte del piccolo Samuele e il caso mediatico.', 'img' => 'landing.svg'],
    ['titolo' => 'Emanuela Orlandi', 'desc' => 'Uno dei misteri vaticani più oscuri e irrisolti.', 'img' => 'landing.svg']
];

$serialKillers = [
    ['titolo' => 'Ted Bundy', 'desc' => 'Il carismatico killer che terrorizzò l\'America negli anni \'70.', 'img' => 'landing.svg'],
    ['titolo' => 'Jeffrey Dahmer', 'desc' => 'Il mostro di Milwaukee: storia di un cannibale.', 'img' => 'landing.svg']
];

// ... Puoi creare altri array per le altre categorie ...

// 3. Funzione Helper per generare l'HTML delle Card
// Rispetta la sintassi XML (chiusura tag img />)
function generaHtmlCards($listaCasi) {
    $html = '';
    $prefix = getPrefix(); // Funzione presa da utils.php
    
    foreach ($listaCasi as $caso) {
        $imgSrc = $prefix . '/assets/imgs/' . $caso['img'];
        
        $html .= <<<HTML
        <article class="case-card">
            <div class="card-image">
                <img src="{$imgSrc}" alt="Copertina caso {$caso['titolo']}" loading="lazy" />
            </div>
            <div class="card-content">
                <h3>{$caso['titolo']}</h3>
                <p>{$caso['desc']}</p>
                <a href="{$prefix}/caso" class="btn-card" aria-label="Leggi approfondimento su {$caso['titolo']}">Leggi Caso</a>
            </div>
        </article>
HTML;
    }
    
    if (empty($html)) {
        $html = '<p>Nessun caso trovato in questa categoria.</p>';
    }
    
    return $html;
}

// 4. Generazione dell'HTML per ogni sezione
$htmlPiuLetti     = generaHtmlCards($casiPiuLetti);
$htmlSerialKiller = generaHtmlCards($serialKillers);
$htmlAmoreTossico = generaHtmlCards([]); // Esempio vuoto
$htmlCelebrity    = generaHtmlCards([]); // Esempio vuoto
$htmlCasiItaliani = generaHtmlCards([]); // Esempio vuoto

// 5. Sostituzione dei Placeholder nel template
$contenuto = str_replace('{{GRID_PIU_LETTI}}', $htmlPiuLetti, $contenuto);
$contenuto = str_replace('{{GRID_SERIAL_KILLER}}', $htmlSerialKiller, $contenuto);
$contenuto = str_replace('{{GRID_AMORE_TOSSICO}}', $htmlAmoreTossico, $contenuto);
$contenuto = str_replace('{{GRID_CELEBRITY}}', $htmlCelebrity, $contenuto);
$contenuto = str_replace('{{GRID_CASI_ITALIANI}}', $htmlCasiItaliani, $contenuto);

// 6. Output finale tramite utils.php
echo getTemplatePage("Esplora - AliceTrueCrime", $contenuto);
?>