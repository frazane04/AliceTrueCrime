<?php
// src/struct/accedi.php
// Gestione Login - Versione con template HTML separato

require_once __DIR__ . '/funzioni_db.php';

// Se l'utente è già loggato, redirect al profilo
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    $prefix = getPrefix();
    header("Location: $prefix/profilo");
    exit;
}

$templatePath = __DIR__ . '/../template/accedi.html';
$email = '';
$messaggioHTML = '';

// ========================================
// GESTIONE FORM POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $emailOrUsername = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    // Validazione base
    if (empty($emailOrUsername) || empty($password)) {
        $messaggioHTML = '
            <div class="alert alert-error">
                <strong>⚠️ Errore:</strong> Email e password sono obbligatori.
            </div>
        ';
    } else {
        // Tentativo di login
        $dbFunctions = new FunzioniDB();

        // Determina se l'input è un'email o uno username
        if (filter_var($emailOrUsername, FILTER_VALIDATE_EMAIL)) {
            // È un'email
            $result = $dbFunctions->loginUtenteEmail($emailOrUsername, $password);
        } else {
            // È uno username
            $result = $dbFunctions->loginUtenteUsername($emailOrUsername, $password);
        }
        
        if ($result['success']) {
            // Login riuscito
            $_SESSION['logged_in'] = true;
            $_SESSION['user'] = $result['user']['username'];
            $_SESSION['user_email'] = $result['user']['email'];
            $_SESSION['is_admin'] = $result['user']['is_admin'];
            
            // Gestione "Ricordami"
            if ($remember) {
                $cookieExpiry = time() + (30 * 24 * 60 * 60);
                $rememberToken = bin2hex(random_bytes(32));
                
                setcookie('remember_token', $rememberToken, $cookieExpiry, '/', '', true, true);
                setcookie('user_email', $result['user']['email'], $cookieExpiry, '/', '', true, true);
            }
            
            // Redirect al profilo
            $prefix = getPrefix();
            header("Location: $prefix/profilo");
            exit;
            
        } else {
            // Login fallito
            $messaggioHTML = '
                <div class="alert alert-error">
                    <strong>⚠️ Errore:</strong> ' . htmlspecialchars($result['message']) . '
                </div>
            ';
        }
    }
}

// ========================================
// CARICAMENTO E SOSTITUZIONE TEMPLATE
// ========================================
if (file_exists($templatePath)) {
    $contenuto = file_get_contents($templatePath);
} else {
    die("Errore: Template non trovato in $templatePath");
}

$prefix = getPrefix();

// Sostituzioni semplici e pulite
$contenuto = str_replace('{{PREFIX}}', $prefix, $contenuto);
$contenuto = str_replace('{{EMAIL_VALUE}}', htmlspecialchars($email, ENT_QUOTES), $contenuto);
$contenuto = str_replace('<!-- PLACEHOLDER_MESSAGGI -->', $messaggioHTML, $contenuto);

// ========================================
// OUTPUT FINALE
// ========================================
$titoloPagina = "Accedi - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>