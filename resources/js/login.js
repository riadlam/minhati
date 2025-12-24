// resources/js/login.js

function togglePassword() {
    const passwordField = document.getElementById('password');
    const icon = document.querySelector('.toggle-password i');
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        passwordField.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const ninInput = document.getElementById('nin');
    if (ninInput) {
        ninInput.addEventListener('input', function () {
            if (this.value.length === 18) {
                document.getElementById('password').focus();
            }
        });
    }

    const toast = document.getElementById('toast-success');
    if (toast) setTimeout(() => toast.remove(), 3000);
});

// ✅ Fichier : resources/js/forget-password.js

document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('successModal');
    const closeBtn = document.getElementById('closeModal');

    if (modal && closeBtn) {
        // Fermer le modal via le bouton
        closeBtn.addEventListener('click', () => {
            modal.style.opacity = '0';
            modal.style.transition = 'opacity 0.3s ease';
            setTimeout(() => modal.remove(), 300);
        });

        // Fermer en cliquant à l’extérieur
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeBtn.click();
            }
        });
    }
});
