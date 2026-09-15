<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Benutzerliste</h1>

<div class="card">
    <ul class="user-list">
    <?php foreach ($users as $user): ?>
        <li>
            <span><?php echo htmlspecialchars($user['name']); ?></span>
            <form method="post" action="/users/<?php echo (int) $user['id']; ?>/delete">
                <button type="submit" aria-label="Löschen">✕</button>
            </form>
        </li>
    <?php endforeach; ?>
    </ul>
</div>

<div class="card">
    <form class="add-user-form" method="post" action="/users">
        <input type="text" name="name" placeholder="Name" required>
        <button type="submit">Hinzufügen</button>
    </form>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>