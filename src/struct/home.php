<?php
// src/struct/home.php
// Questo file gestisce la logica di visualizzazione della Home Page

// 1. Definisco il percorso del file HTML (Template)
// __DIR__ è la cartella corrente (src/struct), quindi scendo di un livello (..) e vado in template
$templatePath = __DIR__ . '/../template/index.html';

// 2. Controllo di sicurezza: Il file esiste?
if (file_exists($templatePath)) {
    // Se esiste, ne leggo tutto il contenuto HTML
    $contenuto = file_get_contents($templatePath);
} else {
    // Se non esiste, mostro un messaggio di errore (utile per il debug)
    $contenuto = "
        <div style='padding: 2rem; text-align: center; color: red;'>
            <h1>Errore Critico</h1>
            <p>Impossibile trovare il template della Home Page.</p>
            <p>Percorso cercato: <code>$templatePath</code></p>
        </div>
    ";
}

// 3. Definisco il Titolo della Pagina (che apparirà nella scheda del browser)
$titoloPagina = "Home - AliceTrueCrime";

// 4. Chiamo la funzione 'getTemplatePage' (definita in utils.php)
// Questa funzione prende il titolo e il contenuto centrale, e ci costruisce intorno Header e Footer.
echo getTemplatePage($titoloPagina, $contenuto);

?>