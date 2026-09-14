<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Willkommen bei MensaPlaner</h1>
<p class="intro">Melde dich an, um den Speiseplan zu sehen und zu verwalten.</p>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<div class="card">
    <form class="login-form" method="post" action="/">
        <label>
            E-Mail
            <input type="email" name="email" placeholder="E-Mail" required>
        </label>
        <label>
            Passwort
            <input type="password" name="password" placeholder="Passwort" required>
        </label>
        <button type="submit">Anmelden</button>
    </form>

    <div class="divider"><span>oder</span></div>

    <div class="oauth-buttons">
        <button type="button" class="oauth-button">
            <svg class="oauth-icon" viewBox="0 0 18 18" aria-hidden="true">
                <path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.9c1.7-1.57 2.7-3.88 2.7-6.62z"/>
                <path fill="#34A853" d="M9 18c2.43 0 4.47-.8 5.96-2.18l-2.9-2.26c-.8.54-1.84.86-3.06.86-2.35 0-4.34-1.59-5.05-3.72H.98v2.33A9 9 0 0 0 9 18z"/>
                <path fill="#FBBC05" d="M3.95 10.7A5.4 5.4 0 0 1 3.67 9c0-.59.1-1.17.28-1.7V4.97H.98A9 9 0 0 0 0 9c0 1.45.35 2.83.98 4.03l2.97-2.33z"/>
                <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.46 3.44 1.35l2.58-2.58C13.47.89 11.43 0 9 0A9 9 0 0 0 .98 4.97l2.97 2.33C4.66 5.17 6.65 3.58 9 3.58z"/>
            </svg>
            Mit Google anmelden
        </button>
        <button type="button" class="oauth-button">
            <svg class="oauth-icon" viewBox="0 0 18 18" aria-hidden="true">
                <rect x="0" y="0" width="8.5" height="8.5" fill="#F35325"/>
                <rect x="9.5" y="0" width="8.5" height="8.5" fill="#81BC06"/>
                <rect x="0" y="9.5" width="8.5" height="8.5" fill="#05A6F0"/>
                <rect x="9.5" y="9.5" width="8.5" height="8.5" fill="#FFBA08"/>
            </svg>
            Mit Microsoft anmelden
        </button>
        <button type="button" class="oauth-button">
            <svg class="oauth-icon" viewBox="0 0 384 512" aria-hidden="true">
                <path fill="currentColor" d="M318.7 268.7c-.2-36.7 16.4-64.4 50-84.8-18.8-26.9-47.2-41.7-84.7-44.6-35.5-2.8-74.3 20.7-88.5 20.7-15 0-49.4-19.7-76.4-19.7C63.3 141 0 184.8 0 273.5c0 26.2 4.8 53.3 14.4 81.2 12.8 37.6 59 129.3 107.2 127.8 25.2-.6 43-17.9 75.8-17.9 31.8 0 48.3 17.9 76.4 17.9 48.6-.7 90.4-84.1 102.6-121.8-65.2-30.7-57.7-90-57.7-92zM256.4 88.9c26.9-32 24.5-61.2 23.7-71.7-23.8 1.4-51.3 16.4-67 34.9-17.3 19.8-27.5 44.4-25.3 71.9 25.9 2 49.5-11.4 68.6-35.1z"/>
            </svg>
            Mit iCloud anmelden
        </button>
    </div>

    <p class="switch-auth">Noch kein Konto? <a href="/register">Jetzt registrieren</a></p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>