<?php
// src/struct/logout.php

// Include utils.php per session_start e getPrefix()
require_once __DIR__ . '/utils.php';

// Distruggi tutte le variabili di sessione
$_SESSION = [];

// Elimina il cookie di sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// Elimina il cookie "Ricordami" se presente
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Distruggi la sessione
session_destroy();

// Redirect alla home
header('Location: ' . getPrefix() . '/');
exit;
?>