<?php

use App\Model\Dish;

require __DIR__ . '/layout/header.php';
?>

<h1>Speiseplan <span class="week-label"><?php echo htmlspecialchars($weekLabel); ?></span></h1>
<p class="intro">Wochenmenü Montag bis Freitag</p>

<div class="menu-week">
<?php foreach ($weeklyMenu as $weekday => $categories): ?>
    <div class="menu-day<?php echo $weekday === $today ? ' menu-day--today' : ''; ?>">
        <div class="menu-day-header">
            <span><?php echo htmlspecialchars($weekday); ?></span>
            <span class="menu-day-date">(<?php echo htmlspecialchars($weekdayDates[$weekday]); ?>)</span>
            <?php if ($weekday === $today): ?>
                <span class="menu-today-badge">heute</span>
            <?php endif; ?>
        </div>

        <?php foreach ($categories as $category => $dishes): ?>
            <div class="menu-category">
                <h3 class="menu-category-title"><?php echo htmlspecialchars(Dish::CATEGORY_LABELS[$category]); ?></h3>
                <ul class="menu-dish-list">
                <?php foreach ($dishes as $dish): ?>
                    <li>
                        <span class="menu-dish-name">
                            <?php echo htmlspecialchars($dish['name']); ?>
                            <?php if ($dish['vegan']): ?>
                                <span class="menu-veg-badge" title="Vegan">🌱</span>
                            <?php endif; ?>
                        </span>
                        <span class="menu-dish-leader"></span>
                        <span class="menu-dish-price"><?php echo htmlspecialchars($dish['price']); ?></span>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>