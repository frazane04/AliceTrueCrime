// Script pagina segnala caso

const templates = {
    vittima: (index) => `
    <div class="persona-entry vittima-entry" data-index="${index}">
        <div class="entry-header">
            <h4>Vittima #${index + 1}</h4>
            <button type="button" class="btn-remove-entry" aria-label="Rimuovi vittima">Rimuovi</button>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="vittima_nome_${index}">Nome *</label>
                <input type="text" id="vittima_nome_${index}" name="vittima_nome[]" placeholder="Nome" required maxlength="50"/>
            </div>
            <div class="form-group half">
                <label for="vittima_cognome_${index}">Cognome *</label>
                <input type="text" id="vittima_cognome_${index}" name="vittima_cognome[]" placeholder="Cognome" required maxlength="50"/>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="vittima_luogo_nascita_${index}">Luogo di Nascita</label>
                <input type="text" id="vittima_luogo_nascita_${index}" name="vittima_luogo_nascita[]" placeholder="Città, Paese" maxlength="100"/>
            </div>
            <div class="form-group half">
                <label for="vittima_data_nascita_${index}">Data di Nascita *</label>
                <input type="date" id="vittima_data_nascita_${index}" name="vittima_data_nascita[]" required/>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="vittima_data_decesso_${index}">Data di Decesso</label>
                <input type="date" id="vittima_data_decesso_${index}" name="vittima_data_decesso[]"/>
            </div>
            <div class="form-group half">
                <label for="vittima_immagine_${index}">Foto (opzionale)</label>
                <input type="file" id="vittima_immagine_${index}" name="vittima_immagine[]" accept="image/jpeg,image/png,image/webp" data-preview-target="preview-vittima-${index}"/>
            </div>
        </div>
        <div id="preview-vittima-${index}" class="image-preview image-preview-small" aria-live="polite"></div>
    </div>
    `,

    colpevole: (index) => `
    <div class="persona-entry colpevole-entry" data-index="${index}">
        <div class="entry-header">
            <h4>Colpevole #${index + 1}</h4>
            <button type="button" class="btn-remove-entry" aria-label="Rimuovi colpevole">Rimuovi</button>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="colpevole_nome_${index}">Nome *</label>
                <input type="text" id="colpevole_nome_${index}" name="colpevole_nome[]" placeholder="Nome (o 'Ignoto')" required maxlength="50"/>
            </div>
            <div class="form-group half">
                <label for="colpevole_cognome_${index}">Cognome *</label>
                <input type="text" id="colpevole_cognome_${index}" name="colpevole_cognome[]" placeholder="Cognome (o 'X')" required maxlength="50"/>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="colpevole_luogo_nascita_${index}">Luogo di Nascita</label>
                <input type="text" id="colpevole_luogo_nascita_${index}" name="colpevole_luogo_nascita[]" placeholder="Città, Paese" maxlength="100"/>
            </div>
            <div class="form-group half">
                <label for="colpevole_data_nascita_${index}">Data di Nascita *</label>
                <input type="date" id="colpevole_data_nascita_${index}" name="colpevole_data_nascita[]" required/>
            </div>
        </div>

        <div class="form-group">
            <label for="colpevole_immagine_${index}">Foto (opzionale)</label>
            <input type="file" id="colpevole_immagine_${index}" name="colpevole_immagine[]" accept="image/jpeg,image/png,image/webp" data-preview-target="preview-colpevole-${index}"/>
        </div>
        <div id="preview-colpevole-${index}" class="image-preview image-preview-small" aria-live="polite"></div>
    </div>
    `,

    articolo: (index) => `
    <div class="articolo-entry" data-index="${index}">
        <div class="entry-header">
            <h4>Fonte #${index + 1}</h4>
            <button type="button" class="btn-remove-entry" aria-label="Rimuovi fonte">Rimuovi</button>
        </div>

        <div class="form-group">
            <label for="articolo_titolo_${index}">Titolo Articolo/Fonte</label>
            <input type="text" id="articolo_titolo_${index}" name="articolo_titolo[]" placeholder="Es. 'Approfondimento su...'" maxlength="200"/>
        </div>

        <div class="form-row">
            <div class="form-group half">
                <label for="articolo_data_${index}">Data Pubblicazione</label>
                <input type="date" id="articolo_data_${index}" name="articolo_data[]"/>
            </div>
            <div class="form-group half">
                <label for="articolo_link_${index}">Link Fonte</label>
                <input type="url" id="articolo_link_${index}" name="articolo_link[]" placeholder="https://esempio.it/articolo"/>
            </div>
        </div>
    </div>
    `
};

// Manager entry dinamiche
const vittimeManager = createEntryManager({
    containerId: 'vittime-container',
    entrySelector: '.vittima-entry',
    templateFn: templates.vittima,
    initialCount: 1,
    confirmRemove: true
});

const colpevoliManager = createEntryManager({
    containerId: 'colpevoli-container',
    entrySelector: '.colpevole-entry',
    templateFn: templates.colpevole,
    initialCount: 1,
    confirmRemove: true
});

const articoliManager = createEntryManager({
    containerId: 'articoli-container',
    entrySelector: '.articolo-entry',
    templateFn: templates.articolo,
    initialCount: 1,
    confirmRemove: true
});

// Event listener pulsanti aggiungi
document.getElementById('btn-add-vittima').addEventListener('click', () => vittimeManager.add());
document.getElementById('btn-add-colpevole').addEventListener('click', () => colpevoliManager.add());
document.getElementById('btn-add-articolo').addEventListener('click', () => articoliManager.add());

const btnResetForm = document.getElementById('btn-reset-form');
if (btnResetForm) {
    btnResetForm.addEventListener('click', confermaResetForm);
}

// Validazione form
(function () {
    const form = document.getElementById('form-segnalazione');
    if (!form) return;

    const titoloInput = document.getElementById('titolo');
    const dataCrimineInput = document.getElementById('data_crimine');
    const luogoInput = document.getElementById('luogo');
    const descrizioneBreveInput = document.getElementById('descrizione_breve');
    const storiaInput = document.getElementById('storia');

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

    attachValidation(titoloInput, titoloRules);
    attachValidation(dataCrimineInput, dataRules);
    attachValidation(luogoInput, luogoRules);
    attachValidation(descrizioneBreveInput, descrizioneBreveRules);
    attachValidation(storiaInput, storiaRules);

    form.addEventListener('submit', function (e) {
        const errors = [];

        if (validateField(titoloInput, titoloRules)) errors.push('Titolo');
        if (validateField(dataCrimineInput, dataRules)) errors.push('Data');
        if (validateField(luogoInput, luogoRules)) errors.push('Luogo');
        if (validateField(descrizioneBreveInput, descrizioneBreveRules)) errors.push('Descrizione breve');
        if (validateField(storiaInput, storiaRules)) errors.push('Storia');

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

            focusFirstError([titoloInput, dataCrimineInput, luogoInput, descrizioneBreveInput, storiaInput]);
        }
    });
})();

// Conferma reset form
function confermaResetForm() {
    openConfirmModal(
        'Conferma cancellazione',
        'Sei sicuro di voler cancellare tutti i dati inseriti?',
        function () {
            const form = document.getElementById('form-segnalazione');
            if (form) {
                form.reset();
                document.querySelectorAll('.vittima-entry:not(:first-child)').forEach(el => el.remove());
                document.querySelectorAll('.colpevole-entry:not(:first-child)').forEach(el => el.remove());
                document.querySelectorAll('.articolo-entry:not(:first-child)').forEach(el => el.remove());
                document.querySelectorAll('.image-preview').forEach(el => el.innerHTML = '');
            }
        }
    );
}
