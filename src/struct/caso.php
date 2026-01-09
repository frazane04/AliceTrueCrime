<?php
// src/struct/caso.php

require_once __DIR__ . '/funzioni_db.php';

// ========================================
// GESTIONE SLUG/ID - Supporta entrambi i formati
// ========================================
$casoId = 0;
$dbFunctions = new FunzioniDB();

// Controlla se c'è uno slug nell'URL (es: /caso/il-mostro-di-milwaukee)
if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    
    // Converti lo slug in ID
    $casoId = $dbFunctions->getCasoIdBySlug($slug);
    
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

// Recupero i dati del caso dal database
$caso = $dbFunctions->getCasoById($casoId);
$colpevoli = $dbFunctions->getColpevoliByCaso($casoId);
$vittime = $dbFunctions->getVittimeByCaso($casoId);
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

// ========================================
// GENERAZIONE HTML VITTIME
// ========================================
$html_vittime = "";

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

// ========================================
// GENERAZIONE HTML ARTICOLI
// ========================================
$html_articoli = "";

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

// ========================================
// PREPARO DATI PER VISUALIZZAZIONE
// ========================================
$titolo = htmlspecialchars($caso['Titolo']);
$storia = nl2br(htmlspecialchars($caso['Storia']));
$data = date('d/m/Y', strtotime($caso['Data']));
$luogo = htmlspecialchars($caso['Luogo']);
$tipologia = htmlspecialchars($caso['Tipologia'] ?? 'Non specificata');

// Gestione immagine
$immagine = !empty($caso['Immagine']) 
    ? $prefix . '/' . htmlspecialchars($caso['Immagine'])
    : $prefix . '/assets/img/caso-placeholder.jpeg';

// Badge status
$statusClass = $caso['Approvato'] ? 'status-approved' : 'status-pending';
$statusText = $caso['Approvato'] ? '✓ Caso Verificato' : '⏳ In Revisione';

// ========================================
// SOSTITUZIONI PLACEHOLDER
// ========================================
$htmlImmagine = '<img alt="Evidenza principale del caso ' . $titolo . '" src="' . $immagine . '" class="img-evidence" width="300" />';
$contenuto = str_replace('<!-- caso_immagine -->', $htmlImmagine, $contenuto);

$htmlStatus = '<p class="status-badge ' . $statusClass . '">' . $statusText . '</p>';
$contenuto = str_replace('<!-- caso_status -->', $htmlStatus, $contenuto);

$htmlTitolo = '<h1>' . $titolo . '</h1>';
$contenuto = str_replace('<!-- caso_titolo -->', $htmlTitolo, $contenuto);

$htmlTipologia = '<p class="italic">Categoria: ' . $tipologia . '</p>';
$contenuto = str_replace('<!-- caso_tipologia -->', $htmlTipologia, $contenuto);

$contenuto = str_replace('<!-- caso_storia -->', $storia, $contenuto);
$contenuto = str_replace('<!-- caso_data -->', $data, $contenuto);
$contenuto = str_replace('<!-- caso_luogo -->', $luogo, $contenuto);
$contenuto = str_replace('<!-- caso_vittime -->', $html_vittime, $contenuto);
$contenuto = str_replace('<!-- caso_colpevoli -->', $html_colpevoli, $contenuto);
$contenuto = str_replace('<!-- caso_articoli -->', $html_articoli, $contenuto);

// ========================================
// GESTIONE COMMENTI
// ========================================
$messaggioCommento = '';

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
        $isAdmin = $_SESSION['is_admin'] ?? false;
        
        if ($idCommento > 0) {
            $resultEliminazione = $dbFunctions->eliminaCommento($idCommento, $emailUtente, $isAdmin);
            
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
$htmlCommenti = '';

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
            $isAdmin = $_SESSION['is_admin'] ?? false;
            
            if ($commento['Email'] === $emailLoggata || $isAdmin) {
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
$htmlFormCommento = '';

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

$contenuto = str_replace('<!-- caso_numero_commenti -->', $numeroCommenti, $contenuto);
$contenuto = str_replace('<!-- caso_form_commento -->', $htmlFormCommento, $contenuto);
$contenuto = str_replace('<!-- caso_lista_commenti -->', $htmlCommenti, $contenuto);

// Output finale
$titoloPagina = $titolo . " - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>