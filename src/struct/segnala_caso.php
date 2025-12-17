<?php
// src/struct/segnala_caso.php
// AGGIORNATO: Usa email dalla sessione

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

    if (!empty($titolo) && !empty($data) && !empty($luogo) && !empty($descrizione)) {
        
        // QUI SALVATAGGIO NEL DB
        // require_once __DIR__ . '/funzioni_db.php';
        // $db = new FunzioniDB();
        // $result = $db->inserisciCaso($titolo, $data, $luogo, $descrizione, $autoreEmail);
        
        $messaggioFeedback = "
            <div class='alert success'>
                <strong>Segnalazione inviata!</strong> Grazie, il caso è in revisione.
                <br>Autore: $autoreUsername ($autoreEmail)
            </div>
        ";
    } else {
        $messaggioFeedback = "
            <div class='alert error'>
                <strong>Errore:</strong> Tutti i campi sono obbligatori.
            </div>
        ";
    }
}

// CARICAMENTO TEMPLATE
if (file_exists($templatePath)) {
    $contenuto = file_get_contents($templatePath);
} else {
    $contenuto = "<div class='error'>Errore critico: Template mancante.</div>";
}

// INIEZIONE DATI
$contenuto = str_replace('<div id="feedback-area">', '<div id="feedback-area">' . $messaggioFeedback, $contenuto);

// OUTPUT
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);

?>