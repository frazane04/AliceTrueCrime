<?php
// src/struct/logout.php

// Include utils.php per session_start e getPrefix()
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/funzioni_db.php';

// Rimuovi il remember_token dal database prima di cancellare la sessione
if (isset($_SESSION['user_email'])) {
    $dbLogout = new FunzioniDB();
    $dbLogout->rimuoviRememberToken($_SESSION['user_email']);
}

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

// Elimina i cookie "Ricordami" se presenti
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/', '', true, true);
}

// Distruggi la sessione
session_destroy();

// Redirect alla home
redirect('/');
?>