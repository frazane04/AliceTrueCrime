<?php
// Imposta il codice di risposta HTTP 403
http_response_code(403);

// Carica il template HTML della pagina 403
$templatePath = __DIR__ . '/../template/403.html';

if (!file_exists($templatePath)) {
    die("Errore: Template 403.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Ottieni il prefix per i link
$prefix = getPrefix();

// Sostituisci il placeholder del link home
$contenuto = str_replace('{{PREFIX}}', $prefix, $contenuto);

// Output finale tramite utils.php
echo getTemplatePage("403 - Accesso Negato | AliceTrueCrime", $contenuto);
?>