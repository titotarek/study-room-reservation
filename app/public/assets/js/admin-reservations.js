let formToSubmit = null;
let lastFocusedElement = null;
const getFocusableElements = (modal) =>
    Array.from(
        modal.querySelectorAll(
            'button:not([disabled]), [href], input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
        ),
    ).filter((element) => !element.hasAttribute('hidden'));

function trapFocus(event, modal) {
    if (!modal || event.key !== 'Tab' || modal.classList.contains('hidden')) {
        return;
    }

    const focusableElements = getFocusableElements(modal);
    if (!focusableElements.length) {
        event.preventDefault();
        return;
    }

    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];

    if (event.shiftKey && document.activeElement === firstElement) {
        event.preventDefault();
        lastElement.focus();
        return;
    }

    if (!event.shiftKey && document.activeElement === lastElement) {
        event.preventDefault();
        firstElement.focus();
    }
}

function showDeleteModal(id) {
    formToSubmit = 'delete-form-' + id;
    lastFocusedElement = document.activeElement;

    const modal = document.getElementById('customConfirmModal');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.setAttribute('aria-hidden', 'false');
    getFocusableElements(modal)[0]?.focus();
}

function hideDeleteModal() {
    const modal = document.getElementById('customConfirmModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    modal.setAttribute('aria-hidden', 'true');
    formToSubmit = null;
    lastFocusedElement?.focus?.();
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

    document.addEventListener('keydown', (event) => {
        const modal = document.getElementById('customConfirmModal');

        if (modal && !modal.classList.contains('hidden')) {
            trapFocus(event, modal);
        }

        if (event.key === 'Escape') {
            hideDeleteModal();
        }
    });
});
