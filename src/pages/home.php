<?php
require_once __DIR__ . '/../helpers/utils.php';
require_once __DIR__ . '/../db/connessione.php';

$contenuto = loadTemplate('index');

$db = new ConnessioneDB();
if (!$db->apriConnessione()) {
    error_log("Errore connessione database nella home");
}


$casiInEvidenzaIDs = [13, 11, 21, 22];

$htmlCasiEvidenza = '';

if (!empty($casiInEvidenzaIDs)) {
    $idsString = implode(',', array_map('intval', $casiInEvidenzaIDs));

    $queryCasiEvidenza = "
        SELECT N_Caso, Titolo, Data, Descrizione, Slug, Immagine
        FROM Caso 
        WHERE N_Caso IN ($idsString)
        AND Approvato = 1
    ";

    $resultEvidenza = $db->query($queryCasiEvidenza);

    if ($resultEvidenza && mysqli_num_rows($resultEvidenza) > 0) {
        while ($caso = mysqli_fetch_assoc($resultEvidenza)) {
            $titolo = htmlspecialchars($caso['Titolo']);
            $descrizione = htmlspecialchars($caso['Descrizione']);
            $sinossi = (strlen($descrizione) > 100) ? substr($descrizione, 0, 100) . '...' : $descrizione;

            $htmlCasiEvidenza .= renderComponent('card-caso', [
                'CARD_CLASS' => 'carousel-card',
                'IMMAGINE' => getImageUrl($caso['Immagine'] ?? null),
                'TITOLO' => $titolo,
                'DATA' => formatData($caso['Data'] ?? null),
                'SINOSSI' => $sinossi,
                'LINK' => getPrefix() . '/esplora/' . urlencode(getSlugFromCaso($caso)),
                'TESTO_BOTTONE' => 'Scopri il Caso'
            ]);
        }
    } else {
        $htmlCasiEvidenza = "<p>Nessun caso in evidenza al momento.</p>";
    }
} else {
    $htmlCasiEvidenza = "<p>Configura i casi in evidenza modificando gli ID in home.php</p>";
}


$htmlUltimeInchieste = '';

$queryUltimeInchieste = "
    SELECT N_Caso, Titolo, Data, Descrizione, Slug
    FROM Caso 
    WHERE Approvato = 1 
    ORDER BY N_Caso DESC 
    LIMIT 3
";

$resultInchieste = $db->query($queryUltimeInchieste);

if ($resultInchieste && mysqli_num_rows($resultInchieste) > 0) {
    while ($caso = mysqli_fetch_assoc($resultInchieste)) {
        $titolo = htmlspecialchars($caso['Titolo']);
        $descrizione = htmlspecialchars($caso['Descrizione']);
        $sinossi = (strlen($descrizione) > 150) ? substr($descrizione, 0, 150) . '...' : $descrizione;

        $htmlUltimeInchieste .= renderComponent('card-caso-text', [
            'CARD_CLASS' => 'investigazione-card',
            'TITOLO' => $titolo,
            'DATA' => formatData($caso['Data'] ?? null),
            'SINOSSI' => $sinossi,
            'LINK' => getPrefix() . '/esplora/' . urlencode(getSlugFromCaso($caso)),
            'TESTO_BOTTONE' => "Leggi l'Inchiesta"
        ]);
    }
} else {
    $htmlUltimeInchieste = "<p>Nessun caso disponibile al momento.</p>";
}


$db->chiudiConnessione();

$contenuto = str_replace('{{CASI_EVIDENZA}}', $htmlCasiEvidenza, $contenuto);
$contenuto = str_replace('{{ULTIME_INCHIESTE}}', $htmlUltimeInchieste, $contenuto);

$linkNewsletter = isLoggedIn() ? getPrefix() . '/profilo#newsletter' : getPrefix() . '/accedi';
$contenuto = str_replace('{{LINK_NEWSLETTER}}', $linkNewsletter, $contenuto);

$contenuto = str_replace('{{BREADCRUMBS_HERO}}', getBreadcrumbs('/'), $contenuto);

$titoloPagina = "Home - AliceTrueCrime | Cronaca Nera e True Crime";
$descrizioneHome = "Esplora i casi di cronaca nera più famigerati in Italia. Analisi approfondite, inchieste e discussioni sulla scena del crimine e true crime italiano.";
echo getTemplatePage($titoloPagina, $contenuto, $descrizioneHome);
?>