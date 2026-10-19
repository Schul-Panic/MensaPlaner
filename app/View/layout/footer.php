
</div>

<script>
    // Auge zum Ein-/Ausblenden für alle Passwortfelder
    (function () {
        var eye = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
        var eyeOff = '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';

        document.querySelectorAll('input[type="password"]').forEach(function (input) {
            var wrapper = document.createElement('span');
            wrapper.className = 'password-field';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
            input.classList.add('password-input');

            var toggle = document.createElement('button');
            toggle.type = 'button';
            toggle.className = 'password-toggle';
            wrapper.appendChild(toggle);

            function render() {
                var visible = input.type === 'text';
                toggle.innerHTML = visible ? eyeOff : eye;
                toggle.setAttribute('aria-label', visible ? 'Passwort verbergen' : 'Passwort anzeigen');
                toggle.setAttribute('aria-pressed', visible ? 'true' : 'false');
            }

            toggle.addEventListener('click', function () {
                input.type = input.type === 'password' ? 'text' : 'password';
                render();
                input.focus();
            });

            render();
        });
    })();
</script>

</body>
</html>
