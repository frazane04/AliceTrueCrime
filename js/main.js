document.addEventListener("DOMContentLoaded", () => {

    // Cambio Tema
    const themeSwitcher = document.querySelector(".theme-switcher");
    const themeIconLunaContainer = document.getElementById("theme-icon-luna-container");
    const themeIconSoleContainer = document.getElementById("theme-icon-sole-container");
    const magnifyingGlass = document.querySelector(".magnifying-glass");
    const body = document.body;

    const isSystemDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
    let isDarkTheme = localStorage.getItem("isDarkTheme");

    // Se non c'è preferenza salvata, usa quella di sistema
    if (isDarkTheme === null) {
        isDarkTheme = isSystemDark;
    } else {
        isDarkTheme = isDarkTheme === "true";
    }

    function applyTheme() {
        if (isDarkTheme) {
            body.classList.add("dark-theme");
            // In dark mode, show SUN (to switch to light)
            if (themeIconSoleContainer) themeIconSoleContainer.classList.remove("hidden");
            if (themeIconLunaContainer) themeIconLunaContainer.classList.add("hidden");
        } else {
            body.classList.remove("dark-theme");
            // In light mode, show MOON (to switch to dark)
            if (themeIconSoleContainer) themeIconSoleContainer.classList.add("hidden");
            if (themeIconLunaContainer) themeIconLunaContainer.classList.remove("hidden");
        }
        localStorage.setItem("isDarkTheme", isDarkTheme);
    }

    if (themeSwitcher) {
        themeSwitcher.addEventListener("click", () => {
            isDarkTheme = !isDarkTheme;
            applyTheme();
        });
    }

    applyTheme();

    // Menu Mobile
    const menuToggle = document.getElementById('menu-toggle');
    const menuClose = document.getElementById('menu-close');
    const mainNav = document.querySelector('.main-nav');

    if (menuToggle && mainNav) {

        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            const isOpen = mainNav.classList.contains('is-visible');

            if (isOpen) {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
            } else {
                mainNav.classList.add('is-visible');
                menuToggle.setAttribute('aria-expanded', 'true');
            }
        });


        if (menuClose) {
            menuClose.addEventListener('click', () => {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
            });
        }

        mainNav.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('is-menu-open');
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && mainNav.classList.contains('is-visible')) {
                mainNav.classList.remove('is-visible');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('is-menu-open');
                menuToggle.focus();
            }
        });
    }

    // Torna Su
    const backToTopButton = document.getElementById("back-to-top");

    if (backToTopButton) {
        window.addEventListener("scroll", () => {
            if (window.scrollY > 300) {
                backToTopButton.classList.add("is-visible");
            } else {
                backToTopButton.classList.remove("is-visible");
            }
        });


        const scrollToTop = (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'
            });
        };

        backToTopButton.addEventListener("click", scrollToTop);


        backToTopButton.addEventListener("keydown", (e) => {
            if (e.key === "Enter") {
                scrollToTop(e);
            }
        });
    }
});