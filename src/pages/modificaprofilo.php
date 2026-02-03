<?php
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/funzioni_db.php';

requireAuth(); //

$db = new FunzioniDB();
$emailAttuale = $_SESSION['user_email'];
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificaCsrfToken()) { //
        $errorMessage = 'Token di sicurezza non valido.';
    } else {
        $nuovaEmail = trim($_POST['email'] ?? '');
        $nuovaPass = $_POST['new_password'] ?? '';
        $confermaPass = $_POST['confirm_password'] ?? '';
        $passAttuale = $_POST['current_password'] ?? '';

        // 1. Verifica password attuale per autorizzare la modifica
        $loginCheck = $db->loginUtente($emailAttuale, $passAttuale);
        
        if (!$loginCheck['success']) {
            $errorMessage = 'La password attuale non è corretta.';
        } 
        // 2. Validazione Email (stesso vincolo di registrazione)
        elseif (empty($nuovaEmail) || !filter_var($nuovaEmail, FILTER_VALIDATE_EMAIL)) {
            $errorMessage = 'Inserisci un\'email valida.';
        } 
        else {
            $passValid = true;
            // 3. Validazione Nuova Password (se inserita, applica vincoli registrazione)
            if (!empty($nuovaPass)) {
                if (strlen($nuovaPass) < 8) {
                    $errorMessage = 'La nuova password deve contenere almeno 8 caratteri.';
                    $passValid = false;
                } elseif ($nuovaPass !== $confermaPass) {
                    $errorMessage = 'Le nuove password non coincidono.';
                    $passValid = false;
                } else {
                    // Controlli complessità (da registrati.php)
                    $hasUpperCase = preg_match('/[A-Z]/', $nuovaPass);
                    $hasLowerCase = preg_match('/[a-z]/', $nuovaPass);
                    $hasNumber = preg_match('/[0-9]/', $nuovaPass);

                    if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
                        $errorMessage = 'La nuova password deve contenere almeno una maiuscola, una minuscola e un numero.';
                        $passValid = false;
                    }
                }
            }

            // 4. Esecuzione aggiornamento se tutto è valido
            if ($passValid) {
                if ($db->aggiornaProfilo($emailAttuale, $nuovaEmail, $nuovaPass)) {
                    $_SESSION['user_email'] = $nuovaEmail; // Aggiorna PK in sessione
                    $emailAttuale = $nuovaEmail;
                    $successMessage = 'Profilo aggiornato con successo!';
                    rigeneraCsrfToken(); //
                } else {
                    $errorMessage = 'Impossibile aggiornare: l\'email è già in uso.';
                }
            }
        }
    }
}

$utente = $db->getUtenteByEmail($emailAttuale);
$html = loadTemplate('modificaprofilo');

// Gestione messaggi nel template
$alertHtml = '';
if (!empty($errorMessage)) $alertHtml = alertHtml('error', $errorMessage);
if (!empty($successMessage)) $alertHtml = alertHtml('success', $successMessage);

$html = str_replace('{{MESSAGGIO_PROFILO}}', $alertHtml, $html);
$html = str_replace('{{CSRF_FIELD}}', csrfField(), $html);
$html = str_replace('{{USERNAME}}', htmlspecialchars($utente['Username']), $html);
$html = str_replace('{{EMAIL}}', htmlspecialchars($utente['Email']), $html);

echo getTemplatePage("Modifica Profilo - AliceTrueCrime", $html);