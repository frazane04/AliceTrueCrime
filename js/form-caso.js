/**
 * Utility comuni per i form di gestione casi (segnala e modifica)
 * Include: anteprima immagini, gestione rimozione immagini, entry dinamiche
 */

// ========================================
// FACTORY PER ENTRY DINAMICHE
// ========================================

/**
 * Crea un gestore per entry dinamiche (vittime, colpevoli, articoli)
 * @param {Object} config - Configurazione
 * @param {string} config.containerId - ID del container
 * @param {string} config.entrySelector - Selettore CSS per le entry
 * @param {Function} config.templateFn - Funzione che genera l'HTML (riceve index)
 * @param {number} config.initialCount - Conteggio iniziale (default 0)
 * @param {number} config.minEntries - Minimo entry richieste (default 0)
 * @param {string} config.minEntriesMessage - Messaggio se si tenta di scendere sotto il minimo
 * @param {boolean} config.confirmRemove - Se chiedere conferma prima di rimuovere
 * @returns {Object} Manager con metodi add e remove
 */
function createEntryManager(config) {
    let counter = config.initialCount || 0;
    const minEntries = config.minEntries || 0;

    return {
        add() {
            const container = document.getElementById(config.containerId);
            if (!container) return;
            const html = config.templateFn(counter);
            container.insertAdjacentHTML('beforeend', html);
            counter++;
        },

        async remove(button) {
            const entry = button.closest(config.entrySelector);
            if (!entry) return;

            const container = entry.parentElement;
            const currentCount = container.querySelectorAll(config.entrySelector).length;

            // Verifica minimo
            if (minEntries > 0 && currentCount <= minEntries) {
                await showInfoModal({
                    title: 'Operazione non consentita',
                    message: config.minEntriesMessage || `Deve esserci almeno ${minEntries} elemento.`
                });
                return;
            }

            // Conferma rimozione se richiesto
            if (config.confirmRemove) {
                const confirmed = await showConfirmModal({
                    title: 'Rimuovi elemento',
                    message: 'Sei sicuro di voler rimuovere questa entry?',
                    confirmText: 'Rimuovi',
                    confirmClass: 'btn-danger'
                });
                if (!confirmed) return;
            }

            entry.remove();
        },

        getCount() {
            return counter;
        },

        setCount(n) {
            counter = n;
        }
    };
}

// ========================================
// ANTEPRIMA IMMAGINI
// ========================================
function mostraAnteprimaImmagine(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Validazione client-side
        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            preview.innerHTML = '<p class="error-text" role="alert">Formato non supportato. Usa JPG, PNG o WebP.</p>';
            input.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            preview.innerHTML = '<p class="error-text" role="alert">File troppo grande. Max 5MB.</p>';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Anteprima immagine caricata';
            img.className = 'preview-image';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-preview';
            removeBtn.textContent = 'Rimuovi';
            removeBtn.setAttribute('aria-label', 'Rimuovi immagine');
            removeBtn.onclick = function() {
                input.value = '';
                preview.innerHTML = '';
            };

            preview.appendChild(img);
            preview.appendChild(removeBtn);
        };
        reader.readAsDataURL(file);
    }
}

// ========================================
// GESTIONE RIMOZIONE IMMAGINI (per modifica)
// ========================================
function marcaRimozioneImmagine(tipo, index, originalPath) {
    const previewId = tipo === 'caso' ? 'caso-img-preview' : tipo + '-img-preview-' + index;
    const noticeId = tipo === 'caso' ? 'caso-img-removed' : tipo + '-img-removed-' + index;
    const hiddenId = tipo === 'caso' ? 'caso-img-hidden' : tipo + '-img-hidden-' + index;

    const preview = document.getElementById(previewId);
    if (preview) preview.classList.add('hidden');

    const notice = document.getElementById(noticeId);
    if (notice) notice.classList.remove('hidden');

    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = '';
}

function annullaRimozioneImmagine(tipo, index, originalPath) {
    const previewId = tipo === 'caso' ? 'caso-img-preview' : tipo + '-img-preview-' + index;
    const noticeId = tipo === 'caso' ? 'caso-img-removed' : tipo + '-img-removed-' + index;
    const hiddenId = tipo === 'caso' ? 'caso-img-hidden' : tipo + '-img-hidden-' + index;

    const preview = document.getElementById(previewId);
    if (preview) preview.classList.remove('hidden');

    const notice = document.getElementById(noticeId);
    if (notice) notice.classList.add('hidden');

    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = originalPath;
}

// ========================================
// EVENT DELEGATION: Gestione immagini e entry
// ========================================
document.addEventListener('click', function(e) {
    // Gestione rimozione immagine
    const btnRemoveImg = e.target.closest('[data-img-action="remove"]');
    if (btnRemoveImg) {
        const tipo = btnRemoveImg.dataset.imgType;
        const index = btnRemoveImg.dataset.imgIndex;
        const path = btnRemoveImg.dataset.imgPath;
        marcaRimozioneImmagine(tipo, index, path);
        return;
    }

    // Gestione annulla rimozione immagine
    const btnUndoImg = e.target.closest('[data-img-action="undo"]');
    if (btnUndoImg) {
        const tipo = btnUndoImg.dataset.imgType;
        const index = btnUndoImg.dataset.imgIndex;
        const path = btnUndoImg.dataset.imgPath;
        annullaRimozioneImmagine(tipo, index, path);
        return;
    }

    // Gestione rimozione entry (vittime, colpevoli, articoli)
    const btnRemoveEntry = e.target.closest('.btn-remove-entry');
    if (btnRemoveEntry) {
        const entry = btnRemoveEntry.closest('.entry-card, .persona-entry, .articolo-entry');
        if (entry) {
            entry.remove();
        }
        return;
    }
});

// Event delegation per anteprima immagini (onchange)
document.addEventListener('change', function(e) {
    const input = e.target;
    if (input.type === 'file' && input.dataset.previewTarget) {
        mostraAnteprimaImmagine(input, input.dataset.previewTarget);
    }
});
