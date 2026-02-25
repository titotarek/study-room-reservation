let formToSubmit = null;

function showDeleteModal(id) {
    formToSubmit = 'delete-form-' + id;

    const modal = document.getElementById('customConfirmModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideDeleteModal() {
    const modal = document.getElementById('customConfirmModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    formToSubmit = null;
}

document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            showDeleteModal(id);
        });
    });

    document.getElementById('modalConfirmBtn')
        ?.addEventListener('click', () => {
            if (formToSubmit) {
                document.getElementById(formToSubmit).submit();
            }
        });

    document.getElementById('modalCancelBtn')
        ?.addEventListener('click', hideDeleteModal);
});
