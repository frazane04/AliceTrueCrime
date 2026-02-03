<?php
// Logout utente

require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/funzioni_db.php';

if (isset($_SESSION['user_email'])) {
    $dbLogout = new FunzioniDB();
    $dbLogout->rimuoviRememberToken($_SESSION['user_email']);
}

$_SESSION = [];

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

if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', true, true);
}
if (isset($_COOKIE['user_email'])) {
    setcookie('user_email', '', time() - 3600, '/', '', true, true);
}

session_destroy();

redirect('/');
?>