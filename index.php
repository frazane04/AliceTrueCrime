<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//richiamo di utils.php per la configurazione iniziale
require_once 'src/struct/utils.php';

// 1. Analisi URL
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

// 2. Gestione Prefisso
$prefix = getPrefix();
if ($prefix && strpos($path, $prefix) === 0) {
    $path = substr($path, strlen($prefix));
}

// 3. CONFIGURAZIONE DELLE ROTTE
// Qui definisci l'URL (chiave) e il nome del file PHP dentro src/struct/ (valore)
$routes = [
    '/'              => 'home.php',
    '/index.php'     => 'home.php',
    '/home'          => 'home.php',
    
    '/esplora'       => 'esplora.php',
    '/segnala-caso'  => 'segnala.php',
    '/newsletter'    => 'newsletter.php',
    
    '/accedi'        => 'accedi.php',
    '/registrati'    => 'registrati.php',
    '/profilo'       => 'profilo.php',
    '/logout'        => 'logout.php'
];

// 4. CONTROLLO E REINDIRIZZAMENTO
if (array_key_exists($path, $routes)) {
    // Se l'URL esiste nell'array, carichiamo il file corrispondente
    $fileToLoad = __DIR__ . '/src/struct/' . $routes[$path];
    
    // Controllo di sicurezza extra: il file esiste davvero?
    if (file_exists($fileToLoad)) {
        require $fileToLoad;
    } else {
        // Errore 500: Rotta definita ma file mancante
        die("Errore configurazione: Il file $fileToLoad non esiste.");
    }

} else {
    // 5. GESTIONE 404 (Pagina non trovata)
    http_response_code(404);
    
    // Puoi creare un file src/struct/404.php per gestirlo meglio, oppure stampare al volo:
    echo getTemplatePage(
        "404 Not Found", 
        "<div style='text-align:center; padding:5rem;'>
            <h1>Errore 404</h1>
            <p>La pagina che cerchi non esiste.</p>
            <a href='$prefix/' class='btn btn-primary'>Torna alla Home</a>
         </div>"
    );
}
?>