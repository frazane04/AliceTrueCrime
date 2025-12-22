<?php
// src/struct/segnala.php
// Gestione segnalazione casi - VERSIONE CORRETTA

require_once __DIR__ . '/funzioni_db.php';

// Controllo Sessione
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $prefix = getPrefix();
    $titoloPagina = "Accesso Negato - AliceTrueCrime";
    $contenuto = "
        <div class='access-denied-container' style='text-align: center; padding: 3rem;'>
            <h1>Area Riservata agli Investigatori</h1>
            <p>Per inviare una segnalazione devi essere registrato.</p>
            <a href='$prefix/accedi' class='btn-login' style='display: inline-block; margin-top: 1rem; padding: 0.8rem 1.5rem; background: #0D8ABC; color: white; text-decoration: none; border-radius: 5px;'>Accedi o Registrati</a>
        </div>
    ";
    echo getTemplatePage($titoloPagina, $contenuto);
    exit;
}

// Inizializzo variabili
$templatePath = __DIR__ . '/../template/segnala_caso.html';
$messaggioFeedback = "";

// Dati da mantenere in caso di errore
$titolo = '';
$data = '';
$luogo = '';
$descrizione = '';
$tipologia = '';

// GESTIONE DEL FORM
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupero dati dal form
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');

    // Recupero utente dalla sessione
    $autoreUsername = $_SESSION['user'];
    $autoreEmail = $_SESSION['user_email'];

    // Validazione base
    if (empty($titolo) || empty($data) || empty($luogo) || empty($descrizione)) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> Tutti i campi obbligatori devono essere compilati.
            </div>
        ";
    } 
    // Validazione lunghezza titolo
    elseif (strlen($titolo) < 5 || strlen($titolo) > 200) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> Il titolo deve essere tra 5 e 200 caratteri.
            </div>
        ";
    }
    // Validazione lunghezza descrizione
    elseif (strlen($descrizione) < 50) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> La descrizione deve contenere almeno 50 caratteri per garantire un'analisi accurata del caso.
            </div>
        ";
    }
    // Validazione lunghezza massima descrizione
    elseif (strlen($descrizione) > 5000) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> La descrizione non può superare i 5000 caratteri.
            </div>
        ";
    }
    // Validazione data
    elseif (strtotime($data) > time()) {
        $messaggioFeedback = "
            <div class='alert alert-error'>
                <strong>Errore:</strong> La data non può essere nel futuro.
            </div>
        ";
    }
    else {
        // Salvataggio nel database
        try {
            $dbFunctions = new FunzioniDB();
            
            // Usa tipologia se fornita, altrimenti NULL
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;
            
            $result = $dbFunctions->inserisciCaso(
                $titolo,
                $data,
                $luogo,
                $descrizione,
                $tipologiaFinal,
                null // immagine (per ora NULL)
            );

            if ($result['success']) {
                $messaggioFeedback = "
                    <div class='alert alert-success'>
                        <strong>Segnalazione inviata con successo!</strong><br>
                        Il caso è stato inoltrato al team di moderazione per la revisione.<br>
                        <small>Caso ID: {$result['caso_id']} | Segnalato da: {$autoreUsername}</small>
                    </div>
                ";

                // Reset campi dopo invio riuscito
                $titolo = $data = $luogo = $descrizione = $tipologia = '';
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
    // Titolo
    $contenuto = preg_replace(
        '/<input\s+type="text"\s+id="titolo"([^>]*)value=""/',
        '<input type="text" id="titolo"$1value="' . htmlspecialchars($titolo, ENT_QUOTES, 'UTF-8') . '"',
        $contenuto,
        1
    );

    // Data
    $contenuto = preg_replace(
        '/<input\s+type="date"\s+id="data_crimine"([^>]*)>/',
        '<input type="date" id="data_crimine"$1 value="' . htmlspecialchars($data, ENT_QUOTES, 'UTF-8') . '">',
        $contenuto,
        1
    );

    // Luogo
    $contenuto = preg_replace(
        '/<input\s+type="text"\s+id="luogo"([^>]*)>/',
        '<input type="text" id="luogo"$1 value="' . htmlspecialchars($luogo, ENT_QUOTES, 'UTF-8') . '">',
        $contenuto,
        1
    );

    // Descrizione
    $contenuto = preg_replace(
        '/<textarea\s+id="descrizione"([^>]*)><\/textarea>/',
        '<textarea id="descrizione"$1>' . htmlspecialchars($descrizione, ENT_QUOTES, 'UTF-8') . '</textarea>',
        $contenuto,
        1
    );

    // Tipologia (se presente nel form)
    if (strpos($contenuto, 'id="tipologia"') !== false) {
        $contenuto = preg_replace(
            '/<select\s+id="tipologia"([^>]*)>/',
            '<select id="tipologia"$1 data-selected="' . htmlspecialchars($tipologia, ENT_QUOTES, 'UTF-8') . '">',
            $contenuto,
            1
        );
    }
}

// OUTPUT
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>