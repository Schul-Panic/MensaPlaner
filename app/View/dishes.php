<?php

use App\Model\Dish;

$wide = true;
$columnIcons = [
    "mit_fleisch" => "🍖",
    "ohne_fleisch" => "🥦",
];

require __DIR__ . '/layout/header.php';
?>

<h1>Speiseplan <span class="week-label">Aktuelle Woche · <?php echo htmlspecialchars($weekLabel); ?></span></h1>

<p class="intro">Wochenmenü Montag bis Freitag</p>

<div class="menu-week">
<?php foreach ($weeklyMatrix as $weekday => $rows): ?>
    <div class="menu-day<?php echo $weekday === $today ? ' menu-day--today' : ''; ?>">
        <div class="menu-day-header">
            <span><?php echo htmlspecialchars($weekday); ?></span>
            <span class="menu-day-date">(<?php echo htmlspecialchars($weekdayDates[$weekday]); ?>)</span>
            <?php if ($weekday === $today): ?>
                <span class="menu-today-badge">heute</span>
            <?php endif; ?>
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
                                    <span class="menu-matrix-name"><?php echo htmlspecialchars($dish['name']); ?></span>
                                    <span class="menu-matrix-price"><?php echo htmlspecialchars($dish['price']); ?></span>
                                    <div class="menu-matrix-labels">
                                    <?php foreach ($dish['labels'] as $label): ?>
                                        <span class="dish-label<?php echo $label === 'Vegan' ? ' dish-label--vegan' : ''; ?>"><?php echo $label === 'Vegan' ? '🌿 ' : ''; ?><?php echo htmlspecialchars($label); ?></span>
                                    <?php endforeach; ?>
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