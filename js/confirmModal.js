/**
 * Sistema Modal Accessibile per conferme
 * Sostituisce window.confirm() con un'interfaccia ARIA compliant
 */
(function() {
    // Verifica che il DOM sia pronto
    function initModal() {
        const modal = document.getElementById('confirm-modal');
        if (!modal) {
            console.warn('Modal confirm-modal non trovato nel DOM');
            return;
        }

        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const confirmBtn = document.getElementById('modal-confirm');
        const cancelBtn = document.getElementById('modal-cancel');

        let resolveCallback = null;
        let previousFocus = null;
        let focusableElements = [];
        let firstFocusable = null;
        let lastFocusable = null;

        /**
         * Mostra il modal con configurazione personalizzata
         * @param {Object} config - Configurazione del modal
         * @returns {Promise<boolean>} - true se confermato, false se annullato
         */
        window.showConfirmModal = function(config) {
            return new Promise((resolve) => {
                resolveCallback = resolve;
                previousFocus = document.activeElement;

                // Imposta contenuti
                modalTitle.textContent = config.title || 'Conferma azione';
                modalDescription.textContent = config.message || 'Sei sicuro di voler procedere?';
                confirmBtn.textContent = config.confirmText || 'Conferma';

                // Imposta stile bottone conferma
                confirmBtn.className = 'btn ' + (config.confirmClass || 'btn-primary');

                // Mostra modal
                modal.style.display = 'flex';

                // Focus trap setup
                updateFocusableElements();

                // Focus sul bottone annulla per sicurezza
                cancelBtn.focus();
            });
        };

        /**
         * Chiude il modal e ritorna il focus all'elemento precedente
         */
        function closeModal(result) {
            modal.style.display = 'none';

            // Ripristina focus
            if (previousFocus) {
                previousFocus.focus();
            }

            // Risolvi la promise
            if (resolveCallback) {
                resolveCallback(result);
                resolveCallback = null;
            }
        }

        /**
         * Aggiorna la lista degli elementi focusabili per il focus trap
         */
        function updateFocusableElements() {
            focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            firstFocusable = focusableElements[0];
            lastFocusable = focusableElements[focusableElements.length - 1];
        }

        /**
         * Focus trap - mantiene il focus all'interno del modal
         */
        function handleTabKey(e) {
            if (e.key !== 'Tab') return;

            if (e.shiftKey) {
                // Shift + Tab
                if (document.activeElement === firstFocusable) {
                    e.preventDefault();
                    lastFocusable.focus();
                }
            } else {
                // Tab
                if (document.activeElement === lastFocusable) {
                    e.preventDefault();
                    firstFocusable.focus();
                }
            }
        }

        // Event Listeners
        confirmBtn.addEventListener('click', () => closeModal(true));
        cancelBtn.addEventListener('click', () => closeModal(false));

        // Chiudi con ESC
        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal(false);
            } else {
                handleTabKey(e);
            }
        });

        // Chiudi cliccando sull'overlay
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal(false);
            }
        });
    }

    // Inizializza quando il DOM è pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initModal);
    } else {
        initModal();
    }
})();
