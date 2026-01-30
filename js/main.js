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
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-nav');
    // const body = document.body; // RIMOSSO: Già dichiarato all'inizio della funzione

    if (menuToggle && mainNav) {
        // REMOVED DEBUG ALERT
        console.log("Menu toggle found, attaching listener");

        menuToggle.addEventListener('click', (e) => {
            e.preventDefault(); // Evita submit form se presente o scroll strani
            e.stopPropagation();

            console.log("CLICK! Toggling menu...");

            mainNav.classList.toggle('is-visible');

            const isExpanded = mainNav.classList.contains('is-visible');
            menuToggle.setAttribute('aria-expanded', isExpanded);

            // Toggle Scroll Lock
            document.body.classList.toggle('is-menu-open', isExpanded);
        });

        // Chiudi menu quando si clicca un link
        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.setAttribute('aria-expanded', 'false');
                mainNav.classList.remove('is-visible');
                body.classList.remove('is-menu-open');
            });
        });
    }

    // Gestione click tema (solo se gli elementi esistono nella pagina)
    if (themeSwitcher && magnifyingGlass) {
        themeSwitcher.addEventListener("click", () => {
            if (magnifyingGlass.classList.contains("animate-sweep")) {
                return;
            }

            magnifyingGlass.classList.add("animate-sweep");

            setTimeout(() => {
                isDarkTheme = !isDarkTheme;
                localStorage.setItem("isDarkTheme", isDarkTheme);
                applyTheme();
            }, 500);
        });

        magnifyingGlass.addEventListener('animationend', () => {
            magnifyingGlass.classList.remove("animate-sweep");
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