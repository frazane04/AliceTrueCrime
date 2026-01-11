<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/funzioni_db.php';

if (!isset($_SESSION['logged_in'])) {
    header('Location: ' . getPrefix() . '/accedi');
    exit;
}

$db = new FunzioniDB();
$email = $_SESSION['user_email'];

// --- GESTIONE LOGICA BOTTONE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_newsletter') {
    $statoAttuale = (int)$_POST['current_status'];
    $nuovoStato = ($statoAttuale === 1) ? 0 : 1; // Inverte lo stato
    $db->updateNewsletter($email, $nuovoStato);
}

// Recupero dati aggiornati
$utente = $db->getUtenteByEmail($email);
$is_iscritti = (int)($utente['Is_Newsletter'] ?? 0);

if ($is_iscritti === 1) {
    $titoloSezione = "Sei iscritto alla Newsletter ✅";
    $testoBottone = "Disiscrivimi dalla Newsletter";
    $btnClass = "btn-danger"; // Rosso per disiscrivere
} else {
    $titoloSezione = "Newsletter Riservata 📩";
    $testoBottone = "Iscriviti alla Newsletter";
    $btnClass = "btn-secondary"; // Colore standard
}

$newsletterHtml = "
    <section class='content-block newsletter-section'>
        <div class='block-header'>
            <h2>$titoloSezione</h2>
        </div>
        <form method='POST' action=''>
            <input type='hidden' name='action' value='toggle_newsletter'>
            <input type='hidden' name='current_status' value='$is_iscritti'>
            <p>Ricevi aggiornamenti esclusivi direttamente nella tua email.</p>
            <button type='submit' class='btn btn-pill $btnClass' style='border:none; cursor:pointer;'>$testoBottone</button>
        </form>
    </section>";

// Caricamento Template
$templatePath = __DIR__ . '/../template/pagineutente.html';
$html = file_get_contents($templatePath);

$html = str_replace('{{USERNAME}}', htmlspecialchars($utente['Username']), $html);
$html = str_replace('{{EMAIL}}', htmlspecialchars($utente['Email']), $html);
$html = str_replace('{{NEWSLETTER_SECTION}}', $newsletterHtml, $html);

echo getTemplatePage("Profilo - AliceTrueCrime", $html);