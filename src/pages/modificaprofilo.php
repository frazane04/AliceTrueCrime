<?php
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/funzioni_db.php';

requireAuth();

$db = new FunzioniDB();
$emailAttuale = $_SESSION['user_email'];
$usernameAttuale = $_SESSION['user'];
$errorMessage = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verificaCsrfToken()) {
        $errorMessage = 'Token di sicurezza non valido.';
    } else {
        $nuovoUsername = trim($_POST['username'] ?? '');
        $nuovaPass = $_POST['new_password'] ?? '';
        $confermaPass = $_POST['confirm_password'] ?? '';
        $passAttuale = $_POST['current_password'] ?? '';

        $loginCheck = $db->loginUtente($emailAttuale, $passAttuale);

        if (!$loginCheck['success']) {
            $errorMessage = 'La password attuale non è corretta.';
        }
        elseif (empty($nuovoUsername) || strlen($nuovoUsername) < 3 || strlen($nuovoUsername) > 30) {
            $errorMessage = 'L\'username deve essere tra 3 e 30 caratteri.';
        }
        elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $nuovoUsername)) {
            $errorMessage = 'L\'username può contenere solo lettere, numeri e underscore.';
        }
        elseif ($nuovoUsername !== $usernameAttuale && $db->verificaUsernameEsistente($nuovoUsername)) {
            $errorMessage = 'Questo username è già in uso.';
        }
        else {
            $passValid = true;
            if (!empty($nuovaPass)) {
                if (strlen($nuovaPass) < 8) {
                    $errorMessage = 'La nuova password deve contenere almeno 8 caratteri.';
                    $passValid = false;
                } elseif ($nuovaPass !== $confermaPass) {
                    $errorMessage = 'Le nuove password non coincidono.';
                    $passValid = false;
                } else {
                    $hasUpperCase = preg_match('/[A-Z]/', $nuovaPass);
                    $hasLowerCase = preg_match('/[a-z]/', $nuovaPass);
                    $hasNumber = preg_match('/[0-9]/', $nuovaPass);

                    if (!$hasUpperCase || !$hasLowerCase || !$hasNumber) {
                        $errorMessage = 'La nuova password deve contenere almeno una maiuscola, una minuscola e un numero.';
                        $passValid = false;
                    }
                }
            }

            if ($passValid) {
                if ($db->aggiornaProfilo($emailAttuale, $nuovoUsername, $nuovaPass)) {
                    $_SESSION['user'] = $nuovoUsername;
                    $usernameAttuale = $nuovoUsername;
                    $successMessage = 'Profilo aggiornato con successo!';
                    rigeneraCsrfToken();
                } else {
                    $errorMessage = 'Errore durante l\'aggiornamento del profilo.';
                }
            }
        }
    }
}

$utente = $db->getUtenteByEmail($emailAttuale);
$html = loadTemplate('modificaprofilo');

$alertHtml = '';
if (!empty($errorMessage)) $alertHtml = alertHtml('error', $errorMessage);
if (!empty($successMessage)) $alertHtml = alertHtml('success', $successMessage);

$html = str_replace('{{MESSAGGIO_PROFILO}}', $alertHtml, $html);
$html = str_replace('{{CSRF_FIELD}}', csrfField(), $html);
$html = str_replace('{{USERNAME}}', htmlspecialchars($utente['Username']), $html);
$html = str_replace('{{EMAIL}}', htmlspecialchars($utente['Email']), $html);

echo getTemplatePage("Modifica Profilo - AliceTrueCrime", $html);
