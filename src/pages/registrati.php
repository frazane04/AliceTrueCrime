<?php
// Registrazione utente

require_once __DIR__ . '/../db/funzioni_db.php';

$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificaCsrfToken()) {
        $errorMessage = 'Token di sicurezza non valido. Ricarica la pagina e riprova.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $newsletterOptIn = isset($_POST['newsletter']);

        if (empty($email) || empty($username) || empty($password) || empty($passwordConfirm)) {
            $errorMessage = 'Per favore, compila tutti i campi obbligatori.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Inserisci un\'email valida.';
        } elseif (strlen($username) < 3 || strlen($username) > 30) {
            $errorMessage = 'Lo username deve essere tra 3 e 30 caratteri.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errorMessage = 'Lo username può contenere solo lettere, numeri e underscore.';
        } elseif (strlen($password) < 8) {
            $errorMessage = 'La password deve contenere almeno 8 caratteri.';
        } elseif ($password !== $passwordConfirm) {
            $errorMessage = 'Le password non coincidono.';
        } else {
            $hasUpperCase = preg_match('/[A-Z]/', $password);
            $hasLowerCase = preg_match('/[a-z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);

            if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
                $errorMessage = 'La password deve contenere almeno una maiuscola, una minuscola e un numero.';
            } else {
                $dbFunctions = new FunzioniDB();

                if ($dbFunctions->verificaUsernameEsistente($username)) {
                    $errorMessage = 'Questo username è già in uso. Scegline un altro.';
                } else {
                    $registrationResult = $dbFunctions->registraUtente($email, $username, $password, false, $newsletterOptIn);

                    if ($registrationResult['success']) {

                        rigeneraCsrfToken();
                        $successMessage = 'Registrazione completata con successo! Verrai reindirizzato alla pagina di login...';

                        header('Refresh: 2; url=' . getPrefix() . '/accedi');
                    } else {
                        $errorMessage = $registrationResult['message'];
                    }
                }
            }
        }
    }
}

$contenuto = loadTemplate('registrati');

$contenuto = str_replace('{{CSRF_TOKEN}}', csrfField(), $contenuto);


if (!empty($errorMessage)) {
    $contenuto = str_replace('<form class="auth-form"', alertHtml('error', $errorMessage) . '<form class="auth-form"', $contenuto);
}

if (!empty($successMessage)) {
    $contenuto = str_replace('<form class="auth-form"', alertHtml('success', $successMessage) . '<form class="auth-form"', $contenuto);
}

echo getTemplatePage("Registrati - AliceTrueCrime", $contenuto, "Unisciti alla community di AliceTrueCrime. Registrati per aprire nuovi fascicoli e partecipare alle discussioni.");
?>