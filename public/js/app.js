/**
 * Reusable SweetAlert2 confirmation dialog for form submissions (e.g. deletions).
 * @param {HTMLElement} button - The button triggering the confirmation.
 * @param {string} text - The warning text to show in the alert.
 */
function confirmDelete(button, text = "Cette action est irréversible !") {
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3d5a3d', // theme olive
        cancelButtonColor: '#8a8273', // theme muted
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = button.closest('form');
            if (form) form.submit();
        }
    });
}
