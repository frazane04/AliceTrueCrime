<?php
// src/struct/accedi.php

require_once __DIR__ . '/funzioni_db.php';

$errorMessage = '';
$successMessage = '';

// Gestione POST del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Validazione base
    if (empty($username) || empty($password)) {
        $errorMessage = 'Per favore, compila tutti i campi.';
    } else {
        // Connessione al database e verifica credenziali
        $dbFunctions = new FunzioniDB();
        $loginResult = $dbFunctions->loginUtente($username, $password);
        
        if ($loginResult['success']) {
            // Login riuscito - imposta la sessione
            $_SESSION['user'] = $loginResult['user']['username'];
            $_SESSION['user_id'] = $loginResult['user']['id'];
            $_SESSION['is_admin'] = $loginResult['user']['is_admin'];
            
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
                        'secure' => isset($_SERVER['HTTPS']), // Solo HTTPS se disponibile
                        'httponly' => true, // Non accessibile via JavaScript
                        'samesite' => 'Strict' // Protezione CSRF
                    ]
                );
            }
            
            // Redirect alla pagina profilo
            header('Location: ' . getPrefix() . '/profilo');
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

// Modifica il campo email in username nel template
$contenuto = str_replace('name="email"', 'name="username"', $contenuto);
$contenuto = str_replace('id="email"', 'id="username"', $contenuto);
$contenuto = str_replace('type="email"', 'type="text"', $contenuto);
$contenuto = str_replace('<label for="email">Email</label>', '<label for="username">Username</label>', $contenuto);
$contenuto = str_replace('placeholder="detective@example.com"', 'placeholder="detective_007"', $contenuto);
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