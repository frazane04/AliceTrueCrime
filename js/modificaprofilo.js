// Validazione form modifica profilo
(function () {
    const form = document.querySelector('.edit-profile-form');
    if (!form) return;

    const usernameInput = document.getElementById('username');
    const newPasswordInput = document.getElementById('new_password');
    const confirmPasswordInput = document.getElementById('confirm_password');
    const currentPasswordInput = document.getElementById('current_password');

    const usernameRules = [
        ValidationRules.required('Username richiesto'),
        ValidationRules.minLength(3, 'Username troppo corto (minimo 3 caratteri)'),
        ValidationRules.maxLength(30, 'Username troppo lungo (massimo 30 caratteri)'),
        ValidationRules.alphanumericUnderscore('Solo lettere, numeri e underscore ammessi')
    ];

    const currentPasswordRules = [
        ValidationRules.required('Password attuale richiesta per confermare')
    ];

    const newPasswordRules = [
        ValidationRules.minLength(8, 'Password troppo corta (minimo 8 caratteri)'),
        ValidationRules.passwordStrength('Deve contenere almeno una maiuscola, una minuscola e un numero')
    ];

    const confirmPasswordRules = [
        ValidationRules.matches(() => newPasswordInput.value, 'Le password non corrispondono')
    ];

    // Validazione on blur
    attachValidation(usernameInput, usernameRules);
    attachValidation(currentPasswordInput, currentPasswordRules);

    // Validazione condizionale per nuova password
    newPasswordInput.addEventListener('blur', function () {
        if (this.value.trim() === '') {
            clearError(this);
            clearError(confirmPasswordInput);
            return;
        }
        validateField(this, newPasswordRules);
    });

    confirmPasswordInput.addEventListener('blur', function () {
        if (newPasswordInput.value.trim() === '' && this.value.trim() === '') {
            clearError(this);
            return;
        }
        validateField(this, confirmPasswordRules);
    });

    // Validazione submit
    form.addEventListener('submit', function (e) {
        clearFormErrors();
        const errors = [];

        if (validateField(usernameInput, usernameRules)) errors.push('Username');
        if (validateField(currentPasswordInput, currentPasswordRules)) errors.push('Password attuale');

        if (newPasswordInput.value.trim() !== '') {
            if (validateField(newPasswordInput, newPasswordRules)) errors.push('Nuova password');
            if (validateField(confirmPasswordInput, confirmPasswordRules)) errors.push('Conferma password');
        }

        if (errors.length > 0) {
            e.preventDefault();
            showFormErrors('Correggi i seguenti campi: ' + errors.join(', '));
            focusFirstError([usernameInput, currentPasswordInput, newPasswordInput, confirmPasswordInput]);
        }
    });
})();
