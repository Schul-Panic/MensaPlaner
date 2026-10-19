<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Mit <?php echo htmlspecialchars($label); ?> anmelden</h1>

<div class="card">
    <p class="mock-notice">
        <strong>Demo:</strong> Die <?php echo htmlspecialchars($label); ?>-Anmeldung ist noch nicht angebunden.
    </p>

    <p class="switch-auth"><a href="/">Zurück zum Login</a></p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
