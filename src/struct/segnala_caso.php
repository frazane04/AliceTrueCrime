<?php
// src/struct/segnala_caso.php

// 0. Controllo Sessione (Importante!)
// Se l'utente NON è loggato, non deve vedere il form né poter inviare dati.
// Assumo che tu abbia già fatto session_start() nell'index.php o in utils.php
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    
    // Preparo un messaggio di errore invece del form
    $titoloPagina = "Accesso Negato - AliceTrueCrime";
    $contenuto = "
        <div class='access-denied-container' style='text-align: center; padding: 3rem;'>
            <h1>Area Riservata agli Investigatori</h1>
            <p>Per inviare una segnalazione devi essere registrato.</p>
            <a href='login.php' class='btn-login'>Accedi o Registrati</a>
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
    $autore = $_SESSION['username']; 
    // Oppure $autore_id = $_SESSION['user_id']; se nel DB usi l'ID numerico

    if (!empty($titolo) && !empty($data) && !empty($luogo) && !empty($descrizione)) {
        
        // QUI SALVATAGGIO NEL DB
        // Esempio query immaginaria: 
        // INSERT INTO casi (titolo, autore, ...) VALUES ('$titolo', '$autore', ...)
        
        $messaggioFeedback = "
            <div class='alert success'>
                <strong>Segnalazione inviata!</strong> Grazie Agente $autore, il caso è in revisione.
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


// 4. INIEZIONE DATI
$contenuto = str_replace('', $messaggioFeedback, $contenuto);


// 5. OUTPUT
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);

?>