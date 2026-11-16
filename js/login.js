
/*
 * ============================================================
 * LOGIN SIT - JAVASCRIPT
 * ============================================================
 *
 * Questo file contiene le interazioni specifiche della pagina
 * di login.
 *
 * Non contiene codice inline nell'HTML.
 */


/*
 * ------------------------------------------------------------
 * MOSTRA / NASCONDI PASSWORD
 * ------------------------------------------------------------
 *
 * Al click sull'icona:
 *
 * - se la password è nascosta, la rende visibile;
 * - se la password è visibile, la nasconde;
 * - aggiorna l'icona Bootstrap;
 * - aggiorna gli attributi accessibili del pulsante.
 */

document.addEventListener('DOMContentLoaded', function () {

    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    // Se gli elementi non esistono, non eseguo altre operazioni.
    if (!togglePassword || !password) {
        return;
    }

    togglePassword.addEventListener('click', function () {

        // Verifico lo stato attuale del campo password.
        const passwordVisible = password.type === 'text';

        // Inverto il tipo del campo.
        password.type = passwordVisible ? 'password' : 'text';

        // Recupero l'elemento grafico Bootstrap Icons.
        const icon = togglePassword.querySelector('i');

        if (icon) {

            // Aggiorno l'icona in base allo stato del campo.
            icon.classList.toggle('bi-eye', !passwordVisible);
            icon.classList.toggle('bi-eye-slash', passwordVisible);

        }

        // Aggiorno le informazioni per gli strumenti assistivi.
        togglePassword.setAttribute(
            'aria-label',
            passwordVisible ? 'Mostra password' : 'Nascondi password'
        );

        togglePassword.setAttribute(
            'aria-pressed',
            String(!passwordVisible)
        );

    });

});



function turnstileScaduto() {
    turnstile.reset();
}

function turnstileErrore() {
    turnstile.reset();
}