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