<?php
// src/struct/caso.php

require_once __DIR__ . '/funzioni_db.php';

// Recupero ID del caso dalla query string
$casoId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Inizializzo variabili
$templatePath = __DIR__ . '/../template/caso.html';
$prefix = getPrefix();

// Verifico che l'ID sia valido
if ($casoId <= 0) {
    http_response_code(400);
    
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>⚠️ ID Caso Non Valido</h1>
            <p>Il caso richiesto non è stato specificato correttamente.</p>
            <a href='$prefix/esplora' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                Esplora tutti i Casi
            </a>
        </div>
    ";
    
    echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
    exit;
}

// Recupero i dati del caso dal database
$dbFunctions = new FunzioniDB();
$caso = $dbFunctions->getCasoById($casoId);
$colpevoli = $dbFunctions->getColpevoliByCaso($casoId);
$vittime=$dbFunctions->getVittimeByCaso($casoId);
// Verifico se il caso esiste
if (!$caso) {
    http_response_code(504);
    
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>🔍 Caso Non Trovato</h1>
            <p>Il caso richiesto (ID: $casoId) non esiste o non è stato ancora approvato.</p>
            <a href='$prefix/esplora' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                Esplora tutti i Casi
            </a>
        </div>
    ";
    
    echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
    exit;
}

// Carico il template HTML
if (!file_exists($templatePath)) {
    die("Errore: Template caso.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

$html_colpevoli = ""; // Variabile vuota inizialmente

foreach ($colpevoli as $colpevole) {
    // Aggiungo (concateno) ogni card alla variabile $html_colpevoli
    // NOTA: Qui definisco l'HTML della SINGOLA card
    $nome_colpevole=htmlspecialchars($colpevole['Nome']);
    $cognome_colpevole=htmlspecialchars($colpevole['Cognome']);
    $luogoNascita_colpevole=htmlspecialchars($colpevole['LuogoNascita']);
    $dataNascita_colpevole = !empty($colpevole['DataNascita']) 
        ? date('d/m/Y', strtotime($colpevole['DataNascita'])) 
        : 'Sconosciuta';    
    $imgColpevole=!empty($colpevole['Immagine']) 
        ? $prefix . '/' . htmlspecialchars($colpevole['Immagine'])
        : $prefix . '/assets/img/caso-placeholder.jpeg';


    $html_colpevoli .= '
    <div class="card-personaggio">
        <div class="card-foto">
            <img src="' . $imgColpevole . '" alt="' . $nome_colpevole." " .$cognome_colpevole. '">
        </div>
        <div class="card-info">
            <h4>' . $nome_colpevole." " .$cognome_colpevole . '</h4>
            <p><strong>Nato a:</strong> ' . $luogoNascita_colpevole. '</p>
            <p><strong>Il:</strong> ' . $dataNascita_colpevole . '</p>
        </div>
    </div>';
}

$html_vittime = ""; // Variabile vuota inizialmente

foreach ($vittime as $vittima) {
    
    // Preparazione variabili (Sanitizzazione)
    $nome_vittima = htmlspecialchars($vittima['Nome']);
    $cognome_vittima = htmlspecialchars($vittima['Cognome']);
    $luogoNascita_vittima = htmlspecialchars($vittima['LuogoNascita']);
    $dataNascita_vittima = !empty($vittima['DataNascita']) 
        ? date('d/m/Y', strtotime($vittima['DataNascita'])) 
        : 'Sconosciuta';
    $dataDecesso_vittima = !empty($vittima['DataNascita']) 
        ? date('d/m/Y', strtotime($vittima['DataDecesso'])) 
        : 'Sconosciuta';
    // Gestione Immagine
    $imgVittima = !empty($vittima['Immagine']) 
        ? $prefix . '/' . htmlspecialchars($vittima['Immagine'])
        : $prefix . '/assets/img/caso-placeholder.jpeg';

    // Costruzione HTML (Concatenazione)
    $html_vittime .= '
    <div class="card-personaggio">
        <div class="card-foto">
            <img src="' . $imgVittima . '" alt="' . $nome_vittima . " " . $cognome_vittima . '">
        </div>
        <div class="card-info">
            <h4>' . $nome_vittima . " " . $cognome_vittima . '</h4>
            <p><strong>Nato a:</strong> ' . $luogoNascita_vittima . '</p>
            <p><strong>Il:</strong> ' . $dataNascita_vittima . '</p>
            <p><strong>Il:</strong> ' . $dataDecesso_vittima . '</p>
        </div>
    </div>';
}

// Preparo i dati per la visualizzazione
$titolo = htmlspecialchars($caso['Titolo']);
$descrizione = nl2br(htmlspecialchars($caso['Descrizione']));
$data = date('d/m/Y', strtotime($caso['Data']));
$luogo = htmlspecialchars($caso['Luogo']);
$tipologia = htmlspecialchars($caso['Tipologia'] ?? 'Non specificata');

// Gestione immagine
$immagine = !empty($caso['Immagine']) 
    ? $prefix . '/' . htmlspecialchars($caso['Immagine'])
    : $prefix . '/assets/img/caso-placeholder.jpeg';

// Badge status
$statusClass = $caso['Approvato'] ? 'status-approved' : 'status-pending';
$statusText = $caso['Approvato'] ? '✓ Caso Verificato' : '⏳ In Revisione';

// Sostituisco i placeholder con i dati reali

// Immagine
$htmlImmagine = '<img alt="Evidenza principale del caso ' . $titolo . '" src="' . $immagine . '" class="img-evidence" width="300" />';
$contenuto = str_replace('<!-- caso_immagine -->', $htmlImmagine, $contenuto);

// Status badge
$htmlStatus = '<p class="status-badge ' . $statusClass . '">' . $statusText . '</p>';
$contenuto = str_replace('<!-- caso_status -->', $htmlStatus, $contenuto);

// Titolo
$htmlTitolo = '<h1>' . $titolo . '</h1>';
$contenuto = str_replace('<!-- caso_titolo -->', $htmlTitolo, $contenuto);

// Tipologia
$htmlTipologia = '<p class="italic">Categoria: ' . $tipologia . '</p>';
$contenuto = str_replace('<!-- [caso_tipologia -->', $htmlTipologia, $contenuto);

// Descrizione
$contenuto = str_replace('<!-- caso_descrizione -->', $descrizione, $contenuto);

// Data
$contenuto = str_replace('<!-- caso_data -->', $data, $contenuto);

// Luogo
$contenuto = str_replace('<!-- caso_luogo -->', $luogo, $contenuto);

// Vittime (da implementare)
$contenuto = str_replace('<!-- caso_vittime -->', $html_vittime, $contenuto);

// Sospettato (da implementare)
$contenuto = str_replace('<!-- caso_colpevoli -->', $html_colpevoli, $contenuto);

// Timeline (da implementare)
$htmlTimeline = '<p style="color: #666; font-style: italic;">Timeline non ancora disponibile per questo caso.</p>';
$contenuto = str_replace('<!-- caso_timeline -->', $htmlTimeline, $contenuto);

// Output finale
$titoloPagina = $titolo . " - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>