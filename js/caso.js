// Script pagina dettaglio caso

document.addEventListener('DOMContentLoaded', function () {

    // Approva caso
    const btnApprovaCaso = document.getElementById('btn-approva-caso');
    if (btnApprovaCaso) {
        btnApprovaCaso.addEventListener('click', async function () {
            const confirmed = await showConfirmModal({
                title: "Approva Caso",
                message: "Confermi l'approvazione di questo caso? Sarà visibile pubblicamente a tutti gli utenti.",
                confirmText: "Approva",
                confirmClass: "btn-success"
            });

            if (confirmed) {
                document.getElementById("form-approva-caso").submit();
            }
        });
    }

    // Rifiuta caso
    const btnRifiutaCaso = document.getElementById('btn-rifiuta-caso');
    if (btnRifiutaCaso) {
        btnRifiutaCaso.addEventListener('click', async function () {
            const confirmed = await showConfirmModal({
                title: "Rifiuta ed Elimina Caso",
                message: "ATTENZIONE: Questa azione eliminerà definitivamente il caso e tutti i dati associati (vittime, colpevoli, articoli, commenti). L'operazione non può essere annullata.",
                confirmText: "Elimina Definitivamente",
                confirmClass: "btn-danger"
            });

            if (confirmed) {
                document.getElementById("form-rifiuta-caso").submit();
            }
        });
    }

    // Elimina commento
    document.addEventListener('click', async function (e) {
        const btn = e.target.closest('.btn-elimina-commento');
        if (!btn) return;

        const idCommento = btn.dataset.commentoId;
        if (!idCommento) return;

        const confirmed = await showConfirmModal({
            title: "Elimina Commento",
            message: "Sei sicuro di voler eliminare questo commento? L'operazione non può essere annullata.",
            confirmText: "Elimina",
            confirmClass: "btn-danger"
        });

        if (confirmed) {
            document.getElementById("form-elimina-" + idCommento).submit();
        }
    });

});
