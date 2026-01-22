<?php
// src/struct/segnala.php
// Gestione segnalazione casi - Versione refactored con FormCasoHelper

require_once __DIR__ . '/funzioni_db.php';
require_once __DIR__ . '/ImageHandler.php';
require_once __DIR__ . '/FormCasoHelper.php';

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

    $dbFunctions = new FunzioniDB();
    $imageHandler = new ImageHandler();
    $formHelper = new FormCasoHelper($imageHandler);

    // Recupero dati generali caso
    $titolo = trim($_POST['titolo'] ?? '');
    $data = $_POST['data_crimine'] ?? '';
    $luogo = trim($_POST['luogo'] ?? '');
    $descrizione_breve = trim($_POST['descrizione_breve'] ?? '');
    $storia = trim($_POST['storia'] ?? '');
    $tipologia = trim($_POST['tipologia'] ?? '');
    $autoreEmail = $_SESSION['user_email'];

    // Parsing dati con helper
    $vittime = $formHelper->parseVittimeFromPost($_POST);
    $colpevoli = $formHelper->parseColpevoliFromPost($_POST);
    $articoli = $formHelper->parseArticoliFromPost($_POST);

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

    // ========================================
    // INSERIMENTO NEL DATABASE
    // ========================================
    if (empty($errori)) {
        try {
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
                        $immagineVittima = $formHelper->gestisciImmaginePersona(
                            $_FILES, 'vittima', $vittima['file_index'],
                            $vittima['nome'], $vittima['cognome']
                        );

                        $dbFunctions->inserisciVittima(
                            $casoId, $vittima['nome'], $vittima['cognome'],
                            $vittima['luogo_nascita'], $vittima['data_nascita'],
                            $vittima['data_decesso'], $immagineVittima
                        );
                    }

                    // 3. Inserimento colpevoli con immagini
                    foreach ($colpevoli as $colpevole) {
                        $immagineColpevole = $formHelper->gestisciImmaginePersona(
                            $_FILES, 'colpevole', $colpevole['file_index'],
                            $colpevole['nome'], $colpevole['cognome']
                        );

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
                    $messaggioFeedback = FormCasoHelper::generaMessaggioSuccessoSegnalazione(
                        $casoId,
                        count($vittime),
                        count($colpevoli),
                        count($articoli),
                        $_SESSION['user']
                    );

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
        $messaggioFeedback = FormCasoHelper::generaMessaggioErrori($errori);
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
