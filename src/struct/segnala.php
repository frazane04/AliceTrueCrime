<?php
// src/struct/segnala.php
// Gestione segnalazione casi completa - VERSIONE CON UPLOAD IMMAGINI

require_once __DIR__ . '/funzioni_db.php';
require_once __DIR__ . '/ImageHandler.php';

// ========================================
// CONTROLLO SESSIONE
// ========================================
$prefix = getPrefix();
requireAuth(false, "
    <div class='access-denied-container text-center'>
        <h1>Area Riservata agli Investigatori</h1>
        <p>Per inviare una segnalazione devi essere registrato e autenticato.</p>
        <a href='{$prefix}/accedi' class='btn btn-primary inline-block mt-1'>
            Accedi o Registrati
        </a>
    </div>
");

// ========================================
// INIZIALIZZAZIONE VARIABILI
// ========================================
$messaggioFeedback = "";
$titolo = $data = $luogo = $descrizione_breve = $storia = $tipologia = '';
$vittime = [];
$colpevoli = [];
$articoli = [];

// ========================================
// GESTIONE FORM POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Recupero dati generali caso
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione_breve = trim($_POST['descrizione_breve'] ?? '');
    $storia = trim($_POST['storia'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');
    $autoreEmail = $_SESSION['user_email'];

    // Recupero vittime
    $vittime_nomi = $_POST['vittima_nome'] ?? [];
    $vittime_cognomi = $_POST['vittima_cognome'] ?? [];
    $vittime_luoghi_nascita = $_POST['vittima_luogo_nascita'] ?? [];
    $vittime_date_nascita = $_POST['vittima_data_nascita'] ?? [];
    $vittime_date_decesso = $_POST['vittima_data_decesso'] ?? [];
    
    $vittime = [];
    for ($i = 0; $i < count($vittime_nomi); $i++) {
        if (!empty($vittime_nomi[$i]) && !empty($vittime_cognomi[$i])) {
            $vittime[] = [
                'nome' => trim($vittime_nomi[$i]),
                'cognome' => trim($vittime_cognomi[$i]),
                'luogo_nascita' => !empty($vittime_luoghi_nascita[$i]) ? trim($vittime_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($vittime_date_nascita[$i]) ? $vittime_date_nascita[$i] : null,
                'data_decesso' => !empty($vittime_date_decesso[$i]) ? $vittime_date_decesso[$i] : null,
                'file_index' => $i
            ];
        }
    }

    // Recupero colpevoli
    $colpevoli_nomi = $_POST['colpevole_nome'] ?? [];
    $colpevoli_cognomi = $_POST['colpevole_cognome'] ?? [];
    $colpevoli_luoghi_nascita = $_POST['colpevole_luogo_nascita'] ?? [];
    $colpevoli_date_nascita = $_POST['colpevole_data_nascita'] ?? [];
    
    $colpevoli = [];
    for ($i = 0; $i < count($colpevoli_nomi); $i++) {
        if (!empty($colpevoli_nomi[$i]) && !empty($colpevoli_cognomi[$i])) {
            $colpevoli[] = [
                'nome' => trim($colpevoli_nomi[$i]),
                'cognome' => trim($colpevoli_cognomi[$i]),
                'luogo_nascita' => !empty($colpevoli_luoghi_nascita[$i]) ? trim($colpevoli_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($colpevoli_date_nascita[$i]) ? $colpevoli_date_nascita[$i] : null,
                'file_index' => $i
            ];
        }
    }

    // Recupero articoli
    $articoli_titoli = $_POST['articolo_titolo'] ?? [];
    $articoli_date = $_POST['articolo_data'] ?? [];
    $articoli_link = $_POST['articolo_link'] ?? [];
    
    $articoli = [];
    for ($i = 0; $i < count($articoli_titoli); $i++) {
        if (!empty($articoli_titoli[$i]) || !empty($articoli_link[$i])) {
            $articoli[] = [
                'titolo' => !empty($articoli_titoli[$i]) ? trim($articoli_titoli[$i]) : 'Fonte senza titolo',
                'data' => !empty($articoli_date[$i]) ? $articoli_date[$i] : null,
                'link' => !empty($articoli_link[$i]) ? trim($articoli_link[$i]) : ''
            ];
        }
    }

    // ========================================
    // VALIDAZIONE
    // ========================================
    $errori = [];

    if (empty($titolo) || strlen($titolo) < 5 || strlen($titolo) > 200) {
        $errori[] = "Il titolo deve essere tra 5 e 200 caratteri";
    }
    if (empty($data) || strtotime($data) > time()) {
        $errori[] = "Data non valida o nel futuro";
    }
    if (empty($luogo)) {
        $errori[] = "Il luogo è obbligatorio";
    }
    if (empty($descrizione_breve) || strlen($descrizione_breve) > 500) {
        $errori[] = "Descrizione breve obbligatoria (max 500 caratteri)";
    }
    if (empty($storia) || strlen($storia) < 50 || strlen($storia) > 10000) {
        $errori[] = "La storia deve essere tra 50 e 10.000 caratteri";
    }
    if (empty($vittime)) {
        $errori[] = "Devi inserire almeno una vittima";
    }
    if (empty($colpevoli)) {
        $errori[] = "Devi inserire almeno un colpevole (o 'Ignoto' se sconosciuto)";
    }
    // Validazione vittime
    foreach ($vittime as $v) {
        if (empty($v['data_nascita'])) {
            $errori[] = "La data di nascita è obbligatoria per tutte le vittime";
            break;
        }
    }

    // Validazione colpevoli
    foreach ($colpevoli as $c) {
        if (empty($c['data_nascita'])) {
            $errori[] = "La data di nascita è obbligatoria per tutti i colpevoli";
            break;
        }
    }


    // ========================================
    // INSERIMENTO NEL DATABASE
    // ========================================
    if (empty($errori)) {
        try {
            $dbFunctions = new FunzioniDB();
            $imageHandler = new ImageHandler();
            
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;
            $slugCaso = $dbFunctions->generaSlugUnico($titolo);
            
            // Gestione immagine caso
            $immagineCaso = null;
            if (isset($_FILES['immagine_caso']) && $_FILES['immagine_caso']['error'] !== UPLOAD_ERR_NO_FILE) {
                $resultImg = $imageHandler->caricaImmagine($_FILES['immagine_caso'], 'caso', $slugCaso);
                if ($resultImg['success'] && $resultImg['path']) {
                    $immagineCaso = $resultImg['path'];
                } elseif (!$resultImg['success']) {
                    $errori[] = "Errore immagine caso: " . $resultImg['message'];
                }
            }
            
            if (empty($errori)) {
                // 1. Inserimento caso
                $resultCaso = $dbFunctions->inserisciCaso(
                    $titolo, $data, $luogo, $descrizione_breve, $storia,
                    $tipologiaFinal, $immagineCaso, $autoreEmail
                );

                if ($resultCaso['success']) {
                    $casoId = $resultCaso['caso_id'];
                    
                    // 2. Inserimento vittime con immagini
                    foreach ($vittime as $vittima) {
                        $immagineVittima = null;
                        $idx = $vittima['file_index'];
                        
                        if (isset($_FILES['vittima_immagine']['name'][$idx]) && 
                            $_FILES['vittima_immagine']['error'][$idx] !== UPLOAD_ERR_NO_FILE) {
                            
                            $fileVittima = [
                                'name' => $_FILES['vittima_immagine']['name'][$idx],
                                'type' => $_FILES['vittima_immagine']['type'][$idx],
                                'tmp_name' => $_FILES['vittima_immagine']['tmp_name'][$idx],
                                'error' => $_FILES['vittima_immagine']['error'][$idx],
                                'size' => $_FILES['vittima_immagine']['size'][$idx]
                            ];
                            
                            $slugV = $imageHandler->generaSlugPersona($vittima['nome'], $vittima['cognome']);
                            $resultImgV = $imageHandler->caricaImmagine($fileVittima, 'vittime', $slugV);
                            
                            if ($resultImgV['success'] && $resultImgV['path']) {
                                $immagineVittima = $resultImgV['path'];
                            }
                        }
                        
                        $dbFunctions->inserisciVittima(
                            $casoId, $vittima['nome'], $vittima['cognome'],
                            $vittima['luogo_nascita'], $vittima['data_nascita'],
                            $vittima['data_decesso'], $immagineVittima
                        );
                    }

                    // 3. Inserimento colpevoli con immagini
                    foreach ($colpevoli as $colpevole) {
                        $immagineColpevole = null;
                        $idx = $colpevole['file_index'];
                        
                        if (isset($_FILES['colpevole_immagine']['name'][$idx]) && 
                            $_FILES['colpevole_immagine']['error'][$idx] !== UPLOAD_ERR_NO_FILE) {
                            
                            $fileColpevole = [
                                'name' => $_FILES['colpevole_immagine']['name'][$idx],
                                'type' => $_FILES['colpevole_immagine']['type'][$idx],
                                'tmp_name' => $_FILES['colpevole_immagine']['tmp_name'][$idx],
                                'error' => $_FILES['colpevole_immagine']['error'][$idx],
                                'size' => $_FILES['colpevole_immagine']['size'][$idx]
                            ];
                            
                            $slugC = $imageHandler->generaSlugPersona($colpevole['nome'], $colpevole['cognome']);
                            $resultImgC = $imageHandler->caricaImmagine($fileColpevole, 'colpevoli', $slugC);
                            
                            if ($resultImgC['success'] && $resultImgC['path']) {
                                $immagineColpevole = $resultImgC['path'];
                            }
                        }
                        
                        $colpevoleId = $dbFunctions->inserisciColpevole(
                            $colpevole['nome'], $colpevole['cognome'],
                            $colpevole['luogo_nascita'], $colpevole['data_nascita'],
                            $immagineColpevole
                        );
                        
                        if ($colpevoleId) {
                            $dbFunctions->collegaColpevoleACaso($colpevoleId, $casoId);
                        }
                    }

                    // 4. Inserimento articoli
                    foreach ($articoli as $articolo) {
                        $dbFunctions->inserisciArticolo(
                            $casoId, $articolo['titolo'],
                            $articolo['data'], $articolo['link']
                        );
                    }

                    // Success
                    $messaggioFeedback = "
                        <div class='alert alert-success'>
                            <strong>✅ Segnalazione inviata con successo!</strong><br>
                            Il caso è stato inoltrato per la revisione.<br><br>
                            <small>
                                <strong>Riepilogo:</strong><br>
                                • Caso ID: {$casoId}<br>
                                • Vittime: " . count($vittime) . "<br>
                                • Colpevoli: " . count($colpevoli) . "<br>
                                • Fonti: " . count($articoli) . "<br>
                                • Segnalato da: {$_SESSION['user']}
                            </small>
                        </div>
                    ";

                    // Reset campi
                    $titolo = $data = $luogo = $descrizione_breve = $storia = $tipologia = '';
                    $vittime = [];
                    $colpevoli = [];
                    $articoli = [];

                } else {
                    $errori[] = $resultCaso['message'];
                }
            }

        } catch (Exception $e) {
            error_log("Errore segnalazione caso: " . $e->getMessage());
            $errori[] = "Si è verificato un errore. Riprova più tardi.";
        }
    }

    // Messaggio errori
    if (!empty($errori)) {
        $messaggioFeedback = "<div class='alert alert-error'><strong>⚠️ Errori:</strong><ul>";
        foreach ($errori as $errore) {
            $messaggioFeedback .= "<li>" . htmlspecialchars($errore) . "</li>";
        }
        $messaggioFeedback .= "</ul></div>";
    }
}

// ========================================
// CARICAMENTO TEMPLATE
// ========================================
$contenuto = loadTemplate('segnala_caso');

// Iniezione feedback
$contenuto = str_replace(
    '<div id="feedback-area">',
    '<div id="feedback-area">' . $messaggioFeedback,
    $contenuto
);

// ========================================
// OUTPUT
// ========================================
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>