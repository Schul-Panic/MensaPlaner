<?php

use App\Model\Dish;

$wide = true;

require __DIR__ . '/layout/header.php';
?>

<h1>Bestellübersicht <span class="week-label"><?php echo htmlspecialchars($weekLabel); ?></span></h1>

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
                        <td><?php echo htmlspecialchars($dish['name']); ?></td>
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

<!--<a href="/speiseplan/bestellungen/pdf" class="button-download">📄 PDF herunterladen</a>-->

<?php require __DIR__ . '/layout/footer.php'; ?>
