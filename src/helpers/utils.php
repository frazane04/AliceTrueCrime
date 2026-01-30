<?php
// utils.php - Gestione logica e templating
// AGGIORNATO: Supporto per email come chiave primaria

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Auto-login tramite cookie "Ricordami"
// SICUREZZA: Verifica che il token nel cookie corrisponda al token hashato nel database
if (!isset($_SESSION['logged_in']) && isset($_COOKIE['remember_token'], $_COOKIE['user_email'])) {
    require_once __DIR__ . '/../db/funzioni_db.php';
    $dbAutoLogin = new FunzioniDB();

    // Verifica il token contro il database (non solo l'email!)
    $utente = $dbAutoLogin->verificaRememberToken($_COOKIE['user_email'], $_COOKIE['remember_token']);

    if ($utente) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user'] = $utente['Username'];
        $_SESSION['user_email'] = $utente['Email'];
        $_SESSION['is_admin'] = (bool)$utente['Is_Admin'];

        // Genera un nuovo token e aggiorna (rotation per sicurezza)
        $nuovoToken = bin2hex(random_bytes(32));
        $dbAutoLogin->salvaRememberToken($utente['Email'], $nuovoToken);

        // Rinnova i cookie per altri 30 giorni con il nuovo token
        $cookieExpiry = time() + (30 * 24 * 60 * 60);
        setcookie('remember_token', $nuovoToken, $cookieExpiry, '/', '', true, true);
        setcookie('user_email', $_COOKIE['user_email'], $cookieExpiry, '/', '', true, true);
    } else {
        // Token non valido, rimuovi i cookie
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
        setcookie('user_email', '', time() - 3600, '/', '', true, true);
    }
}

/**
 * Gestisce il prefisso del percorso per installazioni in sottocartelle.
 */
function getPrefix(): string
{
    return '';
}

// ========================================
// PROTEZIONE CSRF
// ========================================

/**
 * Genera un token CSRF e lo salva in sessione
 */
function generaCsrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Restituisce l'input HTML hidden per il token CSRF
 */
function csrfField(): string
{
    $token = generaCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}

/**
 * Verifica che il token CSRF sia valido
 */
function verificaCsrfToken(): bool
{
    if (empty($_POST['csrf_token']) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

/**
 * Rigenera il token CSRF (da usare dopo un'azione riuscita)
 */
function rigeneraCsrfToken(): void
{
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * Esegue un redirect con il prefix automatico.
 *
 * @param string $path Percorso relativo (es. '/profilo', '/accedi')
 */
function redirect(string $path): void
{
    $prefix = getPrefix();
    header("Location: {$prefix}{$path}");
    exit;
}

/**
 * Genera l'HTML per un messaggio di alert.
 *
 * @param string $tipo Tipo di alert ('error', 'success', 'warning', 'info')
 * @param string $messaggio Messaggio da mostrare
 * @return string HTML dell'alert
 */
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

/**
 * Mostra una pagina di errore generica e termina l'esecuzione.
 *
 * @param string $titolo Titolo dell'errore
 * @param string $messaggio Messaggio descrittivo
 * @param int $httpCode Codice HTTP (default 404)
 */
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

/**
 * Carica un template HTML dalla cartella template.
 *
 * @param string $nome Nome del template (senza .html)
 * @return string Contenuto del template
 */
function loadTemplate(string $nome): string
{
    $templatePath = __DIR__ . '/../template/pages/' . $nome . '.html';

    if (!file_exists($templatePath)) {
        die("Errore: Template {$nome}.html non trovato in {$templatePath}");
    }

    return file_get_contents($templatePath);
}

/**
 * Carica un template componente e sostituisce i placeholder.
 *
 * @param string $nome Nome del componente (es. 'card-caso')
 * @param array $dati Array associativo chiave => valore per i placeholder
 * @return string HTML con placeholder sostituiti
 */
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

/**
 * Genera lo slug da un caso (fallback se non presente).
 */
function getSlugFromCaso(array $caso): string
{
    return $caso['Slug'] ?? strtolower(str_replace(' ', '-', $caso['Titolo']));
}

/**
 * Formatta una data nel formato italiano.
 */
function formatData(?string $data, string $formato = 'd/m/Y'): string
{
    if (empty($data)) {
        return 'Sconosciuta';
    }
    return date($formato, strtotime($data));
}

/**
 * Restituisce l'URL dell'immagine o un placeholder.
 */
function getImageUrl(?string $immagine): string
{
    $prefix = getPrefix();
    if (!empty($immagine)) {
        return $prefix . '/' . htmlspecialchars($immagine);
    }
    return $prefix . '/assets/img/placeholder.webp';
}

/**
 * Genera HTML per lista persone (vittime o colpevoli) usando card-persona.
 */
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

/**
 * Genera HTML per lista articoli/fonti usando articolo-link.
 */
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

/**
 * Genera l'URL dell'avatar UI Avatars.
 */
function getAvatarUrl(string $nome, int $size = 24): string
{
    return "https://ui-avatars.com/api/?name=" . urlencode($nome) . "&background=0D8ABC&color=fff&size=" . $size;
}

/**
 * Verifica se l'utente è loggato.
 */
function isLoggedIn(): bool
{
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Verifica che l'utente sia autenticato.
 * Se non lo è, reindirizza alla pagina di login o mostra un messaggio.
 *
 * @param bool $redirect Se true reindirizza, se false mostra messaggio e termina
 * @param string|null $messaggio Messaggio personalizzato (solo se $redirect = false)
 * @return void
 */
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

/**
 * Mostra una pagina di errore HTTP.
 *
 * @param int $codice Codice HTTP (403, 404, 500, 503)
 */
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

/**
 * Funzione Core: Assembla i pezzi del template.
 */
function getTemplatePage(string $title, string $content): string
{
    $templatePath = __DIR__ . '/../template/layouts/pagestructure.html';

    if (!file_exists($templatePath)) {
        die("ERRORE CRITICO: Template mancante in $templatePath");
    }

    $page = file_get_contents($templatePath);

    $header = getHeaderSection($_SERVER['REQUEST_URI']);
    $footer = getFooterSection($_SERVER['REQUEST_URI']);

    $page = str_replace('{{TITOLO_PAGINA}}', $title, $page);
    $page = str_replace('{{HEADER}}', $header, $page);

    // Breadcrumbs Logic: If Home, hide global breadcrumbs (injected manually in Hero)
    $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
    if (empty($path) || $path === 'home.php' || $path === 'home') {
        $page = str_replace('{{BREADCRUMBS}}', '', $page);
    } else {
        $page = str_replace('{{BREADCRUMBS}}', getBreadcrumbs($_SERVER['REQUEST_URI']), $page);
    }

    $page = str_replace('{{CONTENT}}', $content, $page);
    $page = str_replace('{{FOOTER}}', $footer, $page);
    $page = str_replace('{{PATH_PREFIX}}', getPrefix(), $page);

    return $page;
}


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

/**
 * Genera i link della navbar.
 */
function getNavBarLi($currentPath): string
{
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/segnala-caso' => 'Segnala Caso',
        $prefix . '/chi-siamo' => 'Chi Siamo',
    ];

    return generateLiList($links, $currentPath);
}

/**
 * Gestisce i bottoni di Login/Registrazione o Profilo Utente.
 * AGGIORNATO: Usa email dalla sessione
 */
function getHeaderButtons(): string
{
    $prefix = getPrefix();

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $username = htmlspecialchars($_SESSION['user'] ?? 'Utente');
        $email = htmlspecialchars($_SESSION['user_email'] ?? '');

        // Immagine profilo basata sullo username
        $imgProfile = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=7A3E26&color=fff";

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

function getFooterNavigaLi($currentPath): string
{
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/chi-siamo' => 'Chi Siamo'
    ];
    return generateLiList($links, $currentPath);
}

/**
 * Helper per generare liste <li> con classe 'activePage'.
 */
function generateLiList($links, $currentPath): string
{
    $html = '';
    foreach ($links as $url => $label) {
        $isActive = ($currentPath == $url) ? 'class="activePage" aria-current="page"' : '';
        $html .= "<li $isActive><a href=\"$url\">$label</a></li>";
    }
    return $html;
}

/**
 * Genera le breadcrumbs in base al percorso attuale.
 */
function getBreadcrumbs($currentPath): string
{
    $prefix = getPrefix();
    $path = trim(parse_url($currentPath, PHP_URL_PATH), '/');

    // Se siamo nella Home Page (nessun percorso oltre al prefisso)
    if (empty($path)) {
        return '<nav aria-label="Breadcrumb" class="breadcrumbs"><span aria-current="page">Home</span></nav>';
    }

    // Iniziamo con Home come link perché non siamo nella home
    $breadcrumbs = ['<a href="' . $prefix . '/">Home</a>'];

    $parts = explode('/', $path);
    $accumulatedPath = $prefix;
    $totalParts = count($parts);

    // Usiamo $index per identificare con certezza l'ultimo elemento
    foreach ($parts as $index => $part) {
        if ($part === '')
            continue;

        $accumulatedPath .= '/' . $part;
        // Trasformiamo lo slug (es. segnala-caso) in testo leggibile (es. Segnala Caso)
        $name = ucwords(str_replace(['-', '_'], ' ', $part));

        // Se è l'ultimo elemento dell'array, è la pagina corrente
        if ($index === $totalParts - 1) {
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