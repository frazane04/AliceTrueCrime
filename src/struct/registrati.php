<?php
// src/struct/registrati.php

require_once __DIR__ . '/funzioni_db.php';

$errorMessage = '';
$successMessage = '';

// Gestione POST del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $termsAccepted = isset($_POST['terms']);
    $newsletterOptIn = isset($_POST['newsletter']); // Al momento non gestito nel DB
    
    // Validazione
    if (empty($username) || empty($password) || empty($passwordConfirm)) {
        $errorMessage = 'Per favore, compila tutti i campi obbligatori.';
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
        // Verifica robustezza password (opzionale ma consigliato)
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        
        if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
            $errorMessage = 'La password deve contenere almeno una maiuscola, una minuscola e un numero.';
        } else {
            // Registrazione nel database
            $dbFunctions = new FunzioniDB();
            $registrationResult = $dbFunctions->registraUtente($username, $password, false);
            
            if ($registrationResult['success']) {
                // Registrazione completata
                $successMessage = 'Registrazione completata con successo! Verrai reindirizzato alla pagina di login...';
                
                // TODO: Se gestisci la newsletter, salvala in una tabella apposita
                // if ($newsletterOptIn) {
                //     $dbFunctions->iscriviNewsletter($registrationResult['user_id'], $email);
                // }
                
                // Redirect dopo 2 secondi
                header('Refresh: 2; url=' . getPrefix() . '/accedi');
            } else {
                // Registrazione fallita
                $errorMessage = $registrationResult['message'];
            }
        }
    }
}

// Carica il template HTML
$templatePath = __DIR__ . '/../template/registrati.html';

if (!file_exists($templatePath)) {
    die("Errore: Template registrati.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Rimuovi il campo email dal form (non presente nel DB)
// Se vuoi mantenerlo, devi modificare il database
$contenuto = preg_replace(
    '/<div class="form-group">.*?<label for="email">Email<\/label>.*?<\/div>/s',
    '',
    $contenuto
);

// Mostra eventuali messaggi di errore/successo
if (!empty($errorMessage)) {
    $alert = '<div class="alert alert-error" role="alert">' . htmlspecialchars($errorMessage) . '</div>';
    $contenuto = str_replace('<form class="auth-form"', $alert . '<form class="auth-form"', $contenuto);
}

if (!empty($successMessage)) {
    $alert = '<div class="alert alert-success" role="alert">' . htmlspecialchars($successMessage) . '</div>';
    $contenuto = str_replace('<form class="auth-form"', $alert . '<form class="auth-form"', $contenuto);
}

// Output finale
echo getTemplatePage("Registrati - AliceTrueCrime", $contenuto);
?>