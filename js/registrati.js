/**
 * Validazione form registrazione
 * Richiede: form-validation.js
 */
(function() {
    const form = document.querySelector('.auth-form');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirm');
    const termsInput = document.getElementById('terms');

    // Regole di validazione (sincronizzate con server PHP)
    const usernameRules = [
        ValidationRules.required('Username richiesto'),
        ValidationRules.minLength(3, 'Username troppo corto (minimo 3 caratteri)'),
        ValidationRules.maxLength(30, 'Username troppo lungo (massimo 30 caratteri)'),
        ValidationRules.alphanumericUnderscore('Username può contenere solo lettere, numeri e underscore')
    ];

    const emailRules = [
        ValidationRules.required('Email richiesta'),
        ValidationRules.email('Email non valida')
    ];

    const passwordRules = [
        ValidationRules.required('Password richiesta'),
        ValidationRules.minLength(8, 'Password troppo corta (minimo 8 caratteri)'),
        ValidationRules.passwordStrength('La password deve contenere almeno una maiuscola, una minuscola e un numero')
    ];

    const passwordConfirmRules = [
        ValidationRules.required('Conferma password richiesta'),
        ValidationRules.matches(() => passwordInput.value, 'Le password non corrispondono')
    ];

    // Validazione real-time su blur
    attachValidation(usernameInput, usernameRules);
    attachValidation(emailInput, emailRules);
    attachValidation(passwordInput, passwordRules);
    attachValidation(passwordConfirmInput, passwordConfirmRules);

    // Validazione submit
    form.addEventListener('submit', function(e) {
        clearFormErrors();
        const errors = [];

        if (validateField(usernameInput, usernameRules)) errors.push('Username');
        if (validateField(emailInput, emailRules)) errors.push('Email');
        if (validateField(passwordInput, passwordRules)) errors.push('Password');
        if (validateField(passwordConfirmInput, passwordConfirmRules)) errors.push('Conferma password');

        // Valida termini
        if (!termsInput.checked) {
            showFormErrors('Devi accettare i Termini di Servizio e la Privacy Policy');
            e.preventDefault();
            return;
        }

        if (errors.length > 0) {
            e.preventDefault();
            showFormErrors('Correggi i seguenti campi: ' + errors.join(', '));
            focusFirstError([usernameInput, emailInput, passwordInput, passwordConfirmInput]);
        }
    });
})();
