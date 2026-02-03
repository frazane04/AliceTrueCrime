// Utility per form di gestione casi (segnala e modifica)

// Crea gestore per entry dinamiche (vittime, colpevoli, articoli)
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

            if (minEntries > 0 && currentCount <= minEntries) {
                await showInfoModal({
                    title: 'Operazione non consentita',
                    message: config.minEntriesMessage || `Deve esserci almeno ${minEntries} elemento.`
                });
                return;
            }

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

// Mostra anteprima immagine selezionata
function mostraAnteprimaImmagine(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;

    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const file = input.files[0];

        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type)) {
            preview.innerHTML = '<p class="error-text" role="alert">Formato non supportato. Usa JPG, PNG o WebP.</p>';
            input.value = '';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            preview.innerHTML = '<p class="error-text" role="alert">File troppo grande. Max 2MB.</p>';
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.alt = 'Anteprima immagine caricata';
            img.className = 'preview-image';

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'btn-remove-preview';
            removeBtn.textContent = 'Rimuovi';
            removeBtn.setAttribute('aria-label', 'Rimuovi immagine');
            removeBtn.onclick = function () {
                input.value = '';
                preview.innerHTML = '';
            };

            preview.appendChild(img);
            preview.appendChild(removeBtn);
        };
        reader.readAsDataURL(file);
    }
}

// Marca immagine per rimozione
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

// Annulla rimozione immagine
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

// Event delegation per click
document.addEventListener('click', function (e) {
    const btnRemoveImg = e.target.closest('[data-img-action="remove"]');
    if (btnRemoveImg) {
        const tipo = btnRemoveImg.dataset.imgType;
        const index = btnRemoveImg.dataset.imgIndex;
        const path = btnRemoveImg.dataset.imgPath;
        marcaRimozioneImmagine(tipo, index, path);
        return;
    }

    const btnUndoImg = e.target.closest('[data-img-action="undo"]');
    if (btnUndoImg) {
        const tipo = btnUndoImg.dataset.imgType;
        const index = btnUndoImg.dataset.imgIndex;
        const path = btnUndoImg.dataset.imgPath;
        annullaRimozioneImmagine(tipo, index, path);
        return;
    }

    const btnRemoveEntry = e.target.closest('.btn-remove-entry');
    if (btnRemoveEntry) {
        const entry = btnRemoveEntry.closest('.entry-card, .persona-entry, .articolo-entry');
        if (entry) {
            entry.remove();
        }
        return;
    }
});

// Event delegation per anteprima immagini
document.addEventListener('change', function (e) {
    const input = e.target;
    if (input.type === 'file' && input.dataset.previewTarget) {
        mostraAnteprimaImmagine(input, input.dataset.previewTarget);
    }
});
