<?php
// src/struct/registrati.php

require_once __DIR__ . '/funzioni_db.php';

$errorMessage = '';
$successMessage = '';
$debugInfo = ''; // Per debug

// Gestione POST del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    $termsAccepted = isset($_POST['terms']);
    $newsletterOptIn = isset($_POST['newsletter']);
    
    // DEBUG: Log dei dati ricevuti
    error_log("=== TENTATIVO REGISTRAZIONE ===");
    error_log("Username: " . $username);
    error_log("Password length: " . strlen($password));
    error_log("Terms accepted: " . ($termsAccepted ? 'SI' : 'NO'));
    
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
        // Verifica robustezza password
        $hasUpperCase = preg_match('/[A-Z]/', $password);
        $hasLowerCase = preg_match('/[a-z]/', $password);
        $hasNumber = preg_match('/[0-9]/', $password);
        
        if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
            $errorMessage = 'La password deve contenere almeno una maiuscola, una minuscola e un numero.';
        } else {
            // Registrazione nel database
            try {
                $dbFunctions = new FunzioniDB();
                $registrationResult = $dbFunctions->registraUtente($username, $password, false);
                
                // DEBUG: Log del risultato
                error_log("Risultato registrazione: " . json_encode($registrationResult));
                
                if ($registrationResult['success']) {
                    // Registrazione completata
                    $successMessage = 'Registrazione completata con successo! Verrai reindirizzato alla pagina di login...';
                    error_log("Registrazione completata con successo per: " . $username);
                    
                    // Redirect dopo 2 secondi
                    header('Refresh: 2; url=' . getPrefix() . '/accedi');
                } else {
                    // Registrazione fallita - Mostra il messaggio specifico
                    $errorMessage = $registrationResult['message'];
                    error_log("Registrazione fallita: " . $registrationResult['message']);
                    
                    // In modalità sviluppo, mostra più dettagli
                    if (ini_get('display_errors')) {
                        $debugInfo = "Debug: " . $registrationResult['message'];
                    }
                }
            } catch (Exception $e) {
                $errorMessage = 'Errore critico durante la registrazione.';
                error_log("EXCEPTION in registrazione: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
                
                if (ini_get('display_errors')) {
                    $debugInfo = "Debug: " . $e->getMessage();
                }
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

// Rimuovi il campo email dal form
$contenuto = preg_replace(
    '/<div class="form-group">\s*<label for="email">Email<\/label>.*?<\/div>/s',
    '',
    $contenuto
);

// Mostra eventuali messaggi di errore/successo
if (!empty($errorMessage)) {
    $alert = '<div class="alert alert-error" role="alert">' . htmlspecialchars($errorMessage);
    if (!empty($debugInfo)) {
        $alert .= '<br><small style="color: #666;">' . htmlspecialchars($debugInfo) . '</small>';
    }
    $alert .= '</div>';
    $contenuto = str_replace('<form class="auth-form"', $alert . '<form class="auth-form"', $contenuto);
}

if (!empty($successMessage)) {
    $alert = '<div class="alert alert-success" role="alert">' . htmlspecialchars($successMessage) . '</div>';
    $contenuto = str_replace('<form class="auth-form"', $alert . '<form class="auth-form"', $contenuto);
}

// Output finale
echo getTemplatePage("Registrati - AliceTrueCrime", $contenuto);
?>