// Sistema Modal di conferma
(function () {
    let modal = null;
    let modalTitle = null;
    let modalDescription = null;
    let confirmBtn = null;
    let cancelBtn = null;
    let resolveCallback = null;
    let previousFocus = null;

    // Crea struttura HTML modal
    function createModal() {
        if (modal) return;

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

        modalTitle = modal.querySelector('#modal-title');
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

    // Gestisce focus trap nel modal
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

    // Chiude modal e risolve promise
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

    // Mostra modal di conferma
    window.showConfirmModal = function (config) {
        return new Promise((resolve) => {
            resolveCallback = resolve;
            previousFocus = document.activeElement;

            createModal();

            modalTitle.textContent = config.title || 'Conferma azione';
            modalDescription.textContent = config.message || 'Sei sicuro di voler procedere?';
            confirmBtn.textContent = config.confirmText || 'Conferma';
            confirmBtn.className = 'btn ' + (config.confirmClass || 'btn-primary');

            if (config.hideCancel) {
                cancelBtn.classList.add('hidden');
            } else {
                cancelBtn.classList.remove('hidden');
            }

            if (config.hideCancel) {
                confirmBtn.focus();
            } else {
                cancelBtn.focus();
            }
        });
    };

    // Mostra modal informativo
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
