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

// Verifica se l'utente può modificare questo caso
$puoModificare = false;
$htmlAzioniUtente = '';

if (isLoggedIn()) {
    $emailUtente = $_SESSION['user_email'];
    $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
    
    // Usa il metodo puoModificareCaso per verificare i permessi
    $puoModificare = $dbFunctions->puoModificareCaso($casoId, $emailUtente, $isAdmin);
    
    if ($puoModificare) {
        $linkModifica = $prefix . '/modifica-caso?id=' . $casoId;
        
        $htmlAzioniUtente = '
        <div class="caso-azioni-utente">
            <a href="' . $linkModifica . '" class="btn btn-secondary">
                ✏️ Modifica Caso
            </a>
        </div>';
    }
}

// Carico il template HTML
$contenuto = loadTemplate('caso');

// ========================================
// GENERAZIONE HTML COLPEVOLI
// ========================================
$html_colpevoli = "";

if (!empty($colpevoli)) {
    foreach ($colpevoli as $colpevole) {
        $nomeCompleto = htmlspecialchars($colpevole['Nome'] . ' ' . $colpevole['Cognome']);
        $html_colpevoli .= renderComponent('card-persona', [
            'IMMAGINE'      => getImageUrl($colpevole['Immagine'] ?? null),
            'NOME_COMPLETO' => $nomeCompleto,
            'LUOGO_NASCITA' => htmlspecialchars($colpevole['LuogoNascita'] ?? 'Sconosciuto'),
            'DATA_NASCITA'  => formatData($colpevole['DataNascita'] ?? null),
            'EXTRA_INFO'    => ''
        ]);
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
        $nomeCompleto = htmlspecialchars($vittima['Nome'] . ' ' . $vittima['Cognome']);
        $html_vittime .= renderComponent('card-persona', [
            'IMMAGINE'      => getImageUrl($vittima['Immagine'] ?? null),
            'NOME_COMPLETO' => $nomeCompleto,
            'LUOGO_NASCITA' => htmlspecialchars($vittima['LuogoNascita'] ?? 'Sconosciuto'),
            'DATA_NASCITA'  => formatData($vittima['DataNascita'] ?? null),
            'EXTRA_INFO'    => '<p><strong>Decesso:</strong> ' . formatData($vittima['DataDecesso'] ?? null) . '</p>'
        ]);
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
        $art_link = htmlspecialchars($articolo['Link']);
        $html_articoli .= '<li class="approfondimento"><p class="approfondimento-fonte">';
        $html_articoli .= '<a href="' . $art_link . '" target="_blank" rel="noopener noreferrer">' . $art_Titolo . '</a>';
        $html_articoli .= '<time class="approfondimento-data">' . formatData($articolo['Data']) . '</time>';
        $html_articoli .= '</p></li>';
    }
} else {
    $html_articoli = '<li>Nessuna fonte disponibile.</li>';
}

// ========================================
// PREPARO DATI PER VISUALIZZAZIONE
// ========================================
$titolo = htmlspecialchars($caso['Titolo']);
$descrizione = htmlspecialchars($caso['Descrizione']);
$storia = nl2br(htmlspecialchars($caso['Storia']));
$data = formatData($caso['Data']);
$luogo = htmlspecialchars($caso['Luogo']);
$tipologia = htmlspecialchars($caso['Tipologia'] ?? 'Non specificata');
$isApprovato = (bool)$caso['Approvato'];
$autore = htmlspecialchars($caso['Autore'] ?? 'Anonimo');
$immagine = getImageUrl($caso['Immagine']);

// Badge status
$statusClass = $isApprovato ? 'status-approved' : 'status-pending';
$statusText = $isApprovato ? '✓ Caso Verificato' : '⏳ In Revisione';

// ========================================
// INCREMENTO VISUALIZZAZIONI
// ========================================
// Incrementa il contatore delle visualizzazioni solo per casi approvati
// Non incrementa in modalità preview admin
if ($isApprovato && !$isAdminPreview) {
    $dbFunctions->incrementaVisualizzazioni($casoId);
}

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
                <form method="POST" id="form-approva-caso">
                    <input type="hidden" name="action" value="approva_caso">
                    <button type="button" class="btn btn-success" onclick="confermaApprovaCaso()">
                        Approva Caso
                    </button>
                </form>

                <form method="POST" id="form-rifiuta-caso">
                    <input type="hidden" name="action" value="rifiuta_caso">
                    <button type="button" class="btn btn-danger" onclick="confermaRifiutaCaso()">
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
$htmlImmagine = '<img alt="Evidenza principale del caso ' . $titolo . '" src="' . $immagine . '" class="img-evidence" width="300" />';

$contenuto = strtr($contenuto, [
    '<!-- admin_bar -->'        => $htmlAdminBar,
    '<!-- caso_immagine -->'    => $htmlImmagine,
    '{{caso_status}}'           => $statusClass,
    '<!-- caso_status_text -->' => $statusText,
    '<!-- caso_titolo -->'      => $titolo,
    '<!-- caso_tipologia -->'   => $tipologia,
    '<!-- caso_storia -->'      => $storia,
    '<!-- caso_descrizione -->' => $descrizione,
    '<!-- caso_autore -->'      => $autore,
    '<!-- caso_data -->'        => $data,
    '<!-- caso_luogo -->'       => $luogo,
    '<!-- caso_vittime -->'     => $html_vittime,
    '<!-- caso_colpevoli -->'   => $html_colpevoli,
    '<!-- caso_articoli -->'    => $html_articoli,
]);

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

        if (!isLoggedIn()) {
            $messaggioCommento = alertHtml('error', 'Devi effettuare il login per commentare.');
        } else {
            $testoCommento = trim($_POST['commento'] ?? '');
            $emailUtente = $_SESSION['user_email'];
            
            if (empty($testoCommento)) {
                $messaggioCommento = alertHtml('error', 'Il commento non può essere vuoto.');
            } else {
                $resultCommento = $dbFunctions->inserisciCommento($emailUtente, $casoId, $testoCommento);

                if ($resultCommento['success']) {
                    $messaggioCommento = alertHtml('success', $resultCommento['message']);
                    // Redirect sicuro usando lo slug del caso invece di REQUEST_URI (previene CRLF injection)
                    $redirectUrl = $prefix . '/caso/' . urlencode($caso['Slug']) . '#commenti';
                    header("Location: " . $redirectUrl);
                    exit;
                } else {
                    $messaggioCommento = alertHtml('error', $resultCommento['message']);
                }
            }
        }
    }

    // Gestione eliminazione commento
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'elimina_commento') {

        if (!isLoggedIn()) {
            $messaggioCommento = alertHtml('error', 'Devi effettuare il login.');
        } else {
            $idCommento = intval($_POST['id_commento'] ?? 0);
            $emailUtente = $_SESSION['user_email'];
            $isAdminComment = $_SESSION['is_admin'] ?? false;
            
            if ($idCommento > 0) {
                $resultEliminazione = $dbFunctions->eliminaCommento($idCommento, $emailUtente, $isAdminComment);

                if ($resultEliminazione['success']) {
                    $messaggioCommento = alertHtml('success', $resultEliminazione['message']);
                    // Redirect sicuro usando lo slug del caso invece di REQUEST_URI (previene CRLF injection)
                    $redirectUrl = $prefix . '/caso/' . urlencode($caso['Slug']) . '#commenti';
                    header("Location: " . $redirectUrl);
                    exit;
                } else {
                    $messaggioCommento = alertHtml('error', $resultEliminazione['message']);
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
            $idCommento = (int)$commento['ID_Commento'];
            $usernameCommento = htmlspecialchars($commento['Username']);

            // Pulsante elimina (solo per autore o admin)
            $pulsanteElimina = '';
            if (isLoggedIn()) {
                $emailLoggata = $_SESSION['user_email'];
                $isAdminComment = $_SESSION['is_admin'] ?? false;

                if ($commento['Email'] === $emailLoggata || $isAdminComment) {
                    $pulsanteElimina = '<form method="POST" id="form-elimina-' . $idCommento . '" style="display:inline;">'
                        . '<input type="hidden" name="action" value="elimina_commento" />'
                        . '<input type="hidden" name="id_commento" value="' . $idCommento . '" />'
                        . '<button type="button" class="btn-elimina-commento" aria-label="Elimina commento" onclick="confermaEliminaCommento(' . $idCommento . ')">Elimina</button>'
                        . '</form>';
                }
            }

            $htmlCommenti .= renderComponent('card-commento', [
                'AVATAR_URL'       => getAvatarUrl($usernameCommento, 48),
                'USERNAME'         => $usernameCommento,
                'DATA_ISO'         => $commento['Data'] ?? '',
                'DATA'             => formatData($commento['Data'] ?? null, 'd/m/Y H:i'),
                'TESTO'            => nl2br(htmlspecialchars($commento['Commento'])),
                'PULSANTE_ELIMINA' => $pulsanteElimina
            ]);
        }
    } else {
        $htmlCommenti = '<p class="no-commenti">Nessun commento ancora. Sii il primo a commentare!</p>';
    }

    // Form commento
    if (isLoggedIn()) {
        $usernameLoggato = htmlspecialchars($_SESSION['user']);
        $htmlFormCommento = renderComponent('form-commento', [
            'AVATAR_URL' => getAvatarUrl($usernameLoggato, 48),
            'USERNAME'   => $usernameLoggato,
            'MESSAGGIO'  => $messaggioCommento
        ]);
    } else {
        $htmlFormCommento = '<div class="login-prompt">'
            . '<p>Per commentare devi essere registrato.</p>'
            . '<a href="' . $prefix . '/accedi" class="btn btn-primary">Accedi per Commentare</a>'
            . '</div>';
    }
} else {
    // Caso non approvato - niente commenti
    $htmlCommenti = '<p class="no-commenti">I commenti saranno disponibili dopo l\'approvazione del caso.</p>';
    $htmlFormCommento = '';
}

$contenuto = strtr($contenuto, [
    '<!-- caso_numero_commenti -->' => $numeroCommenti,
    '<!-- caso_form_commento -->'   => $htmlFormCommento,
    '<!-- caso_lista_commenti -->'  => $htmlCommenti,
    '<!-- caso_azioni_utente -->'   => $htmlAzioniUtente,
]);

// Output finale
$titoloPagina = $titolo . " - AliceTrueCrime";
echo getTemplatePage($titoloPagina, $contenuto);
?>
