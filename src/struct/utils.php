<?php
// utils.php - Gestione logica e templating
// AGGIORNATO: Supporto per email come chiave primaria

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Gestisce il prefisso del percorso per installazioni in sottocartelle.
 */
function getPrefix(): string {
    return ''; 
}

/**
 * Funzione Core: Assembla i pezzi del template.
 */
function getTemplatePage(string $title, string $content): string {
    $templatePath = __DIR__ . '/../template/pagestructure.html';
    
    if (!file_exists($templatePath)) {
        die("ERRORE CRITICO: Template mancante in $templatePath");
    }
    
    $page = file_get_contents($templatePath);

    $header = getHeaderSection($_SERVER['REQUEST_URI']);
    $footer = getFooterSection($_SERVER['REQUEST_URI']);
    
    // Carica modal HTML
    $modalPath = __DIR__ . '/../template/modal.html';
    $modal = file_exists($modalPath) ? file_get_contents($modalPath) : '';

    $page = str_replace('{{TITOLO_PAGINA}}', $title, $page);
    $page = str_replace('{{HEADER}}', $header, $page);
    $page = str_replace('{{BREADCRUMBS}}', getBreadcrumbs($_SERVER['REQUEST_URI']), $page);
    $page = str_replace('{{CONTENT}}', $content, $page);
    $page = str_replace('{{FOOTER}}', $footer, $page);
    $page = str_replace('{{MODAL}}', $modal, $page);
    $page = str_replace('{{PATH_PREFIX}}', getPrefix(), $page);

    return $page;
}


function getHeaderSection($currentPath): string {
    $headerPath = __DIR__ . '/../template/header.html';
    if (!file_exists($headerPath)) return "<p>Errore: header.html mancante</p>";
    
    $headerHtml = file_get_contents($headerPath);
    
    $navBar = getNavBarLi($currentPath);
    $buttons = getHeaderButtons();

    $headerHtml = str_replace('{{NAVBAR}}', $navBar, $headerHtml);
    $headerHtml = str_replace('{{HEADER_BUTTONS}}', $buttons, $headerHtml);
    $headerHtml = str_replace('{{PATH_PREFIX}}', getPrefix(), $headerHtml);

    return $headerHtml;
}

function getFooterSection($currentPath): string {
    $footerPath = __DIR__ . '/../template/footer.html';
    if (!file_exists($footerPath)) return "<p>Errore: footer.html mancante</p>";

    $footerHtml = file_get_contents($footerPath);
    $navigaLinks = getFooterNavigaLi($currentPath);
    
    $footerHtml = str_replace('{{LINK_NAVIGA}}', $navigaLinks, $footerHtml);
    $footerHtml = str_replace('{{PATH_PREFIX}}', getPrefix(), $footerHtml);

    return $footerHtml;
}

/**
 * Genera i link della navbar.
 */
function getNavBarLi($currentPath): string {
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/segnala-caso' => 'Segnala Caso',
    ];

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        // RISOLUZIONE ERRORE: Carichiamo la classe FunzioniDB
        require_once __DIR__ . '/funzioni_db.php';
        $db_nav = new FunzioniDB();
        $utente = $db_nav->getUtenteByEmail($_SESSION['user_email']);
        
        // Se è iscritto, mostra il link
        if ($utente && isset($utente['Is_Newsletter']) && $utente['Is_Newsletter'] == 1) {
            $links[$prefix . '/newsletter'] = 'Newsletter';
        }
    }

    return generateLiList($links, $currentPath);
}

/**
 * Gestisce i bottoni di Login/Registrazione o Profilo Utente.
 * AGGIORNATO: Usa email dalla sessione
 */
function getHeaderButtons(): string {
    $prefix = getPrefix();
    
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        $username = htmlspecialchars($_SESSION['user'] ?? 'Utente');
        $email = htmlspecialchars($_SESSION['user_email'] ?? '');
        
        // Immagine profilo basata sullo username
        $imgProfile = "https://ui-avatars.com/api/?name=" . urlencode($username) . "&background=0D8ABC&color=fff"; 
        
        return <<<HTML
            <div class="user-menu">
                <a href="$prefix/notifiche" class="icon-btn" aria-label="Notifiche">
                    <img src="$prefix/assets/imgs/bell.svg" alt="Icona notifiche" width="20" />
                </a>

                <a href="$prefix/profilo" class="button-layout profile-btn" aria-label="Il tuo profilo" title="$email">
                    <img src="$imgProfile" alt="Avatar di $username" width="24" style="border-radius:50%; vertical-align:middle; margin-right:5px;" />
                    $username
                </a>
                <a href="$prefix/logout" class="button-layout btn-logout">Esci</a>
            </div>
        HTML;
    } else {
        return <<<HTML
            <a href="$prefix/accedi" class="button-layout">Accedi</a>
            <a href="$prefix/registrati" class="button-layout btn-dark">Registrati</a>
        HTML;
    }
}

function getFooterNavigaLi($currentPath): string {
    $prefix = getPrefix();
    $links = [
        $prefix . '/' => 'Home',
        $prefix . '/esplora' => 'Esplora Casi',
        $prefix . '/chi-siamo' => 'Chi Siamo',
        $prefix . '/privacy' => 'Privacy Policy'
    ];
    return generateLiList($links, $currentPath);
}

/**
 * Helper per generare liste <li> con classe 'activePage'.
 */
function generateLiList($links, $currentPath): string {
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
function getBreadcrumbs($currentPath): string {
    $prefix = getPrefix();
    $path = trim(parse_url($currentPath, PHP_URL_PATH), '/');
    $parts = explode('/', $path);
    
    // Inizio sempre dalla Home
    $breadcrumbs = ['<a href="' . $prefix . '/">Home</a>'];
    $accumulatedPath = $prefix;

    foreach ($parts as $part) {
        if (empty($part)) continue;
        
        $accumulatedPath .= '/' . $part;
        // Trasformiamo lo slug (es. segnala-caso) in testo leggibile (es. Segnala Caso)
        $name = ucwords(str_replace(['-', '_'], ' ', $part));
        
        // Se è l'ultima parte, non mettiamo il link
        if ($part === end($parts)) {
            $breadcrumbs[] = '<span>' . $name . '</span>';
        } else {
            $breadcrumbs[] = '<a href="' . $accumulatedPath . '">' . $name . '</a>';
        }
    }

    return '<nav aria-label="Breadcrumb" class="breadcrumbs">' . 
           implode(' <span class="separator">/</span> ', $breadcrumbs) . 
           '</nav>';
}

?>