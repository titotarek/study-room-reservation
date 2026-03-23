document.addEventListener('DOMContentLoaded', () => {

    const form = document.getElementById('loginForm');
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnIcon = document.getElementById('btnIcon');

    if (!form || !btn) return;

    form.addEventListener('submit', () => {

        btn.disabled = true;
        btn.classList.add('opacity-80');

        if (btnText) {
            btnText.innerText = 'Signing in...';
        }

        if (btnIcon) {
            btnIcon.innerHTML = `
                <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                            stroke="currentColor" stroke-width="4" fill="none"></circle>
                </svg>
            `;
        }
    });

});
