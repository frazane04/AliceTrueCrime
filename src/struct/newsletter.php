<?php
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/funzioni_db.php';

requireAuth();

$db = new FunzioniDB();
$utente = $db->getUtenteByEmail($_SESSION['user_email']);

if (!$utente || !isset($utente['Is_Newsletter']) || $utente['Is_Newsletter'] == 0) {
    $content = "
        <div style='text-align:center; padding:5rem;'>
            <h1>Area Riservata 🔒</h1>
            <p>I dettagli di questa sezione sono visibili solo agli iscritti. <br> 
            Attiva la newsletter nel tuo <a href='".getPrefix()."/profilo'>profilo</a> per accedere.</p>
        </div>";
} else {
    $template = loadTemplate('newsletter');

    // Recupera i dati (assicurati che la funzione getContenutiNewsletter selezioni anche la colonna 'Slug')
    $casi = $db->getContenutiNewsletter(6);
    $newsHtml = "";

    if (!empty($casi)) {
        foreach ($casi as $caso) {
            $dataFmt = date('d/m/Y', strtotime($caso['Data']));
            $descrizioneBreve = htmlspecialchars(substr($caso['Descrizione'], 0, 150)) . "...";
            
            // Usiamo $caso['Slug'] per il link invece di N_Caso
            $slug = $caso['Slug']; 
            
            $newsHtml .= "
                <article class='news-card'>
                    <div class='news-badge'>Aggiornamento</div>
                    <span class='news-date'>$dataFmt</span>
                    <h3>" . htmlspecialchars($caso['Titolo']) . "</h3>
                    <p>$descrizioneBreve</p>
                    <a href='".getPrefix()."/caso/$slug' class='btn-link'>Esamina indizi &rarr;</a>
                </article>";
        }
    } else {
        $newsHtml = "<p>Nessun aggiornamento esclusivo disponibile al momento. Torna presto!</p>";
    }

    $content = str_replace('{{USERNAME}}', htmlspecialchars($utente['Username']), $template);
    $content = str_replace('{{NEWSLETTER_CONTENT}}', $newsHtml, $content);
}

echo getTemplatePage("Newsletter Riservata", $content);