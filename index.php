<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Richiamo di utils.php per la configurazione iniziale
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
    '/logout'        => 'logout.php',

    
    '/404'           => '404.php',
    '/403'           => '403.php',
    '/500'           => '500.php',
    '/503'           => '503.php'
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
        http_response_code(500);
        $error500File = __DIR__ . '/src/struct/500.php';
        if (file_exists($error500File)) {
            require $error500File;
        } else {
            die("Errore critico: Il file $fileToLoad non esiste e nemmeno la pagina 500.php");
        }
    }

} else {
    // 5. GESTIONE 404 (Pagina non trovata)
    http_response_code(404);
    
    $error404File = __DIR__ . '/src/struct/404.php';
    if (file_exists($error404File)) {
        require $error404File;
    } else {
        // Fallback se anche 404.php non esiste
        echo getTemplatePage(
            "404 Not Found", 
            "<div style='text-align:center; padding:5rem;'>
                <h1>Errore 404</h1>
                <p>La pagina che cerchi non esiste.</p>
                <a href='$prefix/' class='btn btn-primary'>Torna alla Home</a>
             </div>"
        );
    }
}
?>