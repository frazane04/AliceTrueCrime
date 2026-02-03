<?php
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/funzioni_db.php';

requireAuth();

$db = new FunzioniDB();
$email = $_SESSION['user_email'];
$prefix = getPrefix();

// Newsletter Toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_newsletter') {
    $statoAttuale = (int) $_POST['current_status'];
    $nuovoStato = ($statoAttuale === 1) ? 0 : 1;
    $db->updateNewsletter($email, $nuovoStato);
}

$utente = $db->getUtenteByEmail($email);
$is_iscritti = (int) ($utente['Is_Newsletter'] ?? 0);
$is_admin = (bool) ($utente['Is_Admin'] ?? false);

if ($is_iscritti === 1) {
    $titoloSezione = "Sei iscritto alla Newsletter";
    $testoBottone = "Disiscrivimi dalla Newsletter";
    $btnClass = "btn-danger";
} else {
    $titoloSezione = "Newsletter Riservata";
    $testoBottone = "Iscriviti alla Newsletter";
    $btnClass = "btn-secondary";
}

$newsletterHtml = "
    <section id='newsletter' class='content-block newsletter-section'>
        <div class='block-header'>
            <h2>$titoloSezione</h2>
        </div>
        <form method='POST' action=''>
            <input type='hidden' name='action' value='toggle_newsletter'>
            <input type='hidden' name='current_status' value='$is_iscritti'>
            <p>Ricevi aggiornamenti esclusivi direttamente nella tua email.</p>
            <button type='submit' class='btn btn-pill $btnClass'>$testoBottone</button>
        </form>
    </section>";

// Sezione Admin
$adminHtml = '';

if ($is_admin) {
    $casiInAttesa = $db->getCasiNonApprovati(50);
    $numeroCasiAttesa = count($casiInAttesa);

    $adminHtml = "
    <section class='content-block admin-section'>
        <div class='block-header'>
            <h2>Casi da Approvare</h2>
            <span class='badge-admin'>$numeroCasiAttesa in attesa</span>
        </div>";

    if (empty($casiInAttesa)) {
        $adminHtml .= "
        <p class='empty-state'>Nessun caso in attesa di approvazione!</p>";
    } else {
        $adminHtml .= "<ul class='casi-pending-list'>";

        foreach ($casiInAttesa as $caso) {
            $titolo = htmlspecialchars($caso['Titolo']);
            $slug = htmlspecialchars($caso['Slug']);
            $dataInserimento = date('d/m/Y', strtotime($caso['Data_Inserimento']));
            $tipologia = htmlspecialchars($caso['Tipologia'] ?? 'Non specificata');

            $adminHtml .= "
            <li class='caso-pending-item'>
                <a href='$prefix/esplora/$slug?preview=admin&from=profilo'>
                    <span class='caso-pending-title'>$titolo</span>
                    <span class='caso-pending-info'>$tipologia • $dataInserimento</span>
                </a>
            </li>";
        }

        $adminHtml .= "</ul>";
    }

    $adminHtml .= "</section>";
}

$html = loadTemplate('pagineutente');

$html = str_replace('{{USERNAME}}', htmlspecialchars($utente['Username']), $html);
$html = str_replace('{{EMAIL}}', htmlspecialchars($utente['Email']), $html);
$html = str_replace('{{NEWSLETTER_SECTION}}', $newsletterHtml, $html);
$html = str_replace('{{ADMIN_SECTION}}', $adminHtml, $html);

echo getTemplatePage("Profilo - AliceTrueCrime", $html);