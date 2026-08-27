<?php

use App\Model\Dish;

require __DIR__ . '/layout/header.php';
?>

<h1>Abstimmung <span class="week-label"><?php echo htmlspecialchars($weekLabel); ?></span></h1>
<p class="intro">Stimm für deine Wunschgerichte für nächste Woche ab.</p>

<?php foreach ($dishesByCategory as $category => $dishes): ?>
    <div class="card">
        <h2 class="menu-section-title"><?php echo htmlspecialchars(Dish::CATEGORY_LABELS[$category]); ?></h2>
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
                <span class="vote-buttons">
                    <form method="post" action="/speiseplan/naechste-woche/vote/<?php echo (int) $dish['id']; ?>/up">
                        <button type="submit" class="vote-button vote-button--up">
                            👍 <?php echo (int) ($votes[$dish['id']]['up'] ?? 0); ?>
                        </button>
                    </form>
                    <form method="post" action="/speiseplan/naechste-woche/vote/<?php echo (int) $dish['id']; ?>/down">
                        <button type="submit" class="vote-button vote-button--down">
                            👎 <?php echo (int) ($votes[$dish['id']]['down'] ?? 0); ?>
                        </button>
                    </form>
                </span>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>