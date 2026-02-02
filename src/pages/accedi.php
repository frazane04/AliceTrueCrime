<?php
// src/struct/accedi.php
// Gestione Login

require_once __DIR__ . '/../db/funzioni_db.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    redirect('/profilo');
}

$email = '';
$messaggioHTML = '';

// ========================================
// GESTIONE FORM POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificaCsrfToken()) {
        $messaggioHTML = alertHtml('error', 'Token di sicurezza non valido. Ricarica la pagina e riprova.');
    } else {
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

                    $dbFunctions->salvaRememberToken($result['user']['email'], $rememberToken);

                    setcookie('remember_token', $rememberToken, $cookieExpiry, '/', '', true, true);
                    setcookie('user_email', $result['user']['email'], $cookieExpiry, '/', '', true, true);
                }

                rigeneraCsrfToken();
                redirect('/profilo');
            } else {
                $messaggioHTML = alertHtml('error', $result['message']);
            }
        }
    }
}


$contenuto = loadTemplate('accedi');

$contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);
$contenuto = str_replace('{{CSRF_TOKEN}}', csrfField(), $contenuto);
$contenuto = str_replace('{{EMAIL_VALUE}}', htmlspecialchars($email, ENT_QUOTES), $contenuto);
$contenuto = str_replace('<!-- PLACEHOLDER_MESSAGGI -->', $messaggioHTML, $contenuto);

echo getTemplatePage("Accedi - AliceTrueCrime", $contenuto, "Accedi al tuo profilo su AliceTrueCrime per commentare e segnalare nuovi casi.");
