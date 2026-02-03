<?php
// Utils - Gestione logica e templating

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login tramite cookie "ricordami"
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'], $_COOKIE['user_email'])) {
    require_once __DIR__ . '/../db/funzioni_db.php';
    $dbAutoLogin = new FunzioniDB();

    $utente = $dbAutoLogin->verificaRememberToken($_COOKIE['user_email'], $_COOKIE['remember_token']);

    if ($utente) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $utente['Username'];
        $_SESSION['user_email'] = $utente['Email'];
        $_SESSION['is_admin'] = (bool) $utente['Is_Admin'];

        $nuovoToken = bin2hex(random_bytes(32));
        $dbAutoLogin->salvaRememberToken($utente['Email'], $nuovoToken);

        $cookieExpiry = time() + (30 * 24 * 60 * 60);
        setcookie('remember_token', $nuovoToken, $cookieExpiry, '/', '', true, true);
        setcookie('user_email', $_COOKIE['user_email'], $cookieExpiry, '/', '', true, true);
    } else {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        setcookie('user_email', '', time() - 3600, '/', '', true, true);
    }
}

// Restituisce il prefisso URL per sottocartelle
function getPrefix(): string
{
    return '';
}

// Genera o restituisce il token CSRF
function generaCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Genera campo hidden con token CSRF
function csrfField(): string
{
    $token = generaCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

// Verifica validità del token CSRF
function verificaCsrfToken(): bool
{
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

// Rigenera un nuovo token CSRF
function rigeneraCsrfToken(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Reindirizza a un percorso
function redirect(string $path): void
{
    $prefix = getPrefix();
    header("Location: {$prefix}{$path}");
    exit;
}

// Genera HTML per un messaggio alert
function alertHtml(string $tipo, string $messaggio): string
{
    $classi = [
        'error' => 'alert-error',
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'info' => 'alert-info',
    ];

    $classe = $classi[$tipo] ?? 'alert-info';
    $messaggioSafe = htmlspecialchars($messaggio);

    return "<div class=\"alert {$classe}\" role=\"alert\">{$messaggioSafe}</div>";
}

// Mostra pagina errore e termina esecuzione
function renderErrorPageAndExit(string $titolo, string $messaggio, int $httpCode = 404): void
{
    $prefix = getPrefix();
    http_response_code($httpCode);

    $contenuto = renderComponent('error-page', [
        'TITOLO' => htmlspecialchars($titolo),
        'MESSAGGIO' => $messaggio,
        'LINK_HREF' => $prefix . '/esplora',
        'LINK_TESTO' => 'Esplora tutti i Casi'
    ]);

    echo getTemplatePage("$titolo - AliceTrueCrime", $contenuto);
    exit;
}

// Carica un template dalla cartella pages
function loadTemplate(string $nome): string
{
    $templatePath = __DIR__ . '/../template/pages/' . $nome . '.html';

    if (!file_exists($templatePath)) {
        die("Errore: Template {$nome}.html non trovato in {$templatePath}");
    }

    return file_get_contents($templatePath);
}

// Renderizza un componente con dati
function renderComponent(string $nome, array $dati): string
{
    $templatePath = __DIR__ . '/../template/components/' . $nome . '.html';

    if (!file_exists($templatePath)) {
        return "<!-- Componente {$nome} non trovato -->";
    }

    $html = file_get_contents($templatePath);

    foreach ($dati as $chiave => $valore) {
        $html = str_replace('{{' . $chiave . '}}', $valore, $html);
    }

    return $html;
}

// Estrae lo slug da un caso
function getSlugFromCaso(array $caso): string
{
    return $caso['Slug'] ?? strtolower(str_replace(' ', '-', $caso['Titolo']));
}

// Formatta una data nel formato specificato
function formatData(?string $data, string $formato = 'd/m/Y'): string
{
    if (empty($data)) {
        return 'Sconosciuta';
    }
    return date($formato, strtotime($data));
}

// Restituisce URL immagine con fallback placeholder
function getImageUrl(?string $immagine): string
{
    $prefix = getPrefix();
    if (!empty($immagine)) {
        return $prefix . '/' . htmlspecialchars($immagine);
    }
    return $prefix . '/assets/img/placeholder.webp';
}

// Genera HTML per lista persone (vittime/colpevoli)
function generaHtmlPersone(array $persone, string $tipo): string
{
    if (empty($persone)) {
        return $tipo === 'vittima'
            ? '<p>Nessuna vittima registrata.</p>'
            : '<p>Nessun colpevole registrato.</p>';
    }

    $html = '';
    foreach ($persone as $p) {
        $extraInfo = ($tipo === 'vittima')
            ? '<p><strong>Decesso:</strong> ' . formatData($p['DataDecesso'] ?? null) . '</p>'
            : '';

        $html .= renderComponent('card-persona', [
            'IMMAGINE' => getImageUrl($p['Immagine'] ?? null),
            'NOME_COMPLETO' => htmlspecialchars($p['Nome'] . ' ' . $p['Cognome']),
            'LUOGO_NASCITA' => htmlspecialchars($p['LuogoNascita'] ?? 'Sconosciuto'),
            'DATA_NASCITA' => formatData($p['DataNascita'] ?? null),
            'EXTRA_INFO' => $extraInfo
        ]);
    }
    return $html;
}

// Genera HTML delle card per i casi
function generaHtmlCards(array $listaCasi): string
{
    if (empty($listaCasi)) {
        return '<p class="no-results">Nessun caso trovato.</p>';
    }

    $html = '';
    $prefix = getPrefix();

    foreach ($listaCasi as $caso) {
        // Creazione sinossi per la card
        $descrizione = htmlspecialchars(substr($caso['Descrizione'] ?? '', 0, 100)) . '...';

        // Meta informazioni (Data e Luogo) per SEO locale
        $metaParts = [];
        if (!empty($caso['Data'])) {
            $metaParts[] = '<span class="card-meta-item">' . date('Y', strtotime($caso['Data'])) . '</span>';
        }
        if (!empty($caso['Luogo'])) {
            $metaParts[] = '<span class="card-meta-item">' . htmlspecialchars($caso['Luogo']) . '</span>';
        }
        $cardMeta = implode('<span class="card-meta-separator"></span>', $metaParts);

        // Accessibilità: gestione termini stranieri (WCAG AA)
        $tipologia = $caso['Tipologia'] ?? '';
        $tipologieEn = ['Serial killer', 'Cold case', 'Celebrity'];
        $tipologiaLang = in_array($tipologia, $tipologieEn) ? ' lang="en"' : '';

        // Rendering del componente card
        $html .= renderComponent('card-caso-esplora', [
            'IMMAGINE' => getImageUrl($caso['Immagine'] ?? null),
            'TITOLO' => htmlspecialchars($caso['Titolo']),
            'DESCRIZIONE' => $descrizione,
            'TIPOLOGIA' => htmlspecialchars($tipologia),
            'TIPOLOGIA_LANG' => $tipologiaLang,
            'CARD_META' => $cardMeta,
            'LINK' => $prefix . '/esplora/' . urlencode(getSlugFromCaso($caso))
        ]);
    }
    return $html;
}

// Genera HTML per lista articoli
function generaHtmlArticoli(array $articoli): string
{
    if (empty($articoli)) {
        return '<li>Nessuna fonte disponibile.</li>';
    }

    $html = '';
    foreach ($articoli as $a) {
        $html .= renderComponent('articolo-link', [
            'TITOLO' => htmlspecialchars($a['Titolo']),
            'LINK' => htmlspecialchars($a['Link']),
            'DATA' => formatData($a['Data'] ?? null)
        ]);
    }
    return $html;
}

// Genera URL avatar UI Avatars
function getAvatarUrl(string $nome, int $size = 24): string
{
    return "https://ui-avatars.com/api/?name=" . urlencode($nome) . "&background=630D16&color=fff&size=" . $size;
}

// Verifica se l'utente è autenticato
function isLoggedIn(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Richiede autenticazione o mostra messaggio
function requireAuth(bool $doRedirect = true, ?string $messaggio = null): void
{
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return; // Utente autenticato, continua
    }

    if ($doRedirect) {
        redirect('/accedi');
    }

    $prefix = getPrefix();

    // Mostra messaggio di accesso negato
    $titolo = "Accesso Negato - AliceTrueCrime";
    $contenuto = $messaggio ?? "
        <div class='access-denied-container text-center'>
            <h1>Area Riservata</h1>
            <p>Devi essere autenticato per accedere a questa pagina.</p>
            <a href='{$prefix}/accedi' class='btn btn-primary inline-block mt-1'>
                Accedi o Registrati
            </a>
        </div>
    ";
    echo getTemplatePage($titolo, $contenuto);
    exit;
}

// Mostra pagina errore HTTP
function renderErrorPage(int $codice): void
{
    $errori = [
        403 => ['titolo' => 'Accesso Negato', 'template' => '403'],
        404 => ['titolo' => 'Caso Archiviato', 'template' => '404'],
        500 => ['titolo' => 'Errore Server', 'template' => '500'],
        503 => ['titolo' => 'Servizio Non Disponibile', 'template' => '503'],
    ];

    if (!isset($errori[$codice])) {
        $codice = 500;
    }

    http_response_code($codice);

    $config = $errori[$codice];
    $errorPath = __DIR__ . '/../template/errors/' . $config['template'] . '.html';
    $contenuto = file_get_contents($errorPath);
    $contenuto = str_replace('{{PREFIX}}', getPrefix(), $contenuto);

    echo getTemplatePage("{$codice} - {$config['titolo']} | AliceTrueCrime", $contenuto);
    exit;
}

// Genera la pagina completa con template
function getTemplatePage(string $title, string $content, string $description = "", ?string $ogImage = ""): string
{
    $templatePath = __DIR__ . '/../template/layouts/pagestructure.html';

    if (!file_exists($templatePath)) {
        die("ERRORE CRITICO: Template mancante in $templatePath");
    }

    $page = file_get_contents($templatePath);
    $prefix = getPrefix();

    // 1. Gestione Meta Description per il ranking
    // Se non fornita, usa una di default per evitare contenuti vuoti
    if (empty($description)) {
        $description = "AliceTrueCrime - Il portale italiano per esplorare e discutere casi di cronaca nera.";
    }

    // 2. Gestione Immagine Social (Open Graph)
    // Se non fornita, usa il placeholder predefinito
    if (empty($ogImage)) {
        $ogImage = $prefix . '/assets/img/placeholder.webp';
    } else {
        $ogImage = $prefix . '/' . $ogImage;
    }

    // 3. Generazione URL Canonical (SEO)
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
    $fullUrl = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $header = getHeaderSection($_SERVER['REQUEST_URI']);
    $footer = getFooterSection($_SERVER['REQUEST_URI']);

    // Sostituzioni nel template
    $page = str_replace('{{TITOLO_PAGINA}}', $title, $page);
    $page = str_replace('{{META_DESCRIPTION}}', htmlspecialchars($description), $page);
    $page = str_replace('{{CANONICAL_URL}}', htmlspecialchars($fullUrl), $page);
    $page = str_replace('{{OG_IMAGE}}', htmlspecialchars($ogImage), $page);
    
    $page = str_replace('{{HEADER}}', $header, $page);

    // Breadcrumbs Logic
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (empty($path) || $path === 'home.php' || $path === 'home') {
        $page = str_replace('{{BREADCRUMBS}}', '', $page);
    } else {
        $page = str_replace('{{BREADCRUMBS}}', getBreadcrumbs($_SERVER['REQUEST_URI']), $page);
    }

    $page = str_replace('{{CONTENT}}', $content, $page);
    $page = str_replace('{{FOOTER}}', $footer, $page);
    $page = str_replace('{{PATH_PREFIX}}', $prefix, $page);

    return $page;
}

// Genera la sezione header
function getHeaderSection($currentPath): string
{
    $headerPath = __DIR__ . '/../template/layouts/header.html';
    if (!file_exists($headerPath))
        return "<p>Errore: header.html mancante</p>";

    $headerHtml = file_get_contents($headerPath);

    $navBar = getNavBarLi($currentPath);
    $buttons = getHeaderButtons();

    $headerHtml = str_replace('{{NAVBAR}}', $navBar, $headerHtml);
    $headerHtml = str_replace('{{HEADER_BUTTONS}}', $buttons, $headerHtml);
    $headerHtml = str_replace('{{PATH_PREFIX}}', getPrefix(), $headerHtml);

    return $headerHtml;
}

// Genera la sezione footer
function getFooterSection($currentPath): string
{
    $footerPath = __DIR__ . '/../template/layouts/footer.html';
    if (!file_exists($footerPath))
        return "<p>Errore: footer.html mancante</p>";

    $footerHtml = file_get_contents($footerPath);
    $navigaLinks = getFooterNavigaLi($currentPath);

    $footerHtml = str_replace('{{LINK_NAVIGA}}', $navigaLinks, $footerHtml);
    $footerHtml = str_replace('{{PATH_PREFIX}}', getPrefix(), $footerHtml);

    return $footerHtml;
}

// Genera i link della navbar
function getNavBarLi($currentPath): string
{
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/segnala-caso' => 'Segnala Caso',
        $prefix . '/about' => '<span lang="en">About</span>',
    ];

    return generateLiList($links, $currentPath);
}

// Genera i bottoni header (login/profilo)
function getHeaderButtons(): string
{
    $prefix = getPrefix();

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $username = htmlspecialchars($_SESSION['user'] ?? 'Utente');
        $email = htmlspecialchars($_SESSION['user_email'] ?? '');

        // Immagine profilo basata sullo username
        $imgProfile = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=630D16&color=fff";

        return <<<HTML
            <div class="user-menu">
                <a href="$prefix/profilo" class="button-layout profile-btn" aria-label="$username - Vai al profilo" title="$email">
                    <img src="$imgProfile" alt="Avatar di $username" width="24" class="avatar-small" />
                    $username
                </a>
                <a href="$prefix/logout" class="button-layout btn-logout">Esci</a>
            </div>
        HTML;
    } else {
        return <<<HTML
            <a href="$prefix/accedi" class="button-layout">Accedi</a>
        HTML;
    }
}

// Genera i link di navigazione footer
function getFooterNavigaLi($currentPath): string
{
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/segnala-caso' => 'Segnala Caso',
        $prefix . '/about' => '<span lang="en">About</span>',
    ];
    return generateLiList($links, $currentPath);
}

// Genera lista di link <li>
function generateLiList($links, $currentPath): string
{
    $html = '';
    foreach ($links as $url => $label) {
        $isActive = ($currentPath == $url) ? 'class="activePage" aria-current="page"' : '';
        $html .= "<li $isActive><a href=\"$url\">$label</a></li>";
    }
    return $html;
}

// Genera i breadcrumbs della pagina
function getBreadcrumbs($currentPath): string
{
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = ($scriptDir === '/' || $scriptDir === '\\') ? '' : $scriptDir;

    $relativePath = (strpos($currentPath, $basePath) === 0) ? substr($currentPath, strlen($basePath)) : $currentPath;
    $path = trim(parse_url($relativePath, PHP_URL_PATH), '/');

    if (empty($path) || $path === 'home.php' || $path === 'index.php') {
        return '<nav aria-label="Breadcrumb" class="breadcrumbs"><span aria-current="page">Home</span></nav>';
    }

    $breadcrumbs = ['<a href="' . ($basePath ?: '/') . '">Home</a>'];

    // LOGICA SPECIALE: Esplora Tutti deve avere come genitore Esplora
    if ($path === 'esplora-tutti') {
        $breadcrumbs[] = '<a href="' . getPrefix() . '/esplora">Esplora</a>';
        $breadcrumbs[] = '<span aria-current="page">Esplora Tutti</span>';
        
        return '<nav aria-label="Breadcrumb" class="breadcrumbs">' .
            implode(' <span class="separator">/</span> ', $breadcrumbs) .
            '</nav>';
    }
    //LOGICA SPECIALE: Modifica Profilo
    if ($path === 'modifica-profilo' || $path === 'modificaprofilo.php') {
        $breadcrumbs[] = '<a href="' . getPrefix() . '/profilo">Profilo</a>';
        $breadcrumbs[] = '<span aria-current="page">Modifica Profilo</span>';
        
        return '<nav aria-label="Breadcrumb" class="breadcrumbs">' .
            implode(' <span class="separator">/</span> ', $breadcrumbs) .
            '</nav>';
    }

    $parts = explode('/', $path);
    $accumulatedPath = $basePath;

    foreach ($parts as $index => $part) {
        // FILTRO: Salta le cartelle fisiche del server per evitare il 404
        if ($part === '' || $part === 'src' || $part === 'pages') continue;
        
        $accumulatedPath .= '/' . $part;
        $name = ($part === 'about') ? '<span lang="en">About</span>' : ucwords(str_replace(['-', '_'], ' ', $part));

        if ($index === count($parts) - 1) {
            $breadcrumbs[] = '<span aria-current="page">' . $name . '</span>';
        } else {
            $breadcrumbs[] = '<a href="' . $accumulatedPath . '">' . $name . '</a>';
        }
    }

    return '<nav aria-label="Breadcrumb" class="breadcrumbs">' .
        implode(' <span class="separator">/</span> ', $breadcrumbs) .
        '</nav>';
}

?>