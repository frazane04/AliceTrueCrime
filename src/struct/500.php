<?php
// Imposta il codice di risposta HTTP 500
http_response_code(500);

// Carica il template HTML della pagina 500
$templatePath = __DIR__ . '/../template/500.html';

if (!file_exists($templatePath)) {
    die("Errore: Template 500.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Ottieni il prefix per i link
$prefix = getPrefix();

// Sostituisci il placeholder del link home
$contenuto = str_replace('{{PREFIX}}', $prefix, $contenuto);

// Output finale tramite utils.php
echo getTemplatePage("500 - Errore Server | AliceTrueCrime", $contenuto);
?>