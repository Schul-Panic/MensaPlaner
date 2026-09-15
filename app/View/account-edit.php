<?php

use App\Controller\AdminController;

$createdAtValue = (new DateTime($account['created_at']))->format('Y-m-d\TH:i');

require __DIR__ . '/layout/header.php';
?>

<h1>Benutzer bearbeiten</h1>
<p class="intro">ID: <?php echo (int) $account['id']; ?> (nicht änderbar)</p>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<div class="card">
    <form class="login-form" method="post" action="/users/<?php echo (int) $account['id']; ?>">
        <label>
            Name
            <input type="text" name="name" value="<?php echo htmlspecialchars($account['name']); ?>" required>
        </label>
        <label>
            E-Mail
            <input type="email" name="email" value="<?php echo htmlspecialchars($account['email']); ?>" required>
        </label>
        <label>
            Rolle
            <select name="role" required>
            <?php foreach (AdminController::ROLES as $role): ?>
                <option value="<?php echo htmlspecialchars($role); ?>" <?php echo $role === $account['role'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($role); ?>
                </option>
            <?php endforeach; ?>
            </select>
        </label>
        <label>
            Erstellt am
            <input type="datetime-local" name="created_at" value="<?php echo htmlspecialchars($createdAtValue); ?>" required>
        </label>
        <label>
            Neues Passwort (optional)
            <input type="password" name="password" placeholder="Leer lassen = Passwort bleibt gleich">
        </label>
        <p class="field-hint">Das aktuelle Passwort ist als Hash gespeichert und kann aus Sicherheitsgründen nicht angezeigt werden — nur zurücksetzen.</p>
        <button type="submit">Speichern</button>
    </form>

    <p class="switch-auth"><a href="/users">Zurück zur Übersicht</a></p>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>