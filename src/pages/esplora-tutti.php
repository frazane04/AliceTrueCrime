<?php
require_once __DIR__ . '/../db/funzioni_db.php';
require_once __DIR__ . '/../helpers/utils.php';

$dbFunctions = new FunzioniDB();

// 1. Parametri di Ricerca
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
$categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';

// 2. Esecuzione Ricerca
$filtri = ['q' => $searchQuery, 'categoria' => $categoriaFiltro];
$risultatiRicerca = $dbFunctions->cercaCasiConFiltri($filtri, 100);
$numRisultati = count($risultatiRicerca);

// Titoli per la pagina
$titoloMostrato = $searchQuery ? 'Risultati per: "' . htmlspecialchars($searchQuery) . '"' : 'Archivio Completo dei Casi';
$sottotitolo = "Sfoglia tutti i fascicoli presenti nel nostro database.";

// 3. Generazione HTML dei risultati (Card)
// Nota: usa la funzione generaHtmlCards() che è già definita in esplora.php o utils.php
$htmlRisultati = generaHtmlCards($risultatiRicerca);

// 4. Gestione AJAX per la ricerca istantanea (esplora.js)
if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    header('Content-Type: application/json');
    echo json_encode([
        'html_risultati' => $htmlRisultati,
        'num_risultati' => $numRisultati,
        'titolo_ricerca' => $titoloMostrato
    ]);
    exit;
}

// 5. Costruzione Barra di Ricerca
$searchBarHtml = loadTemplate('../components/search_bar');
$categorie = $dbFunctions->getCategorie();

$optionsCategorie = '';
foreach ($categorie as $cat) {
    $selected = ($categoriaFiltro === $cat['Tipologia']) ? 'selected' : '';
    $optionsCategorie .= '<option value="' . htmlspecialchars($cat['Tipologia']) . '" ' . $selected . '>'
        . htmlspecialchars($cat['Tipologia']) . ' (' . $cat['conteggio'] . ')</option>';
}

// Configurazione della barra per funzionare su questa pagina
$searchBarHtml = str_replace([
    '{{SEARCH_VALUE}}',
    '{{OPTIONS_CATEGORIE}}',
    '{{PREFIX}}',
    'action="' . getPrefix() . '/esplora"', // Cambia destinazione form
    'data-url="' . getPrefix() . '/esplora"', // Cambia URL per AJAX
    '{{FILTRI_ATTIVI}}'
], [
    htmlspecialchars($searchQuery),
    $optionsCategorie,
    getPrefix(),
    'action="' . getPrefix() . '/esplora-tutti"',
    'data-url="' . getPrefix() . '/esplora-tutti"',
    ''
], $searchBarHtml);

// 6. Struttura della Pagina (Usa il design che mi hai passato)
// L'ID deve essere "esplora-content" per far funzionare il Javascript esistente
$htmlPagina = '
<div class="sezione-stretta">
    <section class="explore-section">
        <div class="section-top">
            <h1>' . $titoloMostrato . '</h1>
            <p class="section-subtitle">' . $sottotitolo . '</p>
            <span class="section-badge">' . $numRisultati . ' Casi</span>
        </div>

        ' . $searchBarHtml . '

        <div id="esplora-content">
            <div id="results-grid" class="explore-grid">
                ' . $htmlRisultati . '
            </div>
        </div>
    </section>
</div>';

// 7. Rendering finale tramite il layout del sito
$contenuto = $htmlPagina;
$contenuto .= '<script src="' . getPrefix() . '/js/esplora.js"></script>';

$descrizioneSEO = "Sfoglia l'archivio completo dei casi di cronaca nera di AliceTrueCrime. Trovati $numRisultati fascicoli.";
echo getTemplatePage("Archivio Casi - AliceTrueCrime", $contenuto, $descrizioneSEO);