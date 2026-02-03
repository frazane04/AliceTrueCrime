// Utility per validazione form accessibile

// Mostra errore su un input
function showError(input, message) {
    const errorId = input.id + '-error';
    const errorSpan = document.getElementById(errorId);

    input.setAttribute('aria-invalid', 'true');
    if (errorSpan) {
        errorSpan.textContent = message;
        errorSpan.classList.remove('hidden');
    }
}

// Rimuove errore da un input
function clearError(input) {
    const errorId = input.id + '-error';
    const errorSpan = document.getElementById(errorId);

    input.setAttribute('aria-invalid', 'false');
    if (errorSpan) {
        errorSpan.textContent = '';
        errorSpan.classList.add('hidden');
    }
}

// Valida formato email
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Mostra errori generali del form
function showFormErrors(message) {
    const formErrors = document.getElementById('form-errors');
    if (formErrors) {
        formErrors.textContent = message;
        formErrors.classList.remove('hidden');
    }
}

// Nasconde errori generali del form
function clearFormErrors() {
    const formErrors = document.getElementById('form-errors');
    if (formErrors) {
        formErrors.textContent = '';
        formErrors.classList.add('hidden');
    }
}

// Regole di validazione predefinite
const ValidationRules = {
    required: (msg) => ({
        validate: (v) => !!v,
        message: msg || 'Campo obbligatorio'
    }),
    minLength: (n, msg) => ({
        validate: (v) => v.length >= n,
        message: msg || `Minimo ${n} caratteri`
    }),
    maxLength: (n, msg) => ({
        validate: (v) => v.length <= n,
        message: msg || `Massimo ${n} caratteri`
    }),
    email: (msg) => ({
        validate: isValidEmail,
        message: msg || 'Email non valida'
    }),
    matches: (getOtherValue, msg) => ({
        validate: (v) => v === getOtherValue(),
        message: msg || 'I valori non corrispondono'
    }),

    alphanumericUnderscore: (msg) => ({
        validate: (v) => /^[a-zA-Z0-9_]+$/.test(v),
        message: msg || 'Solo lettere, numeri e underscore ammessi'
    }),

    passwordStrength: (msg) => ({
        validate: (v) => /^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])/.test(v),
        message: msg || 'Deve contenere almeno una maiuscola, una minuscola e un numero'
    }),

    notFutureDate: (msg) => ({
        validate: (v) => !v || new Date(v) <= new Date(),
        message: msg || 'La data non può essere nel futuro'
    }),

    url: (msg) => ({
        validate: (v) => !v || /^https?:\/\/.+/.test(v),
        message: msg || 'URL non valido (deve iniziare con http:// o https://)'
    })
};

// Applica validazione blur a un campo
function attachValidation(input, rules) {
    input.addEventListener('blur', function () {
        const value = this.value.trim();
        for (const rule of rules) {
            if (!rule.validate(value)) {
                showError(this, rule.message);
                return;
            }
        }
        clearError(this);
    });
}

// Valida un campo e ritorna l'errore
function validateField(input, rules) {
    const value = input.value.trim();
    for (const rule of rules) {
        if (!rule.validate(value)) {
            showError(input, rule.message);
            return rule.message;
        }
    }
    clearError(input);
    return null;
}

// Focus sul primo campo con errore
function focusFirstError(inputs) {
    for (const input of inputs) {
        if (input.getAttribute('aria-invalid') === 'true') {
            input.focus();
            return;
        }
    }
}

// Inizializza toggle mostra/nascondi password
function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.password-toggle');

    toggleButtons.forEach(button => {
        button.setAttribute('aria-pressed', 'false');

        button.addEventListener('click', function () {
            const targetId = this.getAttribute('data-target');
            const input = document.getElementById(targetId);
            const eyeIcon = this.querySelector('.eye-icon');
            const eyeOffIcon = this.querySelector('.eye-off-icon');

            if (input.type === 'password') {
                input.type = 'text';
                this.setAttribute('aria-label', 'Nascondi password');
                this.setAttribute('aria-pressed', 'true');
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                input.type = 'password';
                this.setAttribute('aria-label', 'Mostra password');
                this.setAttribute('aria-pressed', 'false');
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        });
    });
}


document.addEventListener('DOMContentLoaded', initPasswordToggles);
