<?php
// src/struct/caso.php

require_once __DIR__ . '/funzioni_db.php';

// ========================================
// GESTIONE SLUG/ID - Supporta entrambi i formati
// ========================================
$casoId = 0;
$dbFunctions = new FunzioniDB();
$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
$isAdminPreview = isset($_GET['preview']) && $_GET['preview'] === 'admin' && $isAdmin;

// Determina se filtrare solo approvati (default true, false se admin in preview)
$soloApprovati = !$isAdminPreview;

// Controlla se c'è uno slug nell'URL (es: /caso/il-mostro-di-milwaukee)
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    
    $casoId = $dbFunctions->getCasoIdBySlug($slug, $soloApprovati);
    
    if ($casoId === null) {
        // Slug non trovato
        http_response_code(404);
        $prefix = getPrefix();
        $contenuto = "
            <div class='error-container' style='text-align: center; padding: 3rem;'>
                <h1>🔍 Caso Non Trovato</h1>
                <p>Il caso richiesto (<strong>" . htmlspecialchars($slug) . "</strong>) non esiste.</p>
                <a href='$prefix/esplora' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                    Esplora tutti i Casi
                </a>
            </div>
        ";
        echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
        exit;
    }
}
// Fallback: supporta ancora il vecchio formato ?id=1 per compatibilità
elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $casoId = intval($_GET['id']);
}

// Inizializzo variabili
$templatePath = __DIR__ . '/../template/caso.html';
$prefix = getPrefix();

// Verifico che l'ID sia valido
if ($casoId <= 0) {
    http_response_code(400);
    
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>⚠️ ID Caso Non Valido</h1>
            <p>Il caso richiesto non è stato specificato correttamente.</p>
            <a href='$prefix/esplora' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                Esplora tutti i Casi
            </a>
        </div>
    ";
    
    echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
    exit;
}

// ========================================
// GESTIONE AZIONI ADMIN (POST)
// ========================================
$messaggioAdmin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    
    // Approva Caso
    if (isset($_POST['action']) && $_POST['action'] === 'approva_caso') {
        $result = $dbFunctions->approvaCaso($casoId);
        if ($result['success']) {
            // Redirect alla pagina del caso approvato (senza preview)
            header("Location: $prefix/caso/" . $slug);
            exit;
        } else {
            $messaggioAdmin = '<div class="alert alert-error">❌ ' . htmlspecialchars($result['message']) . '</div>';
        }
    }
    
    // Rifiuta Caso
    if (isset($_POST['action']) && $_POST['action'] === 'rifiuta_caso') {
        $result = $dbFunctions->rifiutaCaso($casoId);
        if ($result['success']) {
            // Redirect al profilo
            header("Location: $prefix/profilo");
            exit;
        } else {
            $messaggioAdmin = '<div class="alert alert-error">❌ ' . htmlspecialchars($result['message']) . '</div>';
        }
    }
}

// ========================================
// RECUPERO DATI 
// ========================================
$caso = $dbFunctions->getCasoById($casoId, $soloApprovati);
$colpevoli = $dbFunctions->getColpevoliByCaso($casoId, $soloApprovati);
$vittime = $dbFunctions->getVittimeByCaso($casoId, $soloApprovati);
$articoli = $dbFunctions->getArticoliByCaso($casoId);

// Verifico se il caso esiste
if (!$caso) {
    http_response_code(404);
    
    $contenuto = "
        <div class='error-container' style='text-align: center; padding: 3rem;'>
            <h1>🔍 Caso Non Trovato</h1>
            <p>Il caso richiesto non esiste o non è stato ancora approvato.</p>
            <a href='$prefix/esplora' class='btn btn-primary' style='display: inline-block; margin-top: 1rem;'>
                Esplora tutti i Casi
            </a>
        </div>";
    
    echo getTemplatePage("Caso Non Trovato - AliceTrueCrime", $contenuto);
    exit;
}

// Carico il template HTML
if (!file_exists($templatePath)) {
    die("Errore: Template caso.html non trovato in $templatePath");
}

$contenuto = file_get_contents($templatePath);

// ========================================
// GENERAZIONE HTML COLPEVOLI
// ========================================
$html_colpevoli = "";

if (!empty($colpevoli)) {
    foreach ($colpevoli as $colpevole) {
        $nome_colpevole = htmlspecialchars($colpevole['Nome']);
        $cognome_colpevole = htmlspecialchars($colpevole['Cognome']);
        $luogoNascita_colpevole = htmlspecialchars($colpevole['LuogoNascita']);
        $dataNascita_colpevole = !empty($colpevole['DataNascita']) 
            ? date('d/m/Y', strtotime($colpevole['DataNascita'])) 
            : 'Sconosciuta';    
        $imgColpevole = !empty($colpevole['Immagine']) 
            ? $prefix . '/' . htmlspecialchars($colpevole['Immagine'])
            : $prefix . '/assets/img/caso-placeholder.jpeg';

        $html_colpevoli .= '
        <article class="carousel-card">
            <div class="card-foto">
                <img src="' . $imgColpevole . '" alt="' . $nome_colpevole . " " . $cognome_colpevole . '">
            </div>
            <div class="card-info">
                <h4>' . $nome_colpevole . " " . $cognome_colpevole . '</h4>
                <p><strong>Nato a:</strong> ' . $luogoNascita_colpevole . '</p>
                <p><strong>Il:</strong> ' . $dataNascita_colpevole . '</p>
            </div>
        </article>';
    }
} else {
    $html_colpevoli = '<p>Nessun colpevole registrato.</p>';
}

// ========================================
// GENERAZIONE HTML VITTIME
// ========================================
$html_vittime = "";

if (!empty($vittime)) {
    foreach ($vittime as $vittima) {
        $nome_vittima = htmlspecialchars($vittima['Nome']);
        $cognome_vittima = htmlspecialchars($vittima['Cognome']);
        $luogoNascita_vittima = htmlspecialchars($vittima['LuogoNascita']);
        $dataNascita_vittima = !empty($vittima['DataNascita']) 
            ? date('d/m/Y', strtotime($vittima['DataNascita'])) 
            : 'Sconosciuta';
        $dataDecesso_vittima = !empty($vittima['DataDecesso']) 
            ? date('d/m/Y', strtotime($vittima['DataDecesso'])) 
            : 'Sconosciuta';
        $imgVittima = !empty($vittima['Immagine']) 
            ? $prefix . '/' . htmlspecialchars($vittima['Immagine'])
            : $prefix . '/assets/img/caso-placeholder.jpeg';

        $html_vittime .= '
        <article class="carousel-card">
            <div class="card-foto">
                <img src="' . $imgVittima . '" alt="' . $nome_vittima . " " . $cognome_vittima . '">
            </div>
            <div class="card-info">
                <h4>' . $nome_vittima . " " . $cognome_vittima . '</h4>
                <p><strong>Nato a:</strong> ' . $luogoNascita_vittima . '</p>
                <p><strong>Il:</strong> ' . $dataNascita_vittima . '</p>
                <p><strong>Decesso:</strong> ' . $dataDecesso_vittima . '</p>
            </div>
        </article>';
    }
} else {
    $html_vittime = '<p>Nessuna vittima registrata.</p>';
}

// ========================================
// GENERAZIONE HTML ARTICOLI
// ========================================
$html_articoli = "";

if (!empty($articoli)) {
    foreach ($articoli as $articolo) {
        $art_Titolo = htmlspecialchars($articolo['Titolo']);
        $art_data = !empty($articolo['Data']) 
            ? date('d/m/Y', strtotime($articolo['Data'])) 
            : 'Sconosciuta';
        $art_link = htmlspecialchars($articolo['Link']);

        $html_articoli .= '
        <li class="approfondimento">
            <p class="approfondimento-fonte">
                <a href="' . $art_link . '" target="_blank" rel="noopener noreferrer">' . $art_Titolo . '</a>
                <time class="approfondimento-data" datetime="d/m/Y">' . $art_data . '</time>
            </p>
        </li>';
    }
} else {
    $html_articoli = '<li>Nessuna fonte disponibile.</li>';
}

// ========================================
// PREPARO DATI PER VISUALIZZAZIONE
// ========================================
$titolo = htmlspecialchars($caso['Titolo']);
$storia = nl2br(htmlspecialchars($caso['Storia']));
$data = date('d/m/Y', strtotime($caso['Data']));
$luogo = htmlspecialchars($caso['Luogo']);
$tipologia = htmlspecialchars($caso['Tipologia'] ?? 'Non specificata');
$isApprovato = (bool)$caso['Approvato'];

// Gestione immagine
$immagine = !empty($caso['Immagine']) 
    ? $prefix . '/' . htmlspecialchars($caso['Immagine'])
    : $prefix . '/assets/img/caso-placeholder.jpeg';

// Badge status
$statusClass = $isApprovato ? 'status-approved' : 'status-pending';
$statusText = $isApprovato ? '✓ Caso Verificato' : '⏳ In Revisione';

// ========================================
// BARRA ADMIN (se admin e caso non approvato)
// ========================================
$htmlAdminBar = '';

if ($isAdmin && !$isApprovato) {
    $htmlAdminBar = '
    <div class="admin-action-bar">
        <div class="admin-bar-content">
            <span class="admin-bar-label">Pannello Admin - Caso in attesa di approvazione</span>
            
            ' . $messaggioAdmin . '
            
            <div class="admin-bar-actions">
                <form method="POST">
                    <input type="hidden" name="action" value="approva_caso">
                    <button type="submit" class="btn btn-success" onclick="return confirm(\'Confermi l\\\'approvazione di questo caso?\')">
                        Approva Caso
                    </button>
                </form>
                
                <form method="POST">
                    <input type="hidden" name="action" value="rifiuta_caso">
                    <button type="submit" class="btn btn-danger" onclick="return confirm(\'⚠️ ATTENZIONE: Questa azione eliminerà definitivamente il caso. Continuare?\')">
                        Rifiuta ed Elimina
                    </button>
                </form>                
            </div>
        </div>
    </div>';
}

// ========================================
// SOSTITUZIONI PLACEHOLDER
// ========================================

// Admin bar in alto
$contenuto = str_replace('<!-- admin_bar -->', $htmlAdminBar, $contenuto);

$htmlImmagine = '<img alt="Evidenza principale del caso ' . $titolo . '" src="' . $immagine . '" class="img-evidence" width="300" />';
$contenuto = str_replace('<!-- caso_immagine -->', $htmlImmagine, $contenuto);

$contenuto = str_replace('{{caso_status}}', $statusClass, $contenuto);

$contenuto = str_replace('<!-- caso_status_text -->', $statusText, $contenuto);

$contenuto = str_replace('<!-- caso_titolo -->', $titolo, $contenuto);

$contenuto = str_replace('<!-- caso_tipologia -->', $tipologia, $contenuto);

$contenuto = str_replace('<!-- caso_storia -->', $storia, $contenuto);
$contenuto = str_replace('<!-- caso_data -->', $data, $contenuto);
$contenuto = str_replace('<!-- caso_luogo -->', $luogo, $contenuto);
$contenuto = str_replace('<!-- caso_vittime -->', $html_vittime, $contenuto);
$contenuto = str_replace('<!-- caso_colpevoli -->', $html_colpevoli, $contenuto);
$contenuto = str_replace('<!-- caso_articoli -->', $html_articoli, $contenuto);

// ========================================
// GESTIONE COMMENTI (solo per casi approvati)
// ========================================
$messaggioCommento = '';
$htmlCommenti = '';
$htmlFormCommento = '';
$numeroCommenti = 0;

if ($isApprovato) {
    // Gestione POST per nuovo commento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'aggiungi_commento') {
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $messaggioCommento = '<div class="alert alert-error">Devi effettuare il login per commentare.</div>';
        } else {
            $testoCommento = trim($_POST['commento'] ?? '');
            $emailUtente = $_SESSION['user_email'];
            
            if (empty($testoCommento)) {
                $messaggioCommento = '<div class="alert alert-error">Il commento non può essere vuoto.</div>';
            } else {
                $resultCommento = $dbFunctions->inserisciCommento($emailUtente, $casoId, $testoCommento);
                
                if ($resultCommento['success']) {
                    $messaggioCommento = '<div class="alert alert-success">' . htmlspecialchars($resultCommento['message']) . '</div>';
                    header("Location: " . $_SERVER['REQUEST_URI'] . "#commenti");
                    exit;
                } else {
                    $messaggioCommento = '<div class="alert alert-error">' . htmlspecialchars($resultCommento['message']) . '</div>';
                }
            }
        }
    }

    // Gestione eliminazione commento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_commento') {
        
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            $messaggioCommento = '<div class="alert alert-error">Devi effettuare il login.</div>';
        } else {
            $idCommento = intval($_POST['id_commento'] ?? 0);
            $emailUtente = $_SESSION['user_email'];
            $isAdminComment = $_SESSION['is_admin'] ?? false;
            
            if ($idCommento > 0) {
                $resultEliminazione = $dbFunctions->eliminaCommento($idCommento, $emailUtente, $isAdminComment);
                
                if ($resultEliminazione['success']) {
                    $messaggioCommento = '<div class="alert alert-success">' . htmlspecialchars($resultEliminazione['message']) . '</div>';
                    header("Location: " . $_SERVER['REQUEST_URI'] . "#commenti");
                    exit;
                } else {
                    $messaggioCommento = '<div class="alert alert-error">' . htmlspecialchars($resultEliminazione['message']) . '</div>';
                }
            }
        }
    }

    // Recupera i commenti
    $commenti = $dbFunctions->getCommentiCaso($casoId);
    $numeroCommenti = $dbFunctions->contaCommentiCaso($casoId);

    // Genera HTML commenti
    if (!empty($commenti)) {
        foreach ($commenti as $commento) {
            $usernameCommento = htmlspecialchars($commento['Username']);
            $dataCommento = date('d/m/Y H:i', strtotime($commento['Data']));
            $testoCommento = nl2br(htmlspecialchars($commento['Commento']));
            $idCommento = (int)$commento['ID_Commento'];
            
            $avatarUrl = "https://ui-avatars.com/api/?name=" . urlencode($usernameCommento) . "&background=0D8ABC&color=fff&size=48";
            
            $pulsanteElimina = '';
            if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
                $emailLoggata = $_SESSION['user_email'];
                $isAdminComment = $_SESSION['is_admin'] ?? false;
                
                if ($commento['Email'] === $emailLoggata || $isAdminComment) {
                    $pulsanteElimina = '
                    <form method="POST" style="display: inline;" onsubmit="return confirm(\'Sei sicuro di voler eliminare questo commento?\');">
                        <input type="hidden" name="action" value="elimina_commento" />
                        <input type="hidden" name="id_commento" value="' . $idCommento . '" />
                        <button type="submit" class="btn-elimina-commento" aria-label="Elimina commento">🗑️ Elimina</button>
                    </form>';
                }
            }
            
            $htmlCommenti .= '
            <article class="commento-card">
                <div class="commento-header">
                    <img src="' . $avatarUrl . '" alt="" class="commento-avatar" />
                    <div class="commento-info">
                        <strong class="commento-autore">' . $usernameCommento . '</strong>
                        <time class="commento-data" datetime="' . $commento['Data'] . '">' . $dataCommento . '</time>
                    </div>
                    ' . $pulsanteElimina . '
                </div>
                <div class="commento-contenuto">
                    <p>' . $testoCommento . '</p>
                </div>
            </article>';
        }
    } else {
        $htmlCommenti = '<p class="no-commenti">Nessun commento ancora. Sii il primo a commentare!</p>';
    }

    // Form commento
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $usernameLoggato = htmlspecialchars($_SESSION['user']);
        $avatarLoggato = "https://ui-avatars.com/api/?name=" . urlencode($usernameLoggato) . "&background=0D8ABC&color=fff&size=48";
        
        $htmlFormCommento = '
        <div class="form-commento-container">
            <h3>Scrivi un commento</h3>
            
            ' . $messaggioCommento . '
            
            <form method="POST" class="form-commento" action="#commenti">
                <input type="hidden" name="action" value="aggiungi_commento" />
                
                <div class="form-commento-header">
                    <img src="' . $avatarLoggato . '" alt="" class="commento-avatar" />
                    <strong>' . $usernameLoggato . '</strong>
                </div>
                
                <div class="form-group">
                    <label for="commento" class="sr-only">Il tuo commento</label>
                    <textarea 
                        id="commento" 
                        name="commento" 
                        rows="4" 
                        placeholder="Condividi la tua opinione su questo caso..."
                        required
                        maxlength="2000"
                    ></textarea>
                    <small class="form-hint">Massimo 2000 caratteri</small>
                </div>
                
                <button type="submit" class="btn btn-primary">💬 Pubblica Commento</button>
            </form>
        </div>';
    } else {
        $htmlFormCommento = '
        <div class="login-prompt">
            <p>Per commentare devi essere registrato.</p>
            <a href="' . $prefix . '/accedi" class="btn btn-primary">Accedi per Commentare</a>
        </div>';
    }
} else {
    // Caso non approvato - niente commenti
    $htmlCommenti = '<p class="no-commenti">I commenti saranno disponibili dopo l\'approvazione del caso.</p>';
    $htmlFormCommento = '';
}

$contenuto = str_replace('<!-- caso_numero_commenti -->', $numeroCommenti, $contenuto);
$contenuto = str_replace('<!-- caso_form_commento -->', $htmlFormCommento, $contenuto);
$contenuto = str_replace('<!-- caso_lista_commenti -->', $htmlCommenti, $contenuto);

// Output finale
$titoloPagina = $titolo . " - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>