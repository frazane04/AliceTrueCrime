<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/helpers/utils.php';

//Analisi URL
$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

//Gestione Prefisso
$prefix = getPrefix();
if ($prefix && strpos($path, $prefix) === 0) {
    $path = substr($path, strlen($prefix));
}

$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

//ROTTE DINAMICHE
if (preg_match('#^/esplora/([a-z0-9\-]+)$#i', $path, $matches)) {
    // Cattura lo slug dall'URL
    $_GET['slug'] = $matches[1];

    $fileToLoad = __DIR__ . '/src/pages/caso.php';

    if (file_exists($fileToLoad)) {
        require $fileToLoad;
        exit;
    } else {
        http_response_code(500);
        die("Errore: caso.php non trovato");
    }
}

// ROTTE STATICHE
$routes = [
    '/' => 'home.php',
    '/index.php' => 'home.php',
    '/home' => 'home.php',

    '/esplora' => 'esplora.php',
    '/segnala-caso' => 'segnala.php',
    '/modifica-caso' => 'modifica_caso.php',
    '/chi-siamo' => 'chi_siamo.php',
    '/privacy' => 'privacy.php',

    '/accedi' => 'accedi.php',
    '/registrati' => 'registrati.php',
    '/profilo' => 'profilo.php',
    '/logout' => 'logout.php',
];

//CONTROLLO E REINDIRIZZAMENTO
if (array_key_exists($path, $routes)) {
    $fileToLoad = __DIR__ . '/src/pages/' . $routes[$path];

    if (file_exists($fileToLoad)) {
        require $fileToLoad;
    } else {
        renderErrorPage(500);
    }
} else {
    renderErrorPage(404);
}
?>