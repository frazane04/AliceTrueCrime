<?php
// src/struct/registrati.php

require_once __DIR__ . '/../db/funzioni_db.php';

$errorMessage = '';
$successMessage = '';

// Gestione POST del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica token CSRF
    if (!verificaCsrfToken()) {
        $errorMessage = 'Token di sicurezza non valido. Ricarica la pagina e riprova.';
    } else {
        $email = trim($_POST['email'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $termsAccepted = isset($_POST['terms']);
        $newsletterOptIn = isset($_POST['newsletter']);

        // Validazione
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
        } elseif (!$termsAccepted) {
            $errorMessage = 'Devi accettare i Termini di Servizio per registrarti.';
        } else {
            // Verifica robustezza password
            $hasUpperCase = preg_match('/[A-Z]/', $password);
            $hasLowerCase = preg_match('/[a-z]/', $password);
            $hasNumber = preg_match('/[0-9]/', $password);

            if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
                $errorMessage = 'La password deve contenere almeno una maiuscola, una minuscola e un numero.';
            } else {
                // Registrazione nel database
                $dbFunctions = new FunzioniDB();

                // Verifica se lo username esiste già
                if ($dbFunctions->verificaUsernameEsistente($username)) {
                    $errorMessage = 'Questo username è già in uso. Scegline un altro.';
                } else {
                    $registrationResult = $dbFunctions->registraUtente($email, $username, $password, false);

                    if ($registrationResult['success']) {
                        // Registrazione completata - rigenera CSRF token
                        rigeneraCsrfToken();
                        $successMessage = 'Registrazione completata con successo! Verrai reindirizzato alla pagina di login...';

                        // Redirect dopo 2 secondi
                        header('Refresh: 2; url=' . getPrefix() . '/accedi');
                    } else {
                        // Registrazione fallita
                        $errorMessage = $registrationResult['message'];
                    }
                }
            }
        }
    }
}

// Carica il template HTML
$contenuto = loadTemplate('registrati');

// Inserisci token CSRF
$contenuto = str_replace('{{CSRF_TOKEN}}', csrfField(), $contenuto);

// Mostra eventuali messaggi di errore/successo
if (!empty($errorMessage)) {
    $contenuto = str_replace('<form class="auth-form"', alertHtml('error', $errorMessage) . '<form class="auth-form"', $contenuto);
}

if (!empty($successMessage)) {
    $contenuto = str_replace('<form class="auth-form"', alertHtml('success', $successMessage) . '<form class="auth-form"', $contenuto);
}

// Output finale
echo getTemplatePage("Registrati - AliceTrueCrime", $contenuto);
?>