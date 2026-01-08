<?php
// src/struct/home.php
// Gestione della Home Page con dati dinamici

require_once __DIR__ . '/connessione.php';

// 1. Carica il template HTML
$templatePath = __DIR__ . '/../template/index.html';

if (!file_exists($templatePath)) {
    $contenuto = "
        <div style='padding: 2rem; text-align: center; color: red;'>
            <h1>Errore Critico</h1>
            <p>Template non trovato: <code>$templatePath</code></p>
        </div>
    ";
    echo getTemplatePage("Errore - AliceTrueCrime", $contenuto);
    exit;
}

$contenuto = file_get_contents($templatePath);

// 2. Connessione Database
$db = new ConnessioneDB();
if (!$db->apriConnessione()) {
    error_log("Errore connessione database nella home");
}

// ========================================
// 3. CASI IN EVIDENZA (scelti manualmente)
// ========================================
$casiInEvidenzaIDs = [1, 5, 12, 8]; 

$htmlCasiEvidenza = '';

if (!empty($casiInEvidenzaIDs)) {
    // Crea la stringa per IN clause (es: "1,5,12,8")
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
            $data = date('d/m/Y', strtotime($caso['Data']));
            
            // Descrizione breve
            $descrizione = htmlspecialchars($caso['Descrizione']);
            $sinossi = (strlen($descrizione) > 100) 
                ? substr($descrizione, 0, 100) . '...' 
                : $descrizione;
            
            // Slug per URL
            $slug = $caso['Slug'] ?? strtolower(str_replace(' ', '-', $caso['Titolo']));
            $linkCaso = getPrefix() . '/caso/' . urlencode($slug);
            
            // Immagine (usa placeholder se non esiste)
            $immagine = $caso['Immagine'] 
                ? getPrefix() . '/' . htmlspecialchars($caso['Immagine'])
                : getPrefix() . '/assets/imgs/copertina-placeholder.jpg';
            
            $htmlCasiEvidenza .= "
            <article class='carousel-card'>
                <img src='{$immagine}' alt='Copertina: {$titolo}' />
                <h3>{$titolo}</h3>
                <p>{$data}</p>
                <p class='caso-sinossi'>{$sinossi}</p>
                <a href='{$linkCaso}' class='btn btn-primary btn-small' 
                   aria-label='Leggi il caso: {$titolo}'>
                    Scopri il Caso
                </a>
            </article>
            ";
        }
    } else {
        $htmlCasiEvidenza = "<p>Nessun caso in evidenza al momento.</p>";
    }
} else {
    $htmlCasiEvidenza = "<p>Configura i casi in evidenza modificando gli ID in home.php</p>";
}

// ========================================
// 4. ULTIME INCHIESTE (ultimi 3 per ID)
// ========================================
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
        $data = date('d/m/Y', strtotime($caso['Data']));
        
        $descrizione = htmlspecialchars($caso['Descrizione']);
        $sinossi = (strlen($descrizione) > 150) 
            ? substr($descrizione, 0, 150) . '...' 
            : $descrizione;
        
        $slug = $caso['Slug'] ?? strtolower(str_replace(' ', '-', $caso['Titolo']));
        $linkCaso = getPrefix() . '/caso/' . urlencode($slug);
        
        
        $htmlUltimeInchieste .= "
        <article class='investigazione-card'>
            <h3>{$titolo}</h3>
            <p class='data-caso'>{$data}</p>
            <p>{$sinossi}</p>
            <a href='{$linkCaso}' class='btn btn-primary btn-small' 
               aria-label='Leggi il caso: {$titolo}'>
                Leggi l'Inchiesta
            </a>
        </article>
        ";
    }
} else {
    $htmlUltimeInchieste = "<p>Nessun caso disponibile al momento.</p>";
}

// ========================================
// 5. ARTICOLI PIÙ LETTI (top 3 per visualizzazioni)
// ========================================
$htmlArticoliPopolari = '';

$queryArticoliPopolari = "
    SELECT N_Caso, Titolo, Slug, Visualizzazioni 
    FROM Caso 
    WHERE Approvato = 1 
    AND Visualizzazioni > 0
    ORDER BY Visualizzazioni DESC 
    LIMIT 3
";

$resultPopolari = $db->query($queryArticoliPopolari);

if ($resultPopolari && mysqli_num_rows($resultPopolari) > 0) {
    $htmlArticoliPopolari .= "<ul>";
    while ($articolo = mysqli_fetch_assoc($resultPopolari)) {
        $titolo = htmlspecialchars($articolo['Titolo']);
        $slug = $articolo['Slug'] ?? strtolower(str_replace(' ', '-', $articolo['Titolo']));
        $linkArticolo = getPrefix() . '/caso/' . urlencode($slug);
        
        $htmlArticoliPopolari .= "
            <li>
                <a href='{$linkArticolo}' aria-label='Leggi: {$titolo}'>
                    {$titolo}
                </a>
            </li>
        ";
    }
    $htmlArticoliPopolari .= "</ul>";
} else {
    $htmlArticoliPopolari = "<p>Nessun articolo popolare disponibile.</p>";
}

// Chiudo connessione
$db->chiudiConnessione();

// ========================================
// 6. SOSTITUISCO I PLACEHOLDER
// ========================================
$contenuto = str_replace('{{CASI_EVIDENZA}}', $htmlCasiEvidenza, $contenuto);
$contenuto = str_replace('{{ULTIME_INCHIESTE}}', $htmlUltimeInchieste, $contenuto);
$contenuto = str_replace('{{ARTICOLI_POPOLARI}}', $htmlArticoliPopolari, $contenuto);

// ========================================
// 7. OUTPUT FINALE
// ========================================
$titoloPagina = "Home - AliceTrueCrime | Cronaca Nera e True Crime";
echo getTemplatePage($titoloPagina, $contenuto);
?>