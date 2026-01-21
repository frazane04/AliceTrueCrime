/**
 * Script per la pagina dettaglio caso
 * Gestisce le conferme admin e utente tramite modal accessibile
 */

/**
 * Conferma approvazione caso admin
 * Apre modal accessibile con messaggio informativo
 */
async function confermaApprovaCaso() {
    const confirmed = await showConfirmModal({
        title: "Approva Caso",
        message: "Confermi l'approvazione di questo caso? Sarà visibile pubblicamente a tutti gli utenti.",
        confirmText: "Approva",
        confirmClass: "btn-success"
    });

    if (confirmed) {
        document.getElementById("form-approva-caso").submit();
    }
}

/**
 * Conferma rifiuto/eliminazione caso admin
 * Apre modal accessibile con avviso forte (azione irreversibile)
 */
async function confermaRifiutaCaso() {
    const confirmed = await showConfirmModal({
        title: "Rifiuta ed Elimina Caso",
        message: "⚠️ ATTENZIONE: Questa azione eliminerà definitivamente il caso e tutti i dati associati (vittime, colpevoli, articoli, commenti). L'operazione non può essere annullata.",
        confirmText: "Elimina Definitivamente",
        confirmClass: "btn-danger"
    });

    if (confirmed) {
        document.getElementById("form-rifiuta-caso").submit();
    }
}

/**
 * Conferma eliminazione commento
 * @param {number} idCommento - ID del commento da eliminare
 */
async function confermaEliminaCommento(idCommento) {
    const confirmed = await showConfirmModal({
        title: "Elimina Commento",
        message: "Sei sicuro di voler eliminare questo commento? L'operazione non può essere annullata.",
        confirmText: "Elimina",
        confirmClass: "btn-danger"
    });

    if (confirmed) {
        document.getElementById("form-elimina-" + idCommento).submit();
    }
}
