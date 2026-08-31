<?php

use App\Model\Dish;

$wide = true;
$columnIcons = [
    "mit_fleisch" => "🍖",
    "ohne_fleisch" => "🥦",
];

require __DIR__ . '/layout/header.php';
?>

<h1>Speiseplan <span class="week-label">Nächste Woche · <?php echo htmlspecialchars($weekLabel); ?></span></h1>

<p class="intro">Wochenmenü Montag bis Freitag &mdash; jetzt schon fürs Bestellen in den Warenkorb legen.</p>

<div class="menu-week">
<?php foreach ($weeklyMatrix as $weekday => $rows): ?>
    <div class="menu-day">
        <div class="menu-day-header">
            <span><?php echo htmlspecialchars($weekday); ?></span>
            <span class="menu-day-date">(<?php echo htmlspecialchars($weekdayDates[$weekday]); ?>)</span>
        </div>

        <div class="menu-matrix-card">
          <div class="menu-matrix-scroll">
            <table class="menu-matrix">
                <thead>
                    <tr>
                        <th class="menu-matrix-corner"></th>
                        <?php foreach (Dish::COLUMN_LABELS as $columnKey => $columnLabel): ?>
                            <th class="menu-matrix-col menu-matrix-col--<?php echo htmlspecialchars($columnKey); ?>">
                                <span class="menu-matrix-col-icon"><?php echo $columnIcons[$columnKey]; ?></span>
                                <?php echo htmlspecialchars($columnLabel); ?>
                            </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach (Dish::ROW_LABELS as $rowKey => $rowLabel): ?>
                    <tr>
                        <th class="menu-matrix-row"><?php echo htmlspecialchars($rowLabel); ?></th>
                        <?php foreach (Dish::COLUMN_LABELS as $columnKey => $columnLabel): ?>
                            <?php $dish = $rows[$rowKey][$columnKey]; ?>
                            <td class="menu-matrix-cell menu-matrix-cell--<?php echo htmlspecialchars($columnKey); ?>">
                                <?php if ($dish): ?>
                                    <div class="menu-matrix-item">
                                        <div class="menu-matrix-info">
                                            <span class="menu-matrix-name"><?php echo htmlspecialchars($dish['name']); ?></span>
                                            <span class="menu-matrix-price"><?php echo htmlspecialchars($dish['price']); ?></span>
                                            <div class="menu-matrix-labels">
                                            <?php foreach ($dish['labels'] as $label): ?>
                                                <span class="dish-label"><?php echo htmlspecialchars($label); ?></span>
                                            <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <form method="post" action="/speiseplan/naechste/warenkorb">
                                            <input type="hidden" name="day" value="<?php echo htmlspecialchars($weekday); ?>">
                                            <input type="hidden" name="row" value="<?php echo htmlspecialchars($rowKey); ?>">
                                            <input type="hidden" name="column" value="<?php echo htmlspecialchars($columnKey); ?>">
                                            <button type="submit" class="cart-add-icon" aria-label="Zum Warenkorb hinzufügen">+</button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="menu-matrix-empty">–</span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
          </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>