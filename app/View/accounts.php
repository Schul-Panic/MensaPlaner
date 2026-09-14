<?php

$wide = true;

require __DIR__ . '/layout/header.php';
?>

<h1>Benutzer</h1>
<p class="intro">Alle registrierten Accounts &mdash; nur für Admins sichtbar.</p>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<div class="card">
    <div class="menu-matrix-scroll">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>E-Mail</th>
                    <th>Rolle</th>
                    <th>Erstellt am</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($accounts as $account): ?>
                <tr>
                    <td><?php echo (int) $account['id']; ?></td>
                    <td><?php echo htmlspecialchars($account['name']); ?></td>
                    <td><?php echo htmlspecialchars($account['email']); ?></td>
                    <td><span class="nav-role role-<?php echo htmlspecialchars($account['role']); ?>"><?php echo htmlspecialchars($account['role']); ?></span></td>
                    <td><?php echo htmlspecialchars(substr($account['created_at'], 0, 16)); ?></td>
                    <td class="data-table-actions">
                        <a href="/users/<?php echo (int) $account['id']; ?>/edit">Bearbeiten</a>
                        <form method="post" action="/users/<?php echo (int) $account['id']; ?>/delete">
                            <button type="submit" aria-label="Löschen">✕</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>