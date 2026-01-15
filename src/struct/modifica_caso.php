<?php
// src/struct/modifica_caso.php
// Gestione modifica casi - Con supporto upload immagini
// VERSIONE CORRETTA: Preserva immagini esistenti

require_once __DIR__ . '/funzioni_db.php';
require_once __DIR__ . '/ImageHandler.php';

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
$imageHandler = new ImageHandler();
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
    // Elimina le immagini associate
    if (!empty($caso['Immagine'])) {
        $imageHandler->eliminaImmagine($caso['Immagine']);
    }
    foreach ($vittimeEsistenti as $v) {
        if (!empty($v['Immagine'])) {
            $imageHandler->eliminaImmagine($v['Immagine']);
        }
    }
    foreach ($colpevoliEsistenti as $c) {
        if (!empty($c['Immagine'])) {
            $imageHandler->eliminaImmagine($c['Immagine']);
        }
    }
    
    $result = $dbFunctions->eliminaCaso($casoId, $emailUtente, $isAdmin);
    
    if ($result['success']) {
        header("Location: $prefix/profilo?msg=caso_eliminato");
        exit;
    } else {
        $messaggioFeedback = '<div class="alert alert-error">❌ ' . htmlspecialchars($result['message']) . '</div>';
    }
}

// ========================================
// GESTIONE RIMOZIONE IMMAGINE SINGOLA
// ========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rimuovi_immagine') {
    $tipoImg = $_POST['tipo_immagine'] ?? '';
    $idEntita = intval($_POST['id_entita'] ?? 0);
    
    if ($tipoImg === 'caso' && $idEntita === $casoId) {
        if (!empty($caso['Immagine'])) {
            $imageHandler->eliminaImmagine($caso['Immagine']);
            $dbFunctions->aggiornaImmagineCaso($casoId, '');
            $caso['Immagine'] = '';
        }
    } elseif ($tipoImg === 'vittima' && $idEntita > 0) {
        foreach ($vittimeEsistenti as &$v) {
            if ($v['ID_Vittima'] == $idEntita && !empty($v['Immagine'])) {
                $imageHandler->eliminaImmagine($v['Immagine']);
                $dbFunctions->aggiornaImmagineVittima($idEntita, '');
                $v['Immagine'] = '';
                break;
            }
        }
    } elseif ($tipoImg === 'colpevole' && $idEntita > 0) {
        foreach ($colpevoliEsistenti as &$c) {
            if ($c['ID_Colpevole'] == $idEntita && !empty($c['Immagine'])) {
                $imageHandler->eliminaImmagine($c['Immagine']);
                $dbFunctions->aggiornaImmagineColpevole($idEntita, '');
                $c['Immagine'] = '';
                break;
            }
        }
    }
    
    $messaggioFeedback = '<div class="alert alert-success">✅ Immagine rimossa con successo</div>';
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

    // Recupero vittime con immagini esistenti
    $vittime_ids = $_POST['vittima_id'] ?? [];
    $vittime_nomi = $_POST['vittima_nome'] ?? [];
    $vittime_cognomi = $_POST['vittima_cognome'] ?? [];
    $vittime_luoghi_nascita = $_POST['vittima_luogo_nascita'] ?? [];
    $vittime_date_nascita = $_POST['vittima_data_nascita'] ?? [];
    $vittime_date_decesso = $_POST['vittima_data_decesso'] ?? [];
    $vittime_immagini_esistenti = $_POST['vittima_immagine_esistente'] ?? []; // NUOVO: campo hidden
    
    $vittime = [];
    for ($i = 0; $i < count($vittime_nomi); $i++) {
        if (!empty($vittime_nomi[$i]) && !empty($vittime_cognomi[$i])) {
            $vittime[] = [
                'id' => isset($vittime_ids[$i]) ? intval($vittime_ids[$i]) : 0,
                'nome' => trim($vittime_nomi[$i]),
                'cognome' => trim($vittime_cognomi[$i]),
                'luogo_nascita' => !empty($vittime_luoghi_nascita[$i]) ? trim($vittime_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($vittime_date_nascita[$i]) ? $vittime_date_nascita[$i] : null,
                'data_decesso' => !empty($vittime_date_decesso[$i]) ? $vittime_date_decesso[$i] : null,
                'immagine_esistente' => $vittime_immagini_esistenti[$i] ?? '', // Immagine esistente
                'file_index' => $i
            ];
        }
    }

    // Recupero colpevoli con immagini esistenti
    $colpevoli_ids = $_POST['colpevole_id'] ?? [];
    $colpevoli_nomi = $_POST['colpevole_nome'] ?? [];
    $colpevoli_cognomi = $_POST['colpevole_cognome'] ?? [];
    $colpevoli_luoghi_nascita = $_POST['colpevole_luogo_nascita'] ?? [];
    $colpevoli_date_nascita = $_POST['colpevole_data_nascita'] ?? [];
    $colpevoli_immagini_esistenti = $_POST['colpevole_immagine_esistente'] ?? []; // NUOVO: campo hidden
    
    $colpevoli = [];
    for ($i = 0; $i < count($colpevoli_nomi); $i++) {
        if (!empty($colpevoli_nomi[$i]) && !empty($colpevoli_cognomi[$i])) {
            $colpevoli[] = [
                'id' => isset($colpevoli_ids[$i]) ? intval($colpevoli_ids[$i]) : 0,
                'nome' => trim($colpevoli_nomi[$i]),
                'cognome' => trim($colpevoli_cognomi[$i]),
                'luogo_nascita' => !empty($colpevoli_luoghi_nascita[$i]) ? trim($colpevoli_luoghi_nascita[$i]) : 'N/A',
                'data_nascita' => !empty($colpevoli_date_nascita[$i]) ? $colpevoli_date_nascita[$i] : null,
                'immagine_esistente' => $colpevoli_immagini_esistenti[$i] ?? '', // Immagine esistente
                'file_index' => $i
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
    if (empty($vittime)) {
        $errori[] = "Inserisci almeno una vittima";
    }
    if (empty($colpevoli)) {
        $errori[] = "Inserisci almeno un colpevole";
    }

    // ========================================
    // SALVATAGGIO
    // ========================================
    if (empty($errori)) {
        try {
            $riApprova = $isAutore && !$isAdmin;
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;
            
            // Gestione immagine caso
            $nuovaImmagineCaso = null;
            if (isset($_FILES['immagine_caso']) && $_FILES['immagine_caso']['error'] !== UPLOAD_ERR_NO_FILE) {
                $resultImg = $imageHandler->caricaImmagine($_FILES['immagine_caso'], 'caso', $caso['Slug']);
                if ($resultImg['success'] && $resultImg['path']) {
                    // Elimina vecchia immagine
                    if (!empty($caso['Immagine'])) {
                        $imageHandler->eliminaImmagine($caso['Immagine']);
                    }
                    $nuovaImmagineCaso = $resultImg['path'];
                } elseif (!$resultImg['success']) {
                    $errori[] = "Errore immagine caso: " . $resultImg['message'];
                }
            }
            
            if (empty($errori)) {
                // 1. Aggiorna caso
                $resultCaso = $dbFunctions->aggiornaCaso(
                    $casoId, $titolo, $data, $luogo, 
                    $descrizione_breve, $storia, $tipologiaFinal, $riApprova,
                    $nuovaImmagineCaso
                );

                if ($resultCaso['success']) {
                    
                    // 2. Gestione vittime
                    // Raccogli le immagini esistenti da preservare
                    $immaginiVittimeDaPreservare = [];
                    foreach ($vittime as $v) {
                        if (!empty($v['immagine_esistente'])) {
                            $immaginiVittimeDaPreservare[] = $v['immagine_esistente'];
                        }
                    }
                    
                    // Elimina solo le immagini che non sono più usate
                    foreach ($vittimeEsistenti as $vOld) {
                        if (!empty($vOld['Immagine']) && !in_array($vOld['Immagine'], $immaginiVittimeDaPreservare)) {
                            $imageHandler->eliminaImmagine($vOld['Immagine']);
                        }
                    }
                    
                    $dbFunctions->eliminaVittimeByCaso($casoId);
                    
                    // Inserisci nuove vittime
                    foreach ($vittime as $v) {
                        $immagineVittima = null;
                        $idx = $v['file_index'];
                        
                        // Prima controlla se c'è una nuova immagine caricata
                        if (isset($_FILES['vittima_immagine']['name'][$idx]) && 
                            !empty($_FILES['vittima_immagine']['name'][$idx]) &&
                            $_FILES['vittima_immagine']['error'][$idx] === UPLOAD_ERR_OK) {
                            
                            $fileV = [
                                'name' => $_FILES['vittima_immagine']['name'][$idx],
                                'type' => $_FILES['vittima_immagine']['type'][$idx],
                                'tmp_name' => $_FILES['vittima_immagine']['tmp_name'][$idx],
                                'error' => $_FILES['vittima_immagine']['error'][$idx],
                                'size' => $_FILES['vittima_immagine']['size'][$idx]
                            ];
                            
                            // Se c'era un'immagine esistente, eliminala
                            if (!empty($v['immagine_esistente'])) {
                                $imageHandler->eliminaImmagine($v['immagine_esistente']);
                            }
                            
                            $slugV = $imageHandler->generaSlugPersona($v['nome'], $v['cognome']);
                            $resultImgV = $imageHandler->caricaImmagine($fileV, 'vittime', $slugV);
                            
                            if ($resultImgV['success'] && $resultImgV['path']) {
                                $immagineVittima = $resultImgV['path'];
                            }
                        } else {
                            // Usa l'immagine esistente se presente
                            $immagineVittima = !empty($v['immagine_esistente']) ? $v['immagine_esistente'] : null;
                        }
                        
                        $dbFunctions->inserisciVittima(
                            $casoId, $v['nome'], $v['cognome'],
                            $v['luogo_nascita'], $v['data_nascita'], $v['data_decesso'],
                            $immagineVittima
                        );
                    }

                    // 3. Gestione colpevoli
                    // Raccogli le immagini esistenti da preservare
                    $immaginiColpevoliDaPreservare = [];
                    foreach ($colpevoli as $c) {
                        if (!empty($c['immagine_esistente'])) {
                            $immaginiColpevoliDaPreservare[] = $c['immagine_esistente'];
                        }
                    }
                    
                    // Elimina solo le immagini che non sono più usate
                    foreach ($colpevoliEsistenti as $cOld) {
                        if (!empty($cOld['Immagine']) && !in_array($cOld['Immagine'], $immaginiColpevoliDaPreservare)) {
                            $imageHandler->eliminaImmagine($cOld['Immagine']);
                        }
                    }
                    
                    $dbFunctions->rimuoviColpevoliByCaso($casoId);
                    
                    foreach ($colpevoli as $c) {
                        $immagineColpevole = null;
                        $idx = $c['file_index'];
                        
                        // Prima controlla se c'è una nuova immagine caricata
                        if (isset($_FILES['colpevole_immagine']['name'][$idx]) && 
                            !empty($_FILES['colpevole_immagine']['name'][$idx]) &&
                            $_FILES['colpevole_immagine']['error'][$idx] === UPLOAD_ERR_OK) {
                            
                            $fileC = [
                                'name' => $_FILES['colpevole_immagine']['name'][$idx],
                                'type' => $_FILES['colpevole_immagine']['type'][$idx],
                                'tmp_name' => $_FILES['colpevole_immagine']['tmp_name'][$idx],
                                'error' => $_FILES['colpevole_immagine']['error'][$idx],
                                'size' => $_FILES['colpevole_immagine']['size'][$idx]
                            ];
                            
                            // Se c'era un'immagine esistente, eliminala
                            if (!empty($c['immagine_esistente'])) {
                                $imageHandler->eliminaImmagine($c['immagine_esistente']);
                            }
                            
                            $slugC = $imageHandler->generaSlugPersona($c['nome'], $c['cognome']);
                            $resultImgC = $imageHandler->caricaImmagine($fileC, 'colpevoli', $slugC);
                            
                            if ($resultImgC['success'] && $resultImgC['path']) {
                                $immagineColpevole = $resultImgC['path'];
                            }
                        } else {
                            // Usa l'immagine esistente se presente
                            $immagineColpevole = !empty($c['immagine_esistente']) ? $c['immagine_esistente'] : null;
                        }
                        
                        $colpevoleId = $dbFunctions->inserisciColpevole(
                            $c['nome'], $c['cognome'], $c['luogo_nascita'], $c['data_nascita'],
                            $immagineColpevole
                        );
                        if ($colpevoleId) {
                            $dbFunctions->collegaColpevoleACaso($colpevoleId, $casoId);
                        }
                    }

                    // 4. Aggiorna articoli
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
foreach ($vittimeEsistenti as $v) {
    $htmlVittime .= generaHtmlVittima($v, $prefix);
}
if (empty($vittimeEsistenti)) {
    $htmlVittime = generaHtmlVittima(null, $prefix);
}

// Colpevoli
$htmlColpevoli = '';
foreach ($colpevoliEsistenti as $c) {
    $htmlColpevoli .= generaHtmlColpevole($c, $prefix);
}
if (empty($colpevoliEsistenti)) {
    $htmlColpevoli = generaHtmlColpevole(null, $prefix);
}

// Articoli
$htmlArticoli = '';
foreach ($articoliEsistenti as $a) {
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

// Anteprima immagine caso esistente
$anteprimaImmagineCaso = '';
if (!empty($caso['Immagine']) && $imageHandler->immagineEsiste($caso['Immagine'])) {
    $altCaso = ImageHandler::generaAlt('caso', ['titolo' => $caso['Titolo']]);
    $anteprimaImmagineCaso = '
    <div class="image-preview-existing">
        <img src="' . $prefix . '/' . htmlspecialchars($caso['Immagine']) . '" alt="' . $altCaso . '" class="preview-image">
        <form method="POST" class="inline-form">
            <input type="hidden" name="action" value="rimuovi_immagine">
            <input type="hidden" name="tipo_immagine" value="caso">
            <input type="hidden" name="id_entita" value="' . $casoId . '">
            <button type="submit" class="btn-remove-preview" onclick="return confirm(\'Rimuovere questa immagine?\')">✕ Rimuovi immagine</button>
        </form>
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
$contenuto = str_replace('<!-- anteprima_immagine_caso -->', $anteprimaImmagineCaso, $contenuto);
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
function generaHtmlVittima($dati = null, $prefix = '') {
    $id = $dati['ID_Vittima'] ?? 0;
    $nome = htmlspecialchars($dati['Nome'] ?? '');
    $cognome = htmlspecialchars($dati['Cognome'] ?? '');
    $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
    $dataNascita = $dati['DataNascita'] ?? '';
    $dataDecesso = $dati['DataDecesso'] ?? '';
    $immagine = $dati['Immagine'] ?? '';
    
    // Campo hidden per preservare l'immagine esistente
    $hiddenImmagine = '<input type="hidden" name="vittima_immagine_esistente[]" value="' . htmlspecialchars($immagine) . '">';
    
    // Anteprima immagine esistente
    $anteprimaImg = '';
    if (!empty($immagine)) {
        $altVittima = ImageHandler::generaAlt('vittima', ['nome' => $nome, 'cognome' => $cognome]);
        $anteprimaImg = '
        <div class="image-preview-existing">
            <img src="' . $prefix . '/' . htmlspecialchars($immagine) . '" alt="' . $altVittima . '" class="preview-image preview-image-small">
            <span class="img-label">Immagine attuale</span>
        </div>';
    }
    
    return <<<HTML
    <div class="entry-card vittima-entry">
        <button type="button" class="btn-remove" onclick="rimuoviEntry(this)" aria-label="Rimuovi vittima">×</button>
        <input type="hidden" name="vittima_id[]" value="$id">
        $hiddenImmagine
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
        <div class="form-row">
            <div class="form-group">
                <label>Foto (carica per sostituire)</label>
                $anteprimaImg
                <input type="file" name="vittima_immagine[]" accept="image/jpeg,image/png,image/webp">
                <small class="form-hint">Lascia vuoto per mantenere l'immagine attuale</small>
            </div>
        </div>
    </div>
HTML;
}

function generaHtmlColpevole($dati = null, $prefix = '') {
    $id = $dati['ID_Colpevole'] ?? 0;
    $nome = htmlspecialchars($dati['Nome'] ?? '');
    $cognome = htmlspecialchars($dati['Cognome'] ?? '');
    $luogo = htmlspecialchars($dati['LuogoNascita'] ?? '');
    $dataNascita = $dati['DataNascita'] ?? '';
    $immagine = $dati['Immagine'] ?? '';
    
    // Campo hidden per preservare l'immagine esistente
    $hiddenImmagine = '<input type="hidden" name="colpevole_immagine_esistente[]" value="' . htmlspecialchars($immagine) . '">';
    
    // Anteprima immagine esistente
    $anteprimaImg = '';
    if (!empty($immagine)) {
        $altColpevole = ImageHandler::generaAlt('colpevole', ['nome' => $nome, 'cognome' => $cognome]);
        $anteprimaImg = '
        <div class="image-preview-existing">
            <img src="' . $prefix . '/' . htmlspecialchars($immagine) . '" alt="' . $altColpevole . '" class="preview-image preview-image-small">
            <span class="img-label">Immagine attuale</span>
        </div>';
    }
    
    return <<<HTML
    <div class="entry-card colpevole-entry">
        <button type="button" class="btn-remove" onclick="rimuoviEntry(this)" aria-label="Rimuovi colpevole">×</button>
        <input type="hidden" name="colpevole_id[]" value="$id">
        $hiddenImmagine
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
        <div class="form-row">
            <div class="form-group">
                <label>Foto (carica per sostituire)</label>
                $anteprimaImg
                <input type="file" name="colpevole_immagine[]" accept="image/jpeg,image/png,image/webp">
                <small class="form-hint">Lascia vuoto per mantenere l'immagine attuale</small>
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