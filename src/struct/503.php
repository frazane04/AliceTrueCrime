<?php
// Imposta il codice di risposta HTTP 503
http_response_code(503);

// Carica il template HTML della pagina 503
$templatePath = __DIR__ . '/../template/503.html';

if (!file_exists($templatePath)) {
    die("Errore: Template 503.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Ottieni il prefix per i link
$prefix = getPrefix();

// Sostituisci il placeholder del link home
$contenuto = str_replace('{{PREFIX}}', $prefix, $contenuto);

// Output finale tramite utils.php
echo getTemplatePage("503 - Servizio Non Disponibile | AliceTrueCrime", $contenuto);
?>