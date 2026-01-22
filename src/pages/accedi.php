<?php
// src/struct/accedi.php
// Gestione Login

require_once __DIR__ . '/../db/funzioni_db.php';

// Se l'utente è già loggato, redirect al profilo
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirect('/profilo');
}

$email = '';
$messaggioHTML = '';

// ========================================
// GESTIONE FORM POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (empty($emailOrUsername) || empty($password)) {
        $messaggioHTML = alertHtml('error', 'Email e password sono obbligatori.');
    } else {
        $dbFunctions = new FunzioniDB();
        $result = $dbFunctions->loginUtente($emailOrUsername, $password);

        if ($result['success']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $result['user']['username'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['is_admin'] = $result['user']['is_admin'];

            if ($remember) {
                $cookieExpiry = time() + (30 * 24 * 60 * 60);
                $rememberToken = bin2hex(random_bytes(32));
                setcookie('remember_token', $rememberToken, $cookieExpiry, '/', '', true, true);
                setcookie('user_email', $result['user']['email'], $cookieExpiry, '/', '', true, true);
            }

            redirect('/profilo');
        } else {
            $messaggioHTML = alertHtml('error', $result['message']);
        }
    }
}

// ========================================
// CARICAMENTO E SOSTITUZIONE TEMPLATE
// ========================================
$contenuto = loadTemplate('accedi');

$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);
$contenuto = str_replace('{{EMAIL_VALUE}}', htmlspecialchars($email, ENT_QUOTES), $contenuto);
$contenuto = str_replace('<!-- PLACEHOLDER_MESSAGGI -->', $messaggioHTML, $contenuto);

echo getTemplatePage("Accedi - AliceTrueCrime", $contenuto);
