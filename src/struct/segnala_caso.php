<?php
// src/struct/segnala_caso.php

// 0. Controllo Sessione (Importante!)
// Se l'utente NON è loggato, non deve vedere il form né poter inviare dati.
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
    $prefix = getPrefix();
    
    // Preparo un messaggio di errore invece del form
    $titoloPagina = "Accesso Negato - AliceTrueCrime";
    $contenuto = "
        <div class='access-denied-container' style='text-align: center; padding: 3rem;'>
            <h1>Area Riservata agli Investigatori</h1>
            <p>Per inviare una segnalazione devi essere registrato.</p>
            <a href='$prefix/accedi' class='btn-login'>Accedi o Registrati</a>
        </div>
    ";
    
    // Stampo subito e blocco l'esecuzione del resto dello script
    echo getTemplatePage($titoloPagina, $contenuto);
    exit; 
}


// 1. Inizializzo variabili
$templatePath = __DIR__ . '/../template/segnala_caso.html';
$messaggioFeedback = "";


// 2. GESTIONE DEL FORM (Solo se loggato)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione = trim($_POST['descrizione'] ?? '');
    
    // RECUPERO AUTOMATICO DELL'AUTORE
    // Non mi fido dell'input utente, prendo il dato sicuro dalla sessione
    $autoreUsername = $_SESSION['user'];
    $autoreId = $_SESSION['user_id'];

    if (!empty($titolo) && !empty($data) && !empty($luogo) && !empty($descrizione)) {
        
        // QUI SALVATAGGIO NEL DB
        // Esempio query preparata:
        // require_once __DIR__ . '/funzioni_db.php';
        // $db = new FunzioniDB();
        // $result = $db->inserisciCaso($titolo, $data, $luogo, $descrizione, $autoreId);
        
        $messaggioFeedback = "
            <div class='alert success'>
                <strong>Segnalazione inviata!</strong> Grazie, il caso è in revisione.
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


// 3. CARICAMENTO TEMPLATE
if (file_exists($templatePath)) {
    $contenuto = file_get_contents($templatePath);
} else {
    $contenuto = "<div class='error'>Errore critico: Template mancante.</div>";
}


// 4. INIEZIONE DATI - Inserisce il feedback nell'area apposita
$contenuto = str_replace('<div id="feedback-area">', '<div id="feedback-area">' . $messaggioFeedback, $contenuto);


// 5. OUTPUT
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);

?>