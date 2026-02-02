/**
 * Script per la pagina modifica caso
 * Richiede: form-caso.js (utility comuni)
 */

// ========================================
// TEMPLATE HTML
// ========================================
const templates = {
    vittima: (idx) => `
        <div class="entry-card vittima-entry">
            <button type="button" class="btn-remove btn-remove-entry" aria-label="Rimuovi vittima">×</button>
            <input type="hidden" name="vittima_id[]" value="0">
            <input type="hidden" name="vittima_immagine_esistente[]" id="vittima-img-hidden-${idx}" value="">
            <div class="form-row">
                <div class="form-group"><label for="vittima-nome-${idx}">Nome *</label><input type="text" id="vittima-nome-${idx}" name="vittima_nome[]" required placeholder="Nome"></div>
                <div class="form-group"><label for="vittima-cognome-${idx}">Cognome *</label><input type="text" id="vittima-cognome-${idx}" name="vittima_cognome[]" required placeholder="Cognome"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="vittima-luogo-${idx}">Luogo Nascita</label><input type="text" id="vittima-luogo-${idx}" name="vittima_luogo_nascita[]" placeholder="Luogo"></div>
                <div class="form-group"><label for="vittima-data-nascita-${idx}">Data Nascita *</label><input type="date" id="vittima-data-nascita-${idx}" name="vittima_data_nascita[]" required></div>
                <div class="form-group"><label for="vittima-data-decesso-${idx}">Data Decesso</label><input type="date" id="vittima-data-decesso-${idx}" name="vittima_data_decesso[]"></div>
            </div>
            <div class="form-group">
                <label for="vittima-immagine-${idx}">Foto (opzionale)</label>
                <input type="file" id="vittima-immagine-${idx}" name="vittima_immagine[]" accept="image/jpeg,image/png,image/webp">
            </div>
        </div>
    `,

    colpevole: (idx) => `
        <div class="entry-card colpevole-entry">
            <button type="button" class="btn-remove btn-remove-entry" aria-label="Rimuovi colpevole">×</button>
            <input type="hidden" name="colpevole_id[]" value="0">
            <input type="hidden" name="colpevole_immagine_esistente[]" id="colpevole-img-hidden-${idx}" value="">
            <div class="form-row">
                <div class="form-group"><label for="colpevole-nome-${idx}">Nome *</label><input type="text" id="colpevole-nome-${idx}" name="colpevole_nome[]" required placeholder="Nome"></div>
                <div class="form-group"><label for="colpevole-cognome-${idx}">Cognome *</label><input type="text" id="colpevole-cognome-${idx}" name="colpevole_cognome[]" required placeholder="Cognome"></div>
            </div>
            <div class="form-row">
                <div class="form-group"><label for="colpevole-luogo-${idx}">Luogo Nascita</label><input type="text" id="colpevole-luogo-${idx}" name="colpevole_luogo_nascita[]" placeholder="Luogo"></div>
                <div class="form-group"><label for="colpevole-data-nascita-${idx}">Data Nascita *</label><input type="date" id="colpevole-data-nascita-${idx}" name="colpevole_data_nascita[]" required></div>
            </div>
            <div class="form-group">
                <label for="colpevole-immagine-${idx}">Foto (opzionale)</label>
                <input type="file" id="colpevole-immagine-${idx}" name="colpevole_immagine[]" accept="image/jpeg,image/png,image/webp">
            </div>
        </div>
    `,

    articolo: (idx) => `
        <div class="entry-card articolo-entry">
            <button type="button" class="btn-remove btn-remove-entry" aria-label="Rimuovi articolo">×</button>
            <input type="hidden" name="articolo_id[]" value="0">
            <div class="form-row">
                <div class="form-group"><label for="articolo-titolo-${idx}">Titolo Fonte</label><input type="text" id="articolo-titolo-${idx}" name="articolo_titolo[]" placeholder="Titolo"></div>
                <div class="form-group"><label for="articolo-data-${idx}">Data</label><input type="date" id="articolo-data-${idx}" name="articolo_data[]"></div>
            </div>
            <div class="form-group"><label for="articolo-link-${idx}">Link</label><input type="url" id="articolo-link-${idx}" name="articolo_link[]" placeholder="https://..."></div>
        </div>
    `
};

// Gestione Entry Dinamiche
const vittimeManager = createEntryManager({
    containerId: 'vittime-container',
    entrySelector: '.vittima-entry',
    templateFn: templates.vittima,
    initialCount: document.querySelectorAll('.vittima-entry').length,
    minEntries: 1,
    minEntriesMessage: 'Deve esserci almeno una vittima.'
});

const colpevoliManager = createEntryManager({
    containerId: 'colpevoli-container',
    entrySelector: '.colpevole-entry',
    templateFn: templates.colpevole,
    initialCount: document.querySelectorAll('.colpevole-entry').length,
    minEntries: 1,
    minEntriesMessage: 'Deve esserci almeno un colpevole.'
});

const articoliManager = createEntryManager({
    containerId: 'articoli-container',
    entrySelector: '.articolo-entry',
    templateFn: templates.articolo,
    initialCount: document.querySelectorAll('.articolo-entry').length
});

// EVENT LISTENERS
const btnAddVittima = document.getElementById('btn-add-vittima');
if (btnAddVittima) {
    btnAddVittima.addEventListener('click', () => vittimeManager.add());
}

const btnAddColpevole = document.getElementById('btn-add-colpevole');
if (btnAddColpevole) {
    btnAddColpevole.addEventListener('click', () => colpevoliManager.add());
}

const btnAddArticolo = document.getElementById('btn-add-articolo');
if (btnAddArticolo) {
    btnAddArticolo.addEventListener('click', () => articoliManager.add());
}

const btnEliminaCaso = document.getElementById('btn-elimina-caso');
if (btnEliminaCaso) {
    btnEliminaCaso.addEventListener('click', async function () {
        const confirmed = await showConfirmModal({
            title: "Elimina Caso",
            message: "ATTENZIONE: Stai per eliminare definitivamente questo caso e tutte le immagini associate. L'operazione non può essere annullata.",
            confirmText: "Elimina Definitivamente",
            confirmClass: "btn-danger"
        });

        if (confirmed) {
            document.getElementById("form-elimina-caso").submit();
        }
    });
}

// Validazione Lato Client
(function () {
    const form = document.getElementById('form-modifica-caso');
    if (!form) return;


    const titoloInput = document.getElementById('titolo');
    const dataCrimineInput = document.getElementById('data_crimine');
    const luogoInput = document.getElementById('luogo');
    const descrizioneBreveInput = document.getElementById('descrizione_breve');
    const storiaInput = document.getElementById('storia');

    // Verifica che gli elementi esistano
    if (!titoloInput || !dataCrimineInput || !luogoInput || !descrizioneBreveInput || !storiaInput) return;


    const titoloRules = [
        ValidationRules.required('Il titolo è obbligatorio'),
        ValidationRules.minLength(5, 'Il titolo deve avere almeno 5 caratteri'),
        ValidationRules.maxLength(200, 'Il titolo non può superare 200 caratteri')
    ];

    const dataRules = [
        ValidationRules.required('La data è obbligatoria'),
        ValidationRules.notFutureDate('La data non può essere nel futuro')
    ];

    const luogoRules = [
        ValidationRules.required('Il luogo è obbligatorio'),
        ValidationRules.maxLength(100, 'Il luogo non può superare 100 caratteri')
    ];

    const descrizioneBreveRules = [
        ValidationRules.required('La descrizione breve è obbligatoria'),
        ValidationRules.maxLength(500, 'La descrizione breve non può superare 500 caratteri')
    ];

    const storiaRules = [
        ValidationRules.required('La storia è obbligatoria'),
        ValidationRules.minLength(50, 'La storia deve avere almeno 50 caratteri'),
        ValidationRules.maxLength(10000, 'La storia non può superare 10.000 caratteri')
    ];

    // Validazione Blur
    attachValidation(titoloInput, titoloRules);
    attachValidation(dataCrimineInput, dataRules);
    attachValidation(luogoInput, luogoRules);
    attachValidation(descrizioneBreveInput, descrizioneBreveRules);
    attachValidation(storiaInput, storiaRules);

    // Validazione Submit
    form.addEventListener('submit', function (e) {
        const errors = [];

        if (validateField(titoloInput, titoloRules)) errors.push('Titolo');
        if (validateField(dataCrimineInput, dataRules)) errors.push('Data');
        if (validateField(luogoInput, luogoRules)) errors.push('Luogo');
        if (validateField(descrizioneBreveInput, descrizioneBreveRules)) errors.push('Descrizione breve');
        if (validateField(storiaInput, storiaRules)) errors.push('Storia');

        // Verifica almeno una vittima con nome, cognome e data nascita
        const vittimeNomi = document.querySelectorAll('input[name="vittima_nome[]"]');
        const vittimeCognomi = document.querySelectorAll('input[name="vittima_cognome[]"]');
        const vittimeDataNascita = document.querySelectorAll('input[name="vittima_data_nascita[]"]');
        let vittimaValida = false;
        let vittimaSenzaData = false;
        for (let i = 0; i < vittimeNomi.length; i++) {
            if (vittimeNomi[i].value.trim() && vittimeCognomi[i].value.trim()) {
                vittimaValida = true;
                if (!vittimeDataNascita[i].value) {
                    vittimaSenzaData = true;
                }
            }
        }
        if (!vittimaValida) {
            errors.push('Almeno una vittima con nome e cognome');
        }
        if (vittimaSenzaData) {
            errors.push('Data di nascita obbligatoria per tutte le vittime');
        }

        // Verifica almeno un colpevole con nome, cognome e data nascita
        const colpevoliNomi = document.querySelectorAll('input[name="colpevole_nome[]"]');
        const colpevoliCognomi = document.querySelectorAll('input[name="colpevole_cognome[]"]');
        const colpevoliDataNascita = document.querySelectorAll('input[name="colpevole_data_nascita[]"]');
        let colpevoleValido = false;
        let colpevoleSenzaData = false;
        for (let i = 0; i < colpevoliNomi.length; i++) {
            if (colpevoliNomi[i].value.trim() && colpevoliCognomi[i].value.trim()) {
                colpevoleValido = true;
                if (!colpevoliDataNascita[i].value) {
                    colpevoleSenzaData = true;
                }
            }
        }
        if (!colpevoleValido) {
            errors.push('Almeno un colpevole con nome e cognome');
        }
        if (colpevoleSenzaData) {
            errors.push('Data di nascita obbligatoria per tutti i colpevoli');
        }

        if (errors.length > 0) {
            e.preventDefault();

            // Mostra errori nel feedback area
            const feedbackArea = document.getElementById('feedback-area');
            if (feedbackArea) {
                feedbackArea.innerHTML = `
                    <div class="alert alert-error" role="alert">
                        <strong>Correggi i seguenti errori:</strong>
                        <ul>${errors.map(err => `<li>${err}</li>`).join('')}</ul>
                    </div>
                `;
                feedbackArea.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            // Focus sul primo campo con errore
            focusFirstError([titoloInput, dataCrimineInput, luogoInput, descrizioneBreveInput, storiaInput]);
        }
    });
})();
