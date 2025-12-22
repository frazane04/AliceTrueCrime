<?php
// src/struct/segnala_caso.php
// AGGIORNATO: Usa email dalla sessione

require_once __DIR__ . '/funzioni_db.php';

// Controllo Sessione
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
    $prefix = getPrefix();
    
    $titoloPagina = "Accesso Negato - AliceTrueCrime";
    $contenuto = "
        <div class='access-denied-container' style='text-align: center; padding: 3rem;'>
            <h1>Area Riservata agli Investigatori</h1>
            <p>Per inviare una segnalazione devi essere registrato.</p>
            <a href='$prefix/accedi' class='btn-login'>Accedi o Registrati</a>
        </div>
    ";
    
    echo getTemplatePage($titoloPagina, $contenuto);
    exit; 
}

// Inizializzo variabili
$templatePath = __DIR__ . '/../template/segnala_caso.html';
$messaggioFeedback = "";

// GESTIONE DEL FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    
    // RECUPERO AUTOMATICO DELL'AUTORE
    $autoreUsername = $_SESSION['user'];
    $autoreEmail = $_SESSION['user_email']; // Email come chiave primaria

    if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione)) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> Tutti i campi sono obbligatori.
            </div>
        ";
    } else {
        // Validazione lunghezza
        if (strlen($titolo) < 5 || strlen($titolo) > 200) {
            $messaggioFeedback = "
                <div class='alert alert-error'>
                    <strong>Errore:</strong> Il titolo deve essere tra 5 e 200 caratteri.
                </div>
            ";
        } elseif (strlen($descrizione) < 50) {
            $messaggioFeedback = "
                <div class='alert alert-error'>
                    <strong>Errore:</strong> La descrizione deve contenere almeno 50 caratteri.
                </div>
            ";
        } else {
            // Salvataggio nel database
            try {
                $dbFunctions = new FunzioniDB();
                $result = $dbFunctions->inserisciCaso(
                    $titolo, 
                    $data, 
                    $luogo, 
                    $descrizione, 
                    $autoreEmail
                );
                
                if ($result['success']) {
                    $messaggioFeedback = "
                        <div class='alert alert-success'>
                            <strong>✓ Segnalazione inviata con successo!</strong><br>
                            Il caso è stato inoltrato al team di moderazione per la revisione.<br>
                            <small>Autore: $autoreUsername</small>
                        </div>
                    ";
                    
                    // Reset campi dopo invio riuscito
                    $titolo = $data = $luogo = $descrizione = '';
                } else {
                    $messaggioFeedback = "
                        <div class='alert alert-error'>
                            <strong>Errore:</strong> {$result['message']}
                        </div>
                    ";
                }
            } catch (Exception $e) {
                error_log("Errore segnalazione caso: " . $e->getMessage());
                $messaggioFeedback = "
                    <div class='alert alert-error'>
                        <strong>Errore:</strong> Si è verificato un problema durante l'invio. Riprova più tardi.
                    </div>
                ";
            }
        }
    }
}

// CARICAMENTO TEMPLATE
if (file_exists($templatePath)) {
    $contenuto = file_get_contents($templatePath);
} else {
    $contenuto = "
        <div class='error' style='padding: 2rem; text-align: center; color: red;'>
            <h1>Errore Critico</h1>
            <p>Template mancante: $templatePath</p>
        </div>
    ";
}

// INIEZIONE FEEDBACK
$contenuto = str_replace(
    '<div id="feedback-area">',
    '<div id="feedback-area">' . $messaggioFeedback,
    $contenuto
);

// Mantieni i valori nei campi se c'è un errore
if (!empty($_POST) && !empty($messaggioFeedback) && strpos($messaggioFeedback, 'alert-error') !== false) {
    $replaceCount = 0;

    $contenuto = str_replace(
    'value=""',
    'value="' . htmlspecialchars($titolo ?? '', ENT_QUOTES, 'UTF-8') . '"',
    $contenuto,
    $replaceCount
);
}

// OUTPUT
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>