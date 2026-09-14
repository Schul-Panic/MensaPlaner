<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Konto erstellen</h1>
<p class="intro">Registriere dich, um MensaPlaner nutzen zu können.</p>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<div class="card">
    <form class="login-form" method="post" action="/register">
        <label>
            Name
            <input type="text" name="name" placeholder="Name" required>
        </label>
        <label>
            E-Mail
            <input type="email" name="email" placeholder="E-Mail" required>
        </label>
        <label>
            Passwort
            <input type="password" name="password" placeholder="Passwort" required>
        </label>
        <label>
            Passwort wiederholen
            <input type="password" name="password_confirm" placeholder="Passwort wiederholen" required>
        </label>
        <button type="submit">Registrieren</button>
    </form>

    <p class="switch-auth">Schon ein Konto? <a href="/">Jetzt anmelden</a></p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>