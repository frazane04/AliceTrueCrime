<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/helpers/utils.php';

$request = $_SERVER['REQUEST_URI'];
$path = parse_url($request, PHP_URL_PATH);

$prefix = getPrefix();
if ($prefix && strpos($path, $prefix) === 0) {
    $path = substr($path, strlen($prefix));
}

$path = rtrim($path, '/');
if (empty($path)) {
    $path = '/';
}

if (preg_match('#^/esplora/([a-z0-9\-]+)$#i', $path, $matches)) {
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

if (preg_match('#^/esplora/([a-z0-9\-]+)/modifica$#i', $path, $matches)) {
    $_GET['slug'] = $matches[1];

    $fileToLoad = __DIR__ . '/src/pages/modifica_caso.php';

    if (file_exists($fileToLoad)) {
        require $fileToLoad;
        exit;
    } else {
        http_response_code(500);
        die("Errore: modifica_caso.php non trovato");
    }
}

// ROTTE STATICHE
$routes = [
    '/' => 'home.php',
    '/index.php' => 'home.php',
    '/home' => 'home.php',

    '/esplora' => 'esplora.php',
    '/esplora-tutti' => 'esplora-tutti.php',
    '/segnala-caso' => 'segnala.php',
    '/about' => 'about.php',
    '/privacy' => 'privacy.php',

    '/accedi' => 'accedi.php',
    '/registrati' => 'registrati.php',
    '/profilo' => 'profilo.php',
    '/logout' => 'logout.php',
];

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