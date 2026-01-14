<?php
// src/struct/modifica_caso.php
// Gestione modifica casi - Logica PHP separata dal template HTML

require_once __DIR__ . '/funzioni_db.php';

// ========================================
// CONTROLLO SESSIONE
// ========================================
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    $prefix = getPrefix();
    header("Location: $prefix/accedi");
    exit;
}

// ========================================
// INIZIALIZZAZIONE
// ========================================
$prefix = getPrefix();
$dbFunctions = new FunzioniDB();
$templatePath = __DIR__ . '/../template/modifica_caso.html';

$emailUtente = $_SESSION['user_email'];
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// ========================================
// RECUPERO ID CASO
// ========================================
$casoId = 0;

if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    $casoId = $dbFunctions->getCasoIdBySlug($slug, false);
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $casoId = intval($_GET['id']);
}

// Verifica esistenza caso
if ($casoId <= 0) {
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>⚠️ Caso Non Specificato</h1>
            <p>Nessun caso selezionato per la modifica.</p>
            <a href='$prefix/esplora' class='btn btn-primary'>Torna all'Esplorazione</a>
        </div>
    ";
    echo getTemplatePage("Errore - AliceTrueCrime", $contenuto);
    exit;
}

// ========================================
// VERIFICA PERMESSI
// ========================================
if (!$dbFunctions->puoModificareCaso($casoId, $emailUtente, $isAdmin)) {
    http_response_code(403);
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>🔒 Accesso Negato</h1>
            <p>Non hai i permessi per modificare questo caso.</p>
            <p>Solo l'autore originale o un amministratore possono modificare un caso.</p>
            <a href='$prefix/esplora' class='btn btn-primary'>Torna all'Esplorazione</a>
        </div>
    ";
    echo getTemplatePage("Accesso Negato - AliceTrueCrime", $contenuto);
    exit;
}

// ========================================
// RECUPERO DATI ESISTENTI
// ========================================
$caso = $dbFunctions->getCasoById($casoId, false);
$vittimeEsistenti = $dbFunctions->getVittimeByCaso($casoId, false);
$colpevoliEsistenti = $dbFunctions->getColpevoliByCaso($casoId, false);
$articoliEsistenti = $dbFunctions->getArticoliByCaso($casoId);

if (!$caso) {
    http_response_code(404);
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>🔍 Caso Non Trovato</h1>
            <p>Il caso richiesto non esiste.</p>
            <a href='$prefix/esplora' class='btn btn-primary'>Torna all'Esplorazione</a>
        </div>
    ";
    echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
    exit;
}

// Determina se l'utente è l'autore
$autoreOriginale = $dbFunctions->getAutoreCaso($casoId);
$isAutore = ($autoreOriginale === $emailUtente);

// ========================================
// VARIABILI FORM
// ========================================
$messaggioFeedback = "";

// ========================================
// GESTIONE ELIMINAZIONE CASO
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_caso') {
    $result = $dbFunctions->eliminaCaso($casoId, $emailUtente, $isAdmin);
    
    if ($result['success']) {
        header("Location: $prefix/profilo?msg=caso_eliminato");
        exit;
    } else {
        $messaggioFeedback = '<div class="alert alert-error">❌ ' . htmlspecialchars($result['message']) . '</div>';
    }
}

// ========================================
// GESTIONE FORM POST - MODIFICA
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifica_caso') {
    
    // Recupero dati dal form
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione_breve = trim($_POST['descrizione_breve'] ?? '');
    $storia = trim($_POST['storia'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');

    // Recupero vittime
    $vittime_ids = $_POST['vittima_id'] ?? [];
    $vittime_nomi = $_POST['vittima_nome'] ?? [];
    $vittime_cognomi = $_POST['vittima_cognome'] ?? [];
    $vittime_luoghi_nascita = $_POST['vittima_luogo_nascita'] ?? [];
    $vittime_date_nascita = $_POST['vittima_data_nascita'] ?? [];
    $vittime_date_decesso = $_POST['vittima_data_decesso'] ?? [];
    
    $vittime = [];
    for ($i = 0; $i < count($vittime_nomi); $i++) {
        if (!empty($vittime_nomi[$i]) && !empty($vittime_cognomi[$i])) {
            $vittime[] = [
                'id' => isset($vittime_ids[$i]) ? intval($vittime_ids[$i]) : 0,
                'nome' => trim($vittime_nomi[$i]),
                'cognome' => trim($vittime_cognomi[$i]),
                'luogo_nascita' => !empty($vittime_luoghi_nascita[$i]) ? trim($vittime_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($vittime_date_nascita[$i]) ? $vittime_date_nascita[$i] : null,
                'data_decesso' => !empty($vittime_date_decesso[$i]) ? $vittime_date_decesso[$i] : null
            ];
        }
    }

    // Recupero colpevoli
    $colpevoli_ids = $_POST['colpevole_id'] ?? [];
    $colpevoli_nomi = $_POST['colpevole_nome'] ?? [];
    $colpevoli_cognomi = $_POST['colpevole_cognome'] ?? [];
    $colpevoli_luoghi_nascita = $_POST['colpevole_luogo_nascita'] ?? [];
    $colpevoli_date_nascita = $_POST['colpevole_data_nascita'] ?? [];
    
    $colpevoli = [];
    for ($i = 0; $i < count($colpevoli_nomi); $i++) {
        if (!empty($colpevoli_nomi[$i]) && !empty($colpevoli_cognomi[$i])) {
            $colpevoli[] = [
                'id' => isset($colpevoli_ids[$i]) ? intval($colpevoli_ids[$i]) : 0,
                'nome' => trim($colpevoli_nomi[$i]),
                'cognome' => trim($colpevoli_cognomi[$i]),
                'luogo_nascita' => !empty($colpevoli_luoghi_nascita[$i]) ? trim($colpevoli_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($colpevoli_date_nascita[$i]) ? $colpevoli_date_nascita[$i] : null
            ];
        }
    }

    // Recupero articoli
    $articoli_ids = $_POST['articolo_id'] ?? [];
    $articoli_titoli = $_POST['articolo_titolo'] ?? [];
    $articoli_date = $_POST['articolo_data'] ?? [];
    $articoli_link = $_POST['articolo_link'] ?? [];
    
    $articoli = [];
    for ($i = 0; $i < count($articoli_titoli); $i++) {
        if (!empty($articoli_titoli[$i]) || !empty($articoli_link[$i])) {
            $articoli[] = [
                'id' => isset($articoli_ids[$i]) ? intval($articoli_ids[$i]) : 0,
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
    if (empty($storia) || strlen($storia) < 50) {
        $errori[] = "La storia deve contenere almeno 50 caratteri";
    }
    /*
    if (empty($vittime)) {
        $errori[] = "Inserisci almeno una vittima";
    }
    if (empty($colpevoli)) {
        $errori[] = "Inserisci almeno un colpevole";
    }*/

    // ========================================
    // SALVATAGGIO
    // ========================================
    if (empty($errori)) {
        try {
            $riApprova = $isAutore && !$isAdmin;
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;
            
            // 1. Aggiorna caso
            $resultCaso = $dbFunctions->aggiornaCaso(
                $casoId, $titolo, $data, $luogo, 
                $descrizione_breve, $storia, $tipologiaFinal, $riApprova
            );

            if ($resultCaso['success']) {
                // 2. Aggiorna vittime (delete + reinsert)
                $dbFunctions->eliminaVittimeByCaso($casoId);
                foreach ($vittime as $v) {
                    $dbFunctions->inserisciVittima(
                        $casoId, $v['nome'], $v['cognome'],
                        $v['luogo_nascita'], $v['data_nascita'], $v['data_decesso']
                    );
                }

                // 3. Aggiorna colpevoli (delete relations + reinsert)
                $dbFunctions->rimuoviColpevoliByCaso($casoId);
                foreach ($colpevoli as $c) {
                    $colpevoleId = $dbFunctions->inserisciColpevole(
                        $c['nome'], $c['cognome'], $c['luogo_nascita'], $c['data_nascita']
                    );
                    if ($colpevoleId) {
                        $dbFunctions->collegaColpevoleACaso($colpevoleId, $casoId);
                    }
                }

                // 4. Aggiorna articoli (delete + reinsert)
                $dbFunctions->eliminaArticoliByCaso($casoId);
                foreach ($articoli as $a) {
                    $dbFunctions->inserisciArticolo($casoId, $a['titolo'], $a['data'], $a['link']);
                }

                // Success
                $msgRiApprova = $riApprova ? '<br><strong>⚠️ Il caso è stato rimesso in attesa di approvazione.</strong>' : '';
                $btnVisualizza = $isAdmin ? "<br><br><a href='$prefix/caso/{$caso['Slug']}' class='btn btn-primary'>Visualizza Caso</a>" : "";
                $messaggioFeedback = "
                    <div class='alert alert-success'>
                        <strong>✅ Caso aggiornato con successo!</strong>
                        $msgRiApprova
                        $btnVisualizza
                    </div>
                ";

                // Ricarica dati aggiornati
                $caso = $dbFunctions->getCasoById($casoId, false);
                $vittimeEsistenti = $dbFunctions->getVittimeByCaso($casoId, false);
                $colpevoliEsistenti = $dbFunctions->getColpevoliByCaso($casoId, false);
                $articoliEsistenti = $dbFunctions->getArticoliByCaso($casoId);

            } else {
                $errori[] = $resultCaso['message'];
            }
        } catch (Exception $e) {
            error_log("Errore modifica caso: " . $e->getMessage());
            $errori[] = "Errore durante l'aggiornamento. Riprova più tardi.";
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
if (!file_exists($templatePath)) {
    die("Errore: Template modifica_caso.html non trovato");
}

$contenuto = file_get_contents($templatePath);

// ========================================
// GENERAZIONE HTML DINAMICO
// ========================================

// Vittime
$htmlVittime = '';
foreach ($vittimeEsistenti as $index => $v) {
    $htmlVittime .= generaHtmlVittima($v);
}
if (empty($vittimeEsistenti)) {
    $htmlVittime = generaHtmlVittima(null);
}

// Colpevoli
$htmlColpevoli = '';
foreach ($colpevoliEsistenti as $index => $c) {
    $htmlColpevoli .= generaHtmlColpevole($c);
}
if (empty($colpevoliEsistenti)) {
    $htmlColpevoli = generaHtmlColpevole(null);
}

// Articoli
$htmlArticoli = '';
foreach ($articoliEsistenti as $index => $a) {
    $htmlArticoli .= generaHtmlArticolo($a);
}

// Opzioni tipologia
$tipologie = ['Serial killer', 'Casi mediatici italiani', 'Amore tossico', 'Celebrity', 'Cold case', 'Altro'];
$opzioniTipologia = '<option value="">-- Seleziona categoria --</option>';
foreach ($tipologie as $t) {
    $selected = ($caso['Tipologia'] === $t) ? 'selected' : '';
    $opzioniTipologia .= "<option value=\"$t\" $selected>$t</option>";
}

// Avviso ri-approvazione
$avvisoRiApprovazione = '';
if ($isAutore && !$isAdmin) {
    $avvisoRiApprovazione = '
    <div class="alert alert-warning">
        <strong>⚠️ Attenzione:</strong> Modificando questo caso, verrà rimesso in attesa di approvazione.
    </div>';
}

// ========================================
// SOSTITUZIONI TEMPLATE
// ========================================
$contenuto = str_replace('<!-- caso_titolo -->', htmlspecialchars($caso['Titolo']), $contenuto);
$contenuto = str_replace('<!-- feedback_message -->', $messaggioFeedback, $contenuto);
$contenuto = str_replace('<!-- avviso_riapprovazione -->', $avvisoRiApprovazione, $contenuto);

$contenuto = str_replace('<!-- value_titolo -->', htmlspecialchars($caso['Titolo']), $contenuto);
$contenuto = str_replace('<!-- value_data -->', htmlspecialchars($caso['Data']), $contenuto);
$contenuto = str_replace('<!-- value_luogo -->', htmlspecialchars($caso['Luogo']), $contenuto);
$contenuto = str_replace('<!-- value_descrizione -->', htmlspecialchars($caso['Descrizione']), $contenuto);
$contenuto = str_replace('<!-- value_storia -->', htmlspecialchars($caso['Storia']), $contenuto);

$contenuto = str_replace('<!-- opzioni_tipologia -->', $opzioniTipologia, $contenuto);
$contenuto = str_replace('<!-- vittime_html -->', $htmlVittime, $contenuto);
$contenuto = str_replace('<!-- colpevoli_html -->', $htmlColpevoli, $contenuto);
$contenuto = str_replace('<!-- articoli_html -->', $htmlArticoli, $contenuto);
$contenuto = str_replace('<!-- link_annulla -->', $prefix . '/caso/' . $caso['Slug'], $contenuto);

// ========================================
// OUTPUT
// ========================================
echo getTemplatePage("Modifica Caso - AliceTrueCrime", $contenuto);

// ========================================
// FUNZIONI HELPER
// ========================================
function generaHtmlVittima($dati = null) {
    $id = $dati['ID_Vittima'] ?? 0;
    $nome = htmlspecialchars($dati['Nome'] ?? '');
    $cognome = htmlspecialchars($dati['Cognome'] ?? '');
    $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
    $dataNascita = $dati['DataNascita'] ?? '';
    $dataDecesso = $dati['DataDecesso'] ?? '';
    
    return <<<HTML
    <div class="entry-card vittima-entry">
        <button type="button" class="btn-remove" onclick="rimuoviEntry(this)" aria-label="Rimuovi vittima">×</button>
        <input type="hidden" name="vittima_id[]" value="$id">
        <div class="form-row">
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="vittima_nome[]" required placeholder="Nome" value="$nome">
            </div>
            <div class="form-group">
                <label>Cognome *</label>
                <input type="text" name="vittima_cognome[]" required placeholder="Cognome" value="$cognome">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Luogo di Nascita</label>
                <input type="text" name="vittima_luogo_nascita[]" placeholder="Luogo" value="$luogo">
            </div>
            <div class="form-group">
                <label>Data di Nascita</label>
                <input type="date" name="vittima_data_nascita[]" value="$dataNascita">
            </div>
            <div class="form-group">
                <label>Data Decesso</label>
                <input type="date" name="vittima_data_decesso[]" value="$dataDecesso">
            </div>
        </div>
    </div>
HTML;
}

function generaHtmlColpevole($dati = null) {
    $id = $dati['ID_Colpevole'] ?? 0;
    $nome = htmlspecialchars($dati['Nome'] ?? '');
    $cognome = htmlspecialchars($dati['Cognome'] ?? '');
    $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
    $dataNascita = $dati['DataNascita'] ?? '';
    
    return <<<HTML
    <div class="entry-card colpevole-entry">
        <button type="button" class="btn-remove" onclick="rimuoviEntry(this)" aria-label="Rimuovi colpevole">×</button>
        <input type="hidden" name="colpevole_id[]" value="$id">
        <div class="form-row">
            <div class="form-group">
                <label>Nome *</label>
                <input type="text" name="colpevole_nome[]" required placeholder="Nome (o 'Ignoto')" value="$nome">
            </div>
            <div class="form-group">
                <label>Cognome *</label>
                <input type="text" name="colpevole_cognome[]" required placeholder="Cognome" value="$cognome">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Luogo di Nascita</label>
                <input type="text" name="colpevole_luogo_nascita[]" placeholder="Luogo" value="$luogo">
            </div>
            <div class="form-group">
                <label>Data di Nascita</label>
                <input type="date" name="colpevole_data_nascita[]" value="$dataNascita">
            </div>
        </div>
    </div>
HTML;
}

function generaHtmlArticolo($dati = null) {
    $id = $dati['ID_Articolo'] ?? 0;
    $titolo = htmlspecialchars($dati['Titolo'] ?? '');
    $dataArt = $dati['Data'] ?? '';
    $link = htmlspecialchars($dati['Link'] ?? '');
    
    return <<<HTML
    <div class="entry-card articolo-entry">
        <button type="button" class="btn-remove" onclick="rimuoviEntry(this)" aria-label="Rimuovi articolo">×</button>
        <input type="hidden" name="articolo_id[]" value="$id">
        <div class="form-row">
            <div class="form-group">
                <label>Titolo Fonte</label>
                <input type="text" name="articolo_titolo[]" placeholder="Es: Articolo Repubblica" value="$titolo">
            </div>
            <div class="form-group">
                <label>Data Pubblicazione</label>
                <input type="date" name="articolo_data[]" value="$dataArt">
            </div>
        </div>
        <div class="form-group">
            <label>Link</label>
            <input type="url" name="articolo_link[]" placeholder="https://..." value="$link">
        </div>
    </div>
HTML;
}
?>