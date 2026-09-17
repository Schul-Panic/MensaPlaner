<?php

use App\Model\Dish;

$wide = true;

require __DIR__ . '/layout/header.php';
?>

<h1>Bestellübersicht</h1>
<p class="intro">Wie oft wurde was bestellt &mdash; aufgeteilt nach Hauptgericht, Beilage und Nachtisch.</p>

<div class="card order-summary">
    <div class="order-summary-stat">
        <span class="order-summary-value"><?php echo (int) $totalOrders; ?></span>
        <span class="order-summary-label">Bestellungen</span>
    </div>
    <div class="order-summary-stat">
        <span class="order-summary-value"><?php echo (int) $totalItems; ?></span>
        <span class="order-summary-label">Gerichte bestellt</span>
    </div>
    <?php if ($topDish): ?>
        <div class="order-summary-stat">
            <span class="order-summary-value">🏆 <?php echo htmlspecialchars($topDish['name']); ?></span>
            <span class="order-summary-label">Beliebtestes Gericht (<?php echo (int) $topDish['quantity']; ?>x)</span>
        </div>
    <?php endif; ?>
</div>

<?php foreach ($dishesByCategory as $category => $dishes): ?>
    <div class="card">
        <h2 class="menu-section-title"><?php echo htmlspecialchars(Dish::ROW_LABELS[$category]); ?></h2>
        <?php if (!$dishes): ?>
            <p class="menu-matrix-empty">Noch keine Bestellungen in dieser Kategorie.</p>
        <?php else: ?>
            <table class="data-table order-table">
                <thead>
                    <tr>
                        <th>Gericht</th>
                        <th>Bestellte Menge</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dishes as $dish): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($dish['name']); ?>
                            <div class="order-bar-track">
                                <div class="order-bar-fill" style="width: <?php echo $maxQuantity > 0 ? round($dish['quantity'] / $maxQuantity * 100) : 0; ?>%"></div>
                            </div>
                        </td>
                        <td class="order-quantity"><?php echo (int) $dish['quantity']; ?>x</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
<?php endforeach; ?>

<?php if ($uncategorized): ?>
    <div class="card">
        <h2 class="menu-section-title">Sonstige</h2>
        <table class="data-table order-table">
            <thead>
                <tr>
                    <th>Gericht</th>
                    <th>Bestellte Menge</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($uncategorized as $dish): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dish['name']); ?></td>
                    <td class="order-quantity"><?php echo (int) $dish['quantity']; ?>x</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>
