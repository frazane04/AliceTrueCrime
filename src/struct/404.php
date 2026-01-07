<?php
// Imposta il codice di risposta HTTP 404
http_response_code(404);

// Carica il template HTML della pagina 404
$templatePath = __DIR__ . '/../template/404.html';

if (!file_exists($templatePath)) {
    die("Errore: Template 404.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Ottieni il prefix per i link
$prefix = getPrefix();

// Sostituisci il placeholder del link home
$contenuto = str_replace('{{PREFIX}}', $prefix, $contenuto);

// Output finale tramite utils.php
echo getTemplatePage("404 - Caso Archiviato | AliceTrueCrime", $contenuto);
?>