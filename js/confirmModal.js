// Sistema Modal
(function () {
    let modal = null;
    let modalTitle = null;
    let modalDescription = null;
    let confirmBtn = null;
    let cancelBtn = null;
    let resolveCallback = null;
    let previousFocus = null;

    // Struttura HTML
    function createModal() {
        if (modal) return; // Già creata

        modal = document.createElement('div');
        modal.id = 'confirm-modal';
        modal.className = 'modal-overlay';
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-labelledby', 'modal-title');
        modal.setAttribute('aria-describedby', 'modal-description');

        modal.innerHTML = `
            <div class="modal-content">
                <h2 id="modal-title"></h2>
                <p id="modal-description"></p>
                <div class="modal-actions">
                    <button id="modal-cancel" class="btn btn-secondary">Annulla</button>
                    <button id="modal-confirm" class="btn btn-primary"></button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);

        // Riferimenti agli elementi

        modalDescription = modal.querySelector('#modal-description');
        confirmBtn = modal.querySelector('#modal-confirm');
        cancelBtn = modal.querySelector('#modal-cancel');


        confirmBtn.addEventListener('click', () => closeModal(true));
        cancelBtn.addEventListener('click', () => closeModal(false));

        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
            } else {
                handleTabKey(e);
            }
        });

        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(false);
            }
        });
    }

    // Gestione Focus
    function handleTabKey(e) {
        if (e.key !== 'Tab') return;

        const focusableElements = modal.querySelectorAll(
            'button:not(.hidden), [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstFocusable = focusableElements[0];
        const lastFocusable = focusableElements[focusableElements.length - 1];

        if (e.shiftKey) {
            if (document.activeElement === firstFocusable) {
                e.preventDefault();
                lastFocusable.focus();
            }
        } else {
            if (document.activeElement === lastFocusable) {
                e.preventDefault();
                firstFocusable.focus();
            }
        }
    }


    function closeModal(result) {
        if (!modal) return;

        modal.remove();
        modal = null;

        if (previousFocus) {
            previousFocus.focus();
        }

        if (resolveCallback) {
            resolveCallback(result);
            resolveCallback = null;
        }
    }

    // Mostra Modal
    window.showConfirmModal = function (config) {
        return new Promise((resolve) => {
            resolveCallback = resolve;
            previousFocus = document.activeElement;

            createModal();

            // Imposta contenuti
            modalTitle.textContent = config.title || 'Conferma azione';
            modalDescription.textContent = config.message || 'Sei sicuro di voler procedere?';
            confirmBtn.textContent = config.confirmText || 'Conferma';

            // Imposta stile bottone conferma
            confirmBtn.className = 'btn ' + (config.confirmClass || 'btn-primary');

            // Mostra/nascondi bottone annulla (per modal solo informative)
            if (config.hideCancel) {
                cancelBtn.classList.add('hidden');
            } else {
                cancelBtn.classList.remove('hidden');
            }

            // Focus sul bottone appropriato
            if (config.hideCancel) {
                confirmBtn.focus();
            } else {
                cancelBtn.focus();
            }
        });
    };

    // Mostra Modal Info
    window.showInfoModal = function (config) {
        return showConfirmModal({
            title: config.title || 'Informazione',
            message: config.message,
            confirmText: config.confirmText || 'OK',
            confirmClass: config.confirmClass || 'btn-primary',
            hideCancel: true
        });
    };
})();
