document.addEventListener("DOMContentLoaded", () => {
    console.log("JavaScript collegato correttamente!");

    const button = document.getElementById("myButton");
    if (button) {
        button.addEventListener("click", () => {
            alert("Hai cliccato il bottone! 🎉");
        });
    }

    // --- LOGICA PER IL CAMBIO TEMA E L'ANIMAZIONE LENTE ---
    const themeSwitcher = document.querySelector(".theme-switcher");
    const themeIconLuna = document.getElementById("theme-icon-luna");
    const themeIconSole = document.getElementById("theme-icon-sole");
    const magnifyingGlass = document.querySelector(".magnifying-glass");
    const body = document.body;

    // Recupera il tema salvato o imposta quello predefinito
    let isDarkTheme = localStorage.getItem("isDarkTheme") === "true";

    // Funzione per applicare il tema
    function applyTheme() {
        if (isDarkTheme) {
            body.classList.add("dark-theme");
            themeIconLuna.classList.remove("hidden");
            themeIconSole.classList.add("hidden");
        } else {
            body.classList.remove("dark-theme");
            themeIconLuna.classList.add("hidden");
            themeIconSole.classList.remove("hidden");
        }
    }

    // Applica il tema all'inizio
    applyTheme();

    // Event listener per il click sul selettore tema
    themeSwitcher.addEventListener("click", () => {
        // Rimuovi eventuali classi di animazione precedenti
        magnifyingGlass.classList.remove("animate-left", "animate-right");

        // Determina l'animazione in base al tema corrente
        if (isDarkTheme) { // Se è scuro, passeremo a chiaro (sole), animazione destra
            magnifyingGlass.classList.add("animate-right");
        } else { // Se è chiaro, passeremo a scuro (luna), animazione sinistra
            magnifyingGlass.classList.add("animate-left");
        }

        // Dopo l'animazione, cambia il tema e l'icona
        // Usiamo un timeout per far finire l'animazione prima di cambiare icona
        setTimeout(() => {
            isDarkTheme = !isDarkTheme; // Inverti il tema
            localStorage.setItem("isDarkTheme", isDarkTheme); // Salva la preferenza
            applyTheme(); // Applica il nuovo tema e cambia le icone
        }, 600); // Questo tempo dovrebbe essere leggermente inferiore alla durata dell'animazione (0.8s = 800ms)
                 // per far sì che il cambio icona avvenga "durante" il ritorno al centro della lente.
    });

    // Opzionale: Rimuovi la classe di animazione alla fine dell'animazione CSS
    // Questo è utile se volessi che l'animazione potesse ripartire più volte di seguito
    magnifyingGlass.addEventListener('animationend', () => {
        magnifyingGlass.classList.remove("animate-left", "animate-right");
    });
});