<?php
// src/struct/accedi.php

require_once __DIR__ . '/funzioni_db.php';

$errorMessage = '';
$successMessage = '';

// Gestione POST del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validazione base
    if (empty($emailOrUsername) || empty($password)) {
        $errorMessage = 'Per favore, compila tutti i campi.';
    } else {
        // Connessione al database e verifica credenziali
        $dbFunctions = new FunzioniDB();
        
        // Determina se l'input è un'email o uno username
        if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
            // È un'email
            $loginResult = $dbFunctions->loginUtenteEmail($emailOrUsername, $password);
        } else {
            // È uno username
            $loginResult = $dbFunctions->loginUtenteUsername($emailOrUsername, $password);
        }
        
        if ($loginResult['success']) {
            // Login riuscito - imposta la sessione
            $_SESSION['user'] = $loginResult['user']['username'];
            $_SESSION['user_email'] = $loginResult['user']['email'];
            $_SESSION['is_admin'] = $loginResult['user']['is_admin'];
            $_SESSION['logged_in'] = true;
            
            // Se "Ricordami" è attivo, imposta cookie sicuro
            if ($remember) {
                // Genera un token casuale sicuro
                $token = bin2hex(random_bytes(32));
                
                // TODO: Salvare il token nel database associato all'utente
                // per poterlo validare in futuro
                
                // Imposta cookie per 30 giorni con flag sicuri
                setcookie(
                    'remember_token', 
                    $token, 
                    [
                        'expires' => time() + (86400 * 30),
                        'path' => '/',
                        'secure' => isset($_SERVER['HTTPS']),
                        'httponly' => true,
                        'samesite' => 'Strict'
                    ]
                );
            }
            
            // Redirect alla pagina profilo
            header('Location: ' . getPrefix() . '/pagineutente.html');
            exit;
        } else {
            // Login fallito
            $errorMessage = $loginResult['message'];
        }
    }
}

// Carica il template HTML
$templatePath = __DIR__ . '/../template/accedi.html';

if (!file_exists($templatePath)) {
    die("Errore: Template accedi.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// Modifica il campo per accettare sia email che username
$contenuto = str_replace('type="email"', 'type="text"', $contenuto);
$contenuto = str_replace('<label for="email">Email</label>', '<label for="email">Email o Username</label>', $contenuto);
$contenuto = str_replace('placeholder="detective@example.com"', 'placeholder="email@example.com o AdolfoBallan"', $contenuto);
$contenuto = str_replace('autocomplete="email"', 'autocomplete="username"', $contenuto);

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
echo getTemplatePage("Accedi - AliceTrueCrime", $contenuto);
?>