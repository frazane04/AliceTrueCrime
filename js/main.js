document.addEventListener("DOMContentLoaded", () => {

    // --- LOGICA PER IL CAMBIO TEMA ---
    const themeSwitcher = document.querySelector(".theme-switcher");
    const themeIconLunaContainer = document.getElementById("theme-icon-luna-container");
    const themeIconSoleContainer = document.getElementById("theme-icon-sole-container");
    const magnifyingGlass = document.querySelector(".magnifying-glass");
    const body = document.body;

    let isDarkTheme = localStorage.getItem("isDarkTheme") === "true";

    function applyTheme() {
        if (isDarkTheme) {
            body.classList.add("dark-theme");
            // AGGIUNTO CONTROLLO IF: evita l'errore se gli elementi non esistono
            if (themeIconLunaContainer) themeIconLunaContainer.classList.remove("hidden");
            if (themeIconSoleContainer) themeIconSoleContainer.classList.add("hidden");
        } else {
            body.classList.remove("dark-theme");
            if (themeIconLunaContainer) themeIconLunaContainer.classList.add("hidden");
            if (themeIconSoleContainer) themeIconSoleContainer.classList.remove("hidden");
        }
    }

    applyTheme();

    // --- LOGICA MENU HAMBURGER MOBILE ---
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mainNav = document.querySelector('.main-nav');

    if (menuToggle && mainNav) {
        // Toggle menu (apri/chiudi)
        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = mainNav.classList.contains('is-visible');

            if (isOpen) {
                // Chiudi menu
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
            } else {
                // Apri menu
                mainNav.classList.add('is-visible');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });

        // Chiudi menu con la croce
        if (menuClose) {
            menuClose.addEventListener('click', () => {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        }

        // Chiudi menu quando si clicca un link
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('is-menu-open');
            });
        });

        // Chiudi menu con tasto Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mainNav.classList.contains('is-visible')) {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('is-menu-open');
                menuToggle.focus();
            }
        });
    }

    // --- LOGICA: PULSANTE TORNA SU (ACCESSIBILE) ---
    const backToTopButton = document.getElementById("back-to-top");

    if (backToTopButton) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add("is-visible");
            } else {
                backToTopButton.classList.remove("is-visible");
            }
        });

        // Funzione comune per lo scroll
        const scrollToTop = (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
            });
        };

        backToTopButton.addEventListener("click", scrollToTop);

        // Accessibilità: Gestione tasto Invio
        backToTopButton.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                scrollToTop(e);
            }
        });
    }
});