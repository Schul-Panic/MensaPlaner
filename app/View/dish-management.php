<?php

use App\Model\Dish;

require __DIR__ . '/layout/header.php';
?>

<h1>Gerichte verwalten</h1>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
<?php endif; ?>

<a href="/speiseplan/verwaltung/neu" class="button-download" style="margin-bottom: 20px;">+ Gericht hinzufügen</a>

<?php foreach ($dishesByCategory as $category => $dishes): ?>
    <div class="card">
        <h2 class="menu-section-title"><?php echo htmlspecialchars(Dish::ROW_LABELS[$category]); ?></h2>
        <?php if (!$dishes): ?>
            <p class="menu-matrix-empty">Noch keine Gerichte in dieser Kategorie.</p>
        <?php else: ?>
            <ul class="menu-dish-list">
            <?php foreach ($dishes as $dish): ?>
                <li>
                    <div class="menu-dish-info">
                        <span class="menu-dish-name">
                            <?php echo htmlspecialchars($dish['name']); ?>
                        </span>
                        <div class="menu-matrix-labels">
                            <span class="dish-label"><?php echo htmlspecialchars(Dish::COLUMN_LABELS[$dish['variant']] ?? $dish['variant']); ?></span>
                            <?php foreach ($dish['allergens'] as $allergen): ?>
                                <span class="dish-label"><?php echo htmlspecialchars($allergen); ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <span class="menu-dish-leader"></span>
                    <span class="menu-dish-price"><?php echo htmlspecialchars($dish['price']); ?></span>
                    <span class="vote-buttons">
                        <a href="/speiseplan/verwaltung/<?php echo (int) $dish['id']; ?>/edit" class="icon-button icon-button--edit" aria-label="Bearbeiten" title="Bearbeiten">✎</a>
                        <form method="post" action="/speiseplan/verwaltung/<?php echo (int) $dish['id']; ?>/delete" onsubmit="return confirm('„<?php echo htmlspecialchars($dish['name'], ENT_QUOTES); ?>“ wirklich löschen?');">
                            <button type="submit" class="icon-button icon-button--delete" aria-label="Löschen" title="Löschen">✕</button>
                        </form>
                    </span>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="/speiseplan/verwaltung/neu?category=<?php echo htmlspecialchars($category); ?>" class="button-download button-download--small">+ Gericht hinzufügen</a>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>
