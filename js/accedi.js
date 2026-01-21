/**
 * Validazione form login
 * Richiede: form-validation.js
 */
(function() {
    const form = document.querySelector('.auth-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    // Regole di validazione
    const emailRules = [ValidationRules.required('Email o username richiesto')];
    const passwordRules = [ValidationRules.required('Password richiesta')];

    // Validazione real-time su blur
    attachValidation(emailInput, emailRules);
    attachValidation(passwordInput, passwordRules);

    // Validazione submit
    form.addEventListener('submit', function(e) {
        clearFormErrors();

        const emailError = validateField(emailInput, emailRules);
        const passwordError = validateField(passwordInput, passwordRules);

        if (emailError || passwordError) {
            e.preventDefault();
            showFormErrors('Correggi gli errori nel form prima di procedere');
            focusFirstError([emailInput, passwordInput]);
        }
    });
})();
