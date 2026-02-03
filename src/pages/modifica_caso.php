<?php
// src/struct/modifica_caso.php
// Gestione modifica casi - Versione refactored con FormCasoHelper

require_once __DIR__ . '/../db/funzioni_db.php';
require_once __DIR__ . '/../helpers/ImageHandler.php';
require_once __DIR__ . '/../helpers/FormCasoHelper.php';
requireAuth();
$prefix = getPrefix();
$dbFunctions = new FunzioniDB();
$imageHandler = new ImageHandler();
$formHelper = new FormCasoHelper($imageHandler);

$emailUtente = $_SESSION['user_email'];
$isAdmin = $_SESSION['is_admin'] ?? false;

$casoId = 0;

if (isset($_GET['slug']) && !empty($_GET['slug'])) {
    $slug = trim($_GET['slug']);
    $casoId = $dbFunctions->getCasoIdBySlug($slug, false);
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    $casoId = intval($_GET['id']);
}

// Verifica esistenza caso
if ($casoId <= 0) {
    renderErrorPageAndExit('Caso Non Specificato', 'Nessun caso selezionato per la modifica.', 400);
}

if (!$dbFunctions->puoModificareCaso($casoId, $emailUtente, $isAdmin)) {
    renderErrorPageAndExit('Accesso Negato', 'Non hai i permessi per modificare questo caso.', 403);
}

$caso = $dbFunctions->getCasoById($casoId, false);
$vittimeEsistenti = $dbFunctions->getVittimeByCaso($casoId, false);
$colpevoliEsistenti = $dbFunctions->getColpevoliByCaso($casoId, false);
$articoliEsistenti = $dbFunctions->getArticoliByCaso($casoId);

if (!$caso) {
    renderErrorPageAndExit('Caso Non Trovato', 'Il caso richiesto non esiste.', 404);
}

$autoreOriginale = $dbFunctions->getAutoreCaso($casoId);
$isAutore = ($autoreOriginale === $emailUtente);

$messaggioFeedback = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'elimina_caso') {
    $formHelper->eliminaTutteImmaginiCaso($caso, $vittimeEsistenti, $colpevoliEsistenti);

    $result = $dbFunctions->eliminaCaso($casoId, $emailUtente, $isAdmin);

    if ($result['success']) {
        header("Location: $prefix/profilo?msg=caso_eliminato");
        exit;
    } else {
        $messaggioFeedback = alertHtml('error', $result['message']);
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'modifica_caso') {

    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione_breve = trim($_POST['descrizione_breve'] ?? '');
    $storia = trim($_POST['storia'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');
    $casoImmagineEsistente = $_POST['caso_immagine_esistente'] ?? '';

    $vittime = $formHelper->parseVittimeFromPost($_POST);
    $colpevoli = $formHelper->parseColpevoliFromPost($_POST);
    $articoli = $formHelper->parseArticoliFromPost($_POST);

    // Validazione
    $errori = [];

    if (empty($titolo) || strlen($titolo) < 5 || strlen($titolo) > 200) {
        $errori[] = "Il titolo deve essere tra 5 e 200 caratteri";
    }
    if (empty($data) || strtotime($data) > time()) {
        $errori[] = "Data non valida o nel futuro";
    }
    if (empty($luogo) || strlen($luogo) > 100) {
        $errori[] = "Il luogo è obbligatorio (max 100 caratteri)";
    }
    if (empty($descrizione_breve) || strlen($descrizione_breve) > 500) {
        $errori[] = "Descrizione breve obbligatoria (max 500 caratteri)";
    }
    if (empty($storia) || strlen($storia) < 50 || strlen($storia) > 10000) {
        $errori[] = "La storia deve essere tra 50 e 10.000 caratteri";
    }
    if (empty($vittime)) {
        $errori[] = "Inserisci almeno una vittima";
    }
    if (empty($colpevoli)) {
        $errori[] = "Inserisci almeno un colpevole";
    }
    foreach ($vittime as $v) {
        if (empty($v['data_nascita'])) {
            $errori[] = "La data di nascita è obbligatoria per tutte le vittime";
            break;
        }
    }
    foreach ($colpevoli as $c) {
        if (empty($c['data_nascita'])) {
            $errori[] = "La data di nascita è obbligatoria per tutti i colpevoli";
            break;
        }
    }

    // Salvataggio
    if (empty($errori)) {
        try {
            $riApprova = $isAutore && !$isAdmin;
            $tipologiaFinal = !empty($tipologia) ? $tipologia : null;

            $nuovaImmagineCaso = null;
            $aggiornaImmagineCaso = false;

            if (
                isset($_FILES['immagine_caso']) &&
                isset($_FILES['immagine_caso']['error']) &&
                $_FILES['immagine_caso']['error'] === UPLOAD_ERR_OK
            ) {

                if (!empty($caso['Immagine'])) {
                    $imageHandler->eliminaImmagine($caso['Immagine']);
                }

                $resultImg = $imageHandler->caricaImmagine($_FILES['immagine_caso'], 'caso', $caso['Slug']);
                if ($resultImg['success'] && $resultImg['path']) {
                    $nuovaImmagineCaso = $resultImg['path'];
                    $aggiornaImmagineCaso = true;
                } elseif (!$resultImg['success'] && $resultImg['message'] !== 'Nessuna immagine caricata') {
                    $errori[] = "Errore immagine caso: " . $resultImg['message'];
                }
            } else {

                if (empty($casoImmagineEsistente) && !empty($caso['Immagine'])) {
                    $imageHandler->eliminaImmagine($caso['Immagine']);
                    $nuovaImmagineCaso = '';
                    $aggiornaImmagineCaso = true;
                }
            }

            if (empty($errori)) {
                $resultCaso = $dbFunctions->aggiornaCaso(
                    $casoId,
                    $titolo,
                    $data,
                    $luogo,
                    $descrizione_breve,
                    $storia,
                    $tipologiaFinal,
                    $riApprova,
                    $nuovaImmagineCaso
                );

                if ($resultCaso['success']) {
                    $formHelper->pulisciImmaginiOrfane($vittime, $vittimeEsistenti);
                    $dbFunctions->eliminaVittimeByCaso($casoId);

                    foreach ($vittime as $v) {
                        $immagineVittima = $formHelper->gestisciImmaginePersona(
                            $_FILES,
                            'vittima',
                            $v['file_index'],
                            $v['nome'],
                            $v['cognome'],
                            $v['immagine_esistente']
                        );

                        $dbFunctions->inserisciVittima(
                            $casoId,
                            $v['nome'],
                            $v['cognome'],
                            $v['luogo_nascita'],
                            $v['data_nascita'],
                            $v['data_decesso'],
                            $immagineVittima
                        );
                    }

                    $formHelper->pulisciImmaginiOrfane($colpevoli, $colpevoliEsistenti);
                    $dbFunctions->rimuoviColpevoliByCaso($casoId);

                    foreach ($colpevoli as $c) {
                        $immagineColpevole = $formHelper->gestisciImmaginePersona(
                            $_FILES,
                            'colpevole',
                            $c['file_index'],
                            $c['nome'],
                            $c['cognome'],
                            $c['immagine_esistente']
                        );

                        $colpevoleId = $dbFunctions->inserisciColpevole(
                            $c['nome'],
                            $c['cognome'],
                            $c['luogo_nascita'],
                            $c['data_nascita'],
                            $immagineColpevole
                        );
                        if ($colpevoleId) {
                            $dbFunctions->collegaColpevoleACaso($colpevoleId, $casoId);
                        }
                    }

                    $dbFunctions->eliminaArticoliByCaso($casoId);
                    foreach ($articoli as $a) {
                        $dbFunctions->inserisciArticolo($casoId, $a['titolo'], $a['data'], $a['link']);
                    }

                    $caso = $dbFunctions->getCasoById($casoId, false);
                    $vittimeEsistenti = $dbFunctions->getVittimeByCaso($casoId, false);
                    $colpevoliEsistenti = $dbFunctions->getColpevoliByCaso($casoId, false);
                    $articoliEsistenti = $dbFunctions->getArticoliByCaso($casoId);

                    $messaggioFeedback = FormCasoHelper::generaMessaggioSuccessoModifica(
                        $riApprova,
                        $prefix,
                        $caso['Slug'],
                        $isAdmin,
                        (bool) $caso['Approvato'],
                        $_GET['from'] ?? ''
                    );

                } else {
                    $errori[] = $resultCaso['message'];
                }
            }
        } catch (Exception $e) {
            error_log("Errore modifica caso: " . $e->getMessage());
            $errori[] = "Errore durante l'aggiornamento. Riprova più tardi.";
        }
    }

    if (!empty($errori)) {
        $messaggioFeedback = FormCasoHelper::generaMessaggioErrori($errori);
    }
}

$contenuto = loadTemplate('modifica_caso');

$htmlVittime = $formHelper->generaHtmlListaVittime($vittimeEsistenti);
$htmlColpevoli = $formHelper->generaHtmlListaColpevoli($colpevoliEsistenti);
$htmlArticoli = $formHelper->generaHtmlListaArticoli($articoliEsistenti);
$opzioniTipologia = $formHelper->generaOpzioniTipologia($caso['Tipologia']);

$immagineCasoData = $formHelper->generaAnteprimaImmagineCaso($caso);

$avvisoRiApprovazione = ($isAutore && !$isAdmin)
    ? alertHtml('warning', 'Attenzione: Modificando questo caso, verrà rimesso in attesa di approvazione.')
    : '';

$contenuto = strtr($contenuto, [
    '<!-- caso_titolo -->' => htmlspecialchars($caso['Titolo']),
    '<!-- feedback_message -->' => $messaggioFeedback,
    '<!-- avviso_riapprovazione -->' => $avvisoRiApprovazione,
    '<!-- value_titolo -->' => htmlspecialchars($caso['Titolo']),
    '<!-- value_data -->' => htmlspecialchars($caso['Data']),
    '<!-- value_luogo -->' => htmlspecialchars($caso['Luogo']),
    '<!-- value_descrizione -->' => htmlspecialchars($caso['Descrizione']),
    '<!-- value_storia -->' => htmlspecialchars($caso['Storia']),
    '<!-- opzioni_tipologia -->' => $opzioniTipologia,
    '<!-- hidden_caso_immagine -->' => $immagineCasoData['hidden'],
    '<!-- anteprima_immagine_caso -->' => $immagineCasoData['anteprima'],
    '<!-- vittime_html -->' => $htmlVittime,
    '<!-- colpevoli_html -->' => $htmlColpevoli,
    '<!-- articoli_html -->' => $htmlArticoli,
    '<!-- link_annulla -->' => $prefix . '/esplora/' . $caso['Slug'],
]);

echo getTemplatePage("Modifica Caso - AliceTrueCrime", $contenuto);
?>