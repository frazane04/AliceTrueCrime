<?php
// src/struct/segnala.php
// Gestione segnalazione casi completa - VERSIONE AGGIORNATA
// Include: informazioni caso, vittime, colpevoli, articoli/fonti

require_once __DIR__ . '/funzioni_db.php';

// ========================================
// CONTROLLO SESSIONE
// ========================================
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $prefix = getPrefix();
    $titoloPagina = "Accesso Negato - AliceTrueCrime";
    $contenuto = "
        <div class='access-denied-container' style='text-align: center; padding: 3rem;'>
            <h1>🔒 Area Riservata agli Investigatori</h1>
            <p>Per inviare una segnalazione devi essere registrato e autenticato.</p>
            <a href='$prefix/accedi' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                Accedi o Registrati
            </a>
        </div>
    ";
    echo getTemplatePage($titoloPagina, $contenuto);
    exit;
}

// ========================================
// INIZIALIZZAZIONE VARIABILI
// ========================================
$templatePath = __DIR__ . '/../template/segnala_caso.html';
$messaggioFeedback = "";

// Dati caso
$titolo = $data = $luogo = $descrizione_breve = $storia = $tipologia = '';

// Arrays per dati dinamici (mantenuti in caso di errore)
$vittime = [];
$colpevoli = [];
$articoli = [];

// ========================================
// GESTIONE FORM POST
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ----------------------------------------
    // RECUPERO DATI GENERALI CASO
    // ----------------------------------------
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione_breve = trim($_POST['descrizione_breve'] ?? '');
    $storia = trim($_POST['storia'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');
    
    $autoreEmail = $_SESSION['user_email'];

    // ----------------------------------------
    // RECUPERO VITTIME (Array di array)
    // ----------------------------------------
    $vittime_nomi = $_POST['vittima_nome'] ?? [];
    $vittime_cognomi = $_POST['vittima_cognome'] ?? [];
    $vittime_luoghi_nascita = $_POST['vittima_luogo_nascita'] ?? [];
    $vittime_date_nascita = $_POST['vittima_data_nascita'] ?? [];
    $vittime_date_decesso = $_POST['vittima_data_decesso'] ?? [];
    
    // Costruisco array di vittime
    $vittime = [];
    $vittimeCount = count($vittime_nomi);
    
    for ($i = 0; $i < $vittimeCount; $i++) {
        if (!empty($vittime_nomi[$i]) && !empty($vittime_cognomi[$i])) {
            $vittime[] = [
                'nome' => trim($vittime_nomi[$i]),
                'cognome' => trim($vittime_cognomi[$i]),
                'luogo_nascita' => !empty($vittime_luoghi_nascita[$i]) ? trim($vittime_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($vittime_date_nascita[$i]) ? $vittime_date_nascita[$i] : null,
                'data_decesso' => !empty($vittime_date_decesso[$i]) ? $vittime_date_decesso[$i] : null
            ];
        }
    }

    // ----------------------------------------
    // RECUPERO COLPEVOLI (Array di array)
    // ----------------------------------------
    $colpevoli_nomi = $_POST['colpevole_nome'] ?? [];
    $colpevoli_cognomi = $_POST['colpevole_cognome'] ?? [];
    $colpevoli_luoghi_nascita = $_POST['colpevole_luogo_nascita'] ?? [];
    $colpevoli_date_nascita = $_POST['colpevole_data_nascita'] ?? [];
    
    // Costruisco array di colpevoli
    $colpevoli = [];
    $colpevoliCount = count($colpevoli_nomi);
    
    for ($i = 0; $i < $colpevoliCount; $i++) {
        if (!empty($colpevoli_nomi[$i]) && !empty($colpevoli_cognomi[$i])) {
            $colpevoli[] = [
                'nome' => trim($colpevoli_nomi[$i]),
                'cognome' => trim($colpevoli_cognomi[$i]),
                'luogo_nascita' => !empty($colpevoli_luoghi_nascita[$i]) ? trim($colpevoli_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($colpevoli_date_nascita[$i]) ? $colpevoli_date_nascita[$i] : null
            ];
        }
    }

    // ----------------------------------------
    // RECUPERO ARTICOLI/FONTI (Array di array)
    // ----------------------------------------
    $articoli_titoli = $_POST['articolo_titolo'] ?? [];
    $articoli_date = $_POST['articolo_data'] ?? [];
    $articoli_link = $_POST['articolo_link'] ?? [];
    
    // Costruisco array di articoli
    $articoli = [];
    $articoliCount = count($articoli_titoli);
    
    for ($i = 0; $i < $articoliCount; $i++) {
        // Aggiungo solo se almeno titolo o link sono presenti
        if (!empty($articoli_titoli[$i]) || !empty($articoli_link[$i])) {
            $articoli[] = [
                'titolo' => !empty($articoli_titoli[$i]) ? trim($articoli_titoli[$i]) : 'Fonte senza titolo',
                'data' => !empty($articoli_date[$i]) ? $articoli_date[$i] : null,
                'link' => !empty($articoli_link[$i]) ? trim($articoli_link[$i]) : ''
            ];
        }
    }

    // ========================================
    // VALIDAZIONE DATI
    // ========================================
    $errori = [];

    // Validazione campi obbligatori
    if (empty($titolo)) {
        $errori[] = "Il titolo del caso è obbligatorio";
    } elseif (strlen($titolo) < 5 || strlen($titolo) > 200) {
        $errori[] = "Il titolo deve essere tra 5 e 200 caratteri";
    }

    if (empty($data)) {
        $errori[] = "La data dell'accaduto è obbligatoria";
    } elseif (strtotime($data) > time()) {
        $errori[] = "La data non può essere nel futuro";
    }

    if (empty($luogo)) {
        $errori[] = "Il luogo è obbligatorio";
    }

    if (empty($descrizione_breve)) {
        $errori[] = "La descrizione breve è obbligatoria";
    } elseif (strlen($descrizione_breve) > 500) {
        $errori[] = "La descrizione breve non può superare i 500 caratteri";
    }

    if (empty($storia)) {
        $errori[] = "La ricostruzione completa del caso è obbligatoria";
    } elseif (strlen($storia) < 50) {
        $errori[] = "La storia deve contenere almeno 50 caratteri";
    } elseif (strlen($storia) > 10000) {
        $errori[] = "La storia non può superare i 10.000 caratteri";
    }

    // Validazione vittime (almeno una richiesta)
    if (empty($vittime)) {
        $errori[] = "Devi inserire almeno una vittima";
    }

    // Validazione colpevoli (almeno uno richiesto)
    if (empty($colpevoli)) {
        $errori[] = "Devi inserire almeno un colpevole (o 'Ignoto' se sconosciuto)";
    }

    // ========================================
    // INSERIMENTO NEL DATABASE
    // ========================================
    if (empty($errori)) {
        try {
            $dbFunctions = new FunzioniDB();
            
            // Tipologia finale (NULL se vuota)
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;
            
            // 1. INSERIMENTO CASO
            $resultCaso = $dbFunctions->inserisciCaso(
                $titolo,
                $data,
                $luogo,
                $descrizione_breve,
                $storia,
                $tipologiaFinal,
                null, // immagine (per ora NULL)
                $autoreEmail
            );

            if ($resultCaso['success']) {
                $casoId = $resultCaso['caso_id'];
                
                // 2. INSERIMENTO VITTIME
                foreach ($vittime as $vittima) {
                    $dbFunctions->inserisciVittima(
                        $casoId,
                        $vittima['nome'],
                        $vittima['cognome'],
                        $vittima['luogo_nascita'],
                        $vittima['data_nascita'],
                        $vittima['data_decesso']
                    );
                }

                // 3. INSERIMENTO COLPEVOLI
                foreach ($colpevoli as $colpevole) {
                    $colpevoleId = $dbFunctions->inserisciColpevole(
                        $colpevole['nome'],
                        $colpevole['cognome'],
                        $colpevole['luogo_nascita'],
                        $colpevole['data_nascita']
                    );
                    
                    // Collegamento colpevole-caso
                    if ($colpevoleId) {
                        $dbFunctions->collegaColpevoleACaso($colpevoleId, $casoId);
                    }
                }

                // 4. INSERIMENTO ARTICOLI/FONTI
                foreach ($articoli as $articolo) {
                    $dbFunctions->inserisciArticolo(
                        $casoId,
                        $articolo['titolo'],
                        $articolo['data'],
                        $articolo['link']
                    );
                }

                // SUCCESS MESSAGE
                $messaggioFeedback = "
                    <div class='alert alert-success'>
                        <strong>✅ Segnalazione completa inviata con successo!</strong><br>
                        Il caso è stato inoltrato al team di moderazione per la revisione.<br><br>
                        <small>
                            <strong>Riepilogo:</strong><br>
                            • Caso ID: {$casoId}<br>
                            • Vittime inserite: " . count($vittime) . "<br>
                            • Colpevoli inseriti: " . count($colpevoli) . "<br>
                            • Fonti inserite: " . count($articoli) . "<br>
                            • Segnalato da: {$_SESSION['user']}
                        </small>
                    </div>
                ";

                // Reset campi dopo successo
                $titolo = $data = $luogo = $descrizione_breve = $storia = $tipologia = '';
                $vittime = [];
                $colpevoli = [];
                $articoli = [];

            } else {
                $errori[] = $resultCaso['message'];
            }

        } catch (Exception $e) {
            error_log("Errore segnalazione caso completa: " . $e->getMessage());
            $errori[] = "Si è verificato un errore durante l'invio. Riprova più tardi.";
        }
    }

    // ========================================
    // MESSAGGIO ERRORI
    // ========================================
    if (!empty($errori)) {
        $messaggioFeedback = "<div class='alert alert-error'><strong>⚠️ Errori nella segnalazione:</strong><ul>";
        foreach ($errori as $errore) {
            $messaggioFeedback .= "<li>" . htmlspecialchars($errore) . "</li>";
        }
        $messaggioFeedback .= "</ul></div>";
    }
}

// ========================================
// CARICAMENTO TEMPLATE
// ========================================
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

// ========================================
// INIEZIONE FEEDBACK
// ========================================
$contenuto = str_replace(
    '<div id="feedback-area">',
    '<div id="feedback-area">' . $messaggioFeedback,
    $contenuto
);

// ========================================
// MANTENIMENTO VALORI IN CASO DI ERRORE
// ========================================
if (!empty($_POST) && !empty($messaggioFeedback) && strpos($messaggioFeedback, 'alert-error') !== false) {
    
    // Titolo
    $contenuto = preg_replace(
        '/<input\s+type="text"\s+id="titolo"([^>]*)>/',
        '<input type="text" id="titolo"$1 value="' . htmlspecialchars($titolo, ENT_QUOTES) . '">',
        $contenuto,
        1
    );

    // Data
    $contenuto = preg_replace(
        '/<input\s+type="date"\s+id="data_crimine"([^>]*)>/',
        '<input type="date" id="data_crimine"$1 value="' . htmlspecialchars($data, ENT_QUOTES) . '">',
        $contenuto,
        1
    );

    // Luogo
    $contenuto = preg_replace(
        '/<input\s+type="text"\s+id="luogo"([^>]*)>/',
        '<input type="text" id="luogo"$1 value="' . htmlspecialchars($luogo, ENT_QUOTES) . '">',
        $contenuto,
        1
    );

    // Descrizione breve
    $contenuto = preg_replace(
        '/<textarea\s+id="descrizione_breve"([^>]*)><\/textarea>/',
        '<textarea id="descrizione_breve"$1>' . htmlspecialchars($descrizione_breve, ENT_QUOTES) . '</textarea>',
        $contenuto,
        1
    );

    // Storia
    $contenuto = preg_replace(
        '/<textarea\s+id="storia"([^>]*)><\/textarea>/',
        '<textarea id="storia"$1>' . htmlspecialchars($storia, ENT_QUOTES) . '</textarea>',
        $contenuto,
        1
    );
}

// ========================================
// OUTPUT FINALE
// ========================================
$titoloPagina = "Apri Fascicolo - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>
