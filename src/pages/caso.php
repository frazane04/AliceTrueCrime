<?php
// src/struct/caso.php

require_once __DIR__ . '/../db/funzioni_db.php';

// Gestione Slug
$casoId = 0;
$dbFunctions = new FunzioniDB();
$isAdmin = $_SESSION['is_admin'] ?? false;
$isAdminPreview = ($_GET['preview'] ?? '') === 'admin' && $isAdmin;

$soloApprovati = !$isAdminPreview;

if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    $casoId = $dbFunctions->getCasoIdBySlug($slug, $soloApprovati);

    if ($casoId === null) {
        renderErrorPageAndExit('Caso Non Trovato', 'Il caso richiesto (<strong>' . htmlspecialchars($slug) . '</strong>) non esiste.', 404);
    }
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $casoId = intval($_GET['id']);
}

$prefix = getPrefix();

if ($casoId <= 0) {
    renderErrorPageAndExit('ID Caso Non Valido', 'Il caso richiesto non è stato specificato correttamente.', 400);
}

// Azioni Admin
$messaggioAdmin = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $isAdmin) {
    $action = $_POST['action'] ?? '';

    if ($action === 'approva_caso') {
        $result = $dbFunctions->approvaCaso($casoId);
        if ($result['success']) {
            header("Location: $prefix/esplora/" . $slug);
            exit;
        }
        $messaggioAdmin = alertHtml('error', $result['message']);
    }

    if ($action === 'rifiuta_caso') {
        $result = $dbFunctions->rifiutaCaso($casoId);
        if ($result['success']) {
            header("Location: $prefix/profilo");
            exit;
        }
        $messaggioAdmin = alertHtml('error', $result['message']);
    }
}

// Recupero Dati

$caso = $dbFunctions->getCasoById($casoId, $soloApprovati);
$colpevoli = $dbFunctions->getColpevoliByCaso($casoId, $soloApprovati);
$vittime = $dbFunctions->getVittimeByCaso($casoId, $soloApprovati);
$articoli = $dbFunctions->getArticoliByCaso($casoId);

if (!$caso) {
    renderErrorPageAndExit('Caso Non Trovato', 'Il caso richiesto non esiste o non è stato ancora approvato.', 404);
}

$puoModificare = false;
$htmlAzioniUtente = '';

if (isLoggedIn()) {
    $emailUtente = $_SESSION['user_email'];
    $puoModificare = $dbFunctions->puoModificareCaso($casoId, $emailUtente, $isAdmin);

    if ($puoModificare) {
        $linkModifica = $prefix . '/esplora/' . $caso['Slug'] . '/modifica';
        $fromParam = $_GET['from'] ?? '';
        if ($fromParam === 'profilo') {
            $linkModifica .= '?from=profilo';
        }
        $htmlAzioniUtente = renderComponent('btn-azione-caso', [
            'LINK_HREF' => $linkModifica,
            'TESTO' => 'Modifica Caso'
        ]);
    }
}

$contenuto = loadTemplate('caso');

$htmlColpevoli = generaHtmlPersone($colpevoli, 'colpevole');
$htmlVittime = generaHtmlPersone($vittime, 'vittima');
$htmlArticoli = generaHtmlArticoli($articoli);


$titolo = htmlspecialchars($caso['Titolo']);
$descrizione = htmlspecialchars($caso['Descrizione']);
$storia = nl2br(htmlspecialchars($caso['Storia']));
$data = formatData($caso['Data']);
$luogo = htmlspecialchars($caso['Luogo']);
$tipologiaRaw = $caso['Tipologia'] ?? 'Non specificata';
$tipologieEn = ['Serial killer', 'Cold case', 'Celebrity'];
$tipologia = in_array($tipologiaRaw, $tipologieEn)
    ? '<span lang="en">' . htmlspecialchars($tipologiaRaw) . '</span>'
    : htmlspecialchars($tipologiaRaw);
$isApprovato = (bool) $caso['Approvato'];
$autore = htmlspecialchars($caso['Autore'] ?? 'Anonimo');
$immagine = getImageUrl($caso['Immagine']);

$isApprovato = (bool) $caso['Approvato'];
$autore = htmlspecialchars($caso['Autore'] ?? 'Anonimo');
$immagine = getImageUrl($caso['Immagine']);

$htmlStatusBadge = '';
if ($isAdmin) {
    $statusClass = $isApprovato ? 'status-approved' : 'status-pending';
    $statusText = $isApprovato ? 'Caso Verificato' : 'In Revisione';
    $htmlStatusBadge = '<p class="status-badge ' . $statusClass . '">' . $statusText . '</p>';
}

if ($isAdmin) {
    $statusClass = $isApprovato ? 'status-approved' : 'status-pending';
    $statusText = $isApprovato ? 'Caso Verificato' : 'In Revisione';
    $htmlStatusBadge = '<p class="status-badge ' . $statusClass . '">' . $statusText . '</p>';
}

if ($isApprovato && !$isAdminPreview) {
    $dbFunctions->incrementaVisualizzazioni($casoId);
}

$htmlAdminBar = ($isAdmin && !$isApprovato)
    ? renderComponent('admin-bar-caso', ['MESSAGGIO_ADMIN' => $messaggioAdmin])
    : '';

$htmlImmagine = '<img alt="Evidenza principale del caso ' . $titolo . '" src="' . $immagine . '" class="img-evidence" width="300" />';

$contenuto = strtr($contenuto, [
    '<!-- admin_bar -->' => $htmlAdminBar,
    '<!-- caso_immagine -->' => $htmlImmagine,
    '<!-- caso_status_badge -->' => $htmlStatusBadge,
    '<!-- caso_titolo -->' => $titolo,
    '<!-- caso_tipologia -->' => $tipologia,
    '<!-- caso_storia -->' => $storia,
    '<!-- caso_descrizione -->' => $descrizione,
    '<!-- caso_autore -->' => $autore,
    '<!-- caso_data -->' => $data,
    '<!-- caso_luogo -->' => $luogo,
    '<!-- caso_vittime -->' => $htmlVittime,
    '<!-- caso_colpevoli -->' => $htmlColpevoli,
    '<!-- caso_articoli -->' => $htmlArticoli,
]);

// Gestione Commenti

$messaggioCommento = '';
$htmlCommenti = '';
$htmlFormCommento = '';
$numeroCommenti = 0;

if ($isApprovato) {

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
                    $redirectUrl = $prefix . '/esplora/' . urlencode($caso['Slug']) . '#commenti';
                    header("Location: " . $redirectUrl);
                    exit;
                } else {
                    $messaggioCommento = alertHtml('error', $resultCommento['message']);
                }
            }
        }
    }


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
                    $redirectUrl = $prefix . '/esplora/' . urlencode($caso['Slug']) . '#commenti';
                    header("Location: " . $redirectUrl);
                    exit;
                } else {
                    $messaggioCommento = alertHtml('error', $resultEliminazione['message']);
                }
            }
        }
    }


    $commenti = $dbFunctions->getCommentiCaso($casoId);
    $numeroCommenti = $dbFunctions->contaCommentiCaso($casoId);


    if (!empty($commenti)) {
        foreach ($commenti as $commento) {
            $idCommento = (int) $commento['ID_Commento'];
            $usernameCommento = htmlspecialchars($commento['Username']);

            $usernameCommento = htmlspecialchars($commento['Username']);

            $pulsanteElimina = '';
            if (isLoggedIn()) {
                $emailLoggata = $_SESSION['user_email'];
                $isAdminComment = $_SESSION['is_admin'] ?? false;

                if ($commento['Email'] === $emailLoggata || $isAdminComment) {
                    $pulsanteElimina = renderComponent('btn-elimina-commento', ['ID_COMMENTO' => $idCommento]);
                }
            }

            $htmlCommenti .= renderComponent('card-commento', [
                'AVATAR_URL' => getAvatarUrl($usernameCommento, 48),
                'USERNAME' => $usernameCommento,
                'DATA_ISO' => $commento['Data'] ?? '',
                'DATA' => formatData($commento['Data'] ?? null, 'd/m/Y H:i'),
                'TESTO' => nl2br(htmlspecialchars($commento['Commento'])),
                'PULSANTE_ELIMINA' => $pulsanteElimina
            ]);
        }
    } else {
        $htmlCommenti = '<p class="no-commenti">Nessun commento ancora. Sii il primo a commentare!</p>';
    }


    if (isLoggedIn()) {
        $usernameLoggato = htmlspecialchars($_SESSION['user']);
        $htmlFormCommento = renderComponent('form-commento', [
            'AVATAR_URL' => getAvatarUrl($usernameLoggato, 48),
            'USERNAME' => $usernameLoggato,
            'MESSAGGIO' => $messaggioCommento
        ]);
    } else {
        $htmlFormCommento = renderComponent('login-prompt', [
            'MESSAGGIO' => 'Per commentare devi essere registrato.',
            'LINK_HREF' => $prefix . '/accedi',
            'LINK_TESTO' => 'Accedi per Commentare'
        ]);
    }
} else {
    $htmlCommenti = '<p class="no-commenti">I commenti saranno disponibili dopo l\'approvazione del caso.</p>';
    $htmlFormCommento = '';
}

$contenuto = strtr($contenuto, [
    '<!-- caso_numero_commenti -->' => $numeroCommenti,
    '<!-- caso_form_commento -->' => $htmlFormCommento,
    '<!-- caso_lista_commenti -->' => $htmlCommenti,
    '<!-- caso_azioni_utente -->' => $htmlAzioniUtente,
]);


$titoloPagina = $titolo . " - AliceTrueCrime";
// Crea una descrizione pulita di circa 150 caratteri per Google
$descrizioneSEO = mb_substr(strip_tags($caso['Descrizione']), 0, 155) . '...';
// Passa titolo, contenuto, descrizione e immagine specifica del caso
echo getTemplatePage($titoloPagina, $contenuto, $descrizioneSEO, $caso['Immagine']);
?>