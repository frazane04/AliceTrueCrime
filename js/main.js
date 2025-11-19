document.addEventListener("DOMContentLoaded", () => {
    
    // --- Logica per il bottone di prova "Vai al Gioco" ---
    const button = document.getElementById("myButton");
    if (button) {
        button.addEventListener("click", () => {
            alert("Vai al Gioco!");
        });
    }

    // --- LOGICA PER IL CAMBIO TEMA ---

    // Elementi del DOM
    const themeSwitcher = document.querySelector(".theme-switcher");
    const themeIconLunaContainer = document.getElementById("theme-icon-luna-container");
    const themeIconSoleContainer = document.getElementById("theme-icon-sole-container");
    const magnifyingGlass = document.querySelector(".magnifying-glass");
    const body = document.body;

    // Stato tema (recupera dal localStorage)
    let isDarkTheme = localStorage.getItem("isDarkTheme") === "true";

    // Funzione per applicare il tema e mostrare/nascondere le icone (sole/luna)
    function applyTheme() {
        if (isDarkTheme) {
            body.classList.add("dark-theme");
            themeIconLunaContainer.classList.remove("hidden"); // Mostra luna (scuro)
            themeIconSoleContainer.classList.add("hidden");    // Nascondi sole (chiaro)
        } else {
            body.classList.remove("dark-theme");
            themeIconLunaContainer.classList.add("hidden");      // Nascondi luna
            themeIconSoleContainer.classList.remove("hidden"); // Mostra sole
        }
    }

    // Applica il tema al caricamento della pagina
    applyTheme();

    // Gestione click sul selettore tema
    themeSwitcher.addEventListener("click", () => {
        
        // Blocca se l'animazione è già in corso
        if (magnifyingGlass.classList.contains("animate-sweep")) {
            return; 
        }

        // Avvia l'animazione
        magnifyingGlass.classList.add("animate-sweep");

        // Cambia tema ESATTAMENTE a metà animazione (500ms)
        setTimeout(() => {
            isDarkTheme = !isDarkTheme; // Inverti lo stato
            localStorage.setItem("isDarkTheme", isDarkTheme); // Salva in locale
            applyTheme(); // Applica il nuovo tema
        }, 500); 
    });

    // Rimuove la classe di animazione al termine per consentire il riavvio
    magnifyingGlass.addEventListener('animationend', () => {
        magnifyingGlass.classList.remove("animate-sweep");
    });
});