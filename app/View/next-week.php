<?php

use App\Model\Dish;
use App\Model\Wish;

require __DIR__ . '/layout/header.php';
?>

<h1>Abstimmung <span class="week-label"><?php echo htmlspecialchars($weekLabel); ?></span></h1>
<p class="intro">Stimm für deine Wunschgerichte für nächste Woche ab.</p>

<?php foreach ($dishesByCategory as $category => $dishes): ?>
    <div class="card">
        <h2 class="menu-section-title"><?php echo htmlspecialchars(Dish::CATEGORY_LABELS[$category]); ?></h2>
        <ul class="menu-dish-list">
        <?php foreach ($dishes as $dish): ?>
            <?php $choice = $votedChoices[$dish['id']] ?? null; ?>
            <li>
                <div class="menu-dish-info">
                    <span class="menu-dish-name">
                        <?php echo htmlspecialchars($dish['name']); ?>
                        <?php if ($dish['vegan']): ?>
                            <span class="menu-veg-badge" title="Vegan">🌱</span>
                        <?php endif; ?>
                    </span>
                    <div class="menu-matrix-labels">
                    <?php foreach ($dish['labels'] as $label): ?>
                        <span class="dish-label<?php echo $label === 'Vegan' ? ' dish-label--vegan' : ''; ?>"><?php echo $label === 'Vegan' ? '🌿 ' : ''; ?><?php echo htmlspecialchars($label); ?></span>
                    <?php endforeach; ?>
                    </div>
                </div>
                <span class="menu-dish-leader"></span>
                <span class="vote-buttons">
                    <form method="post" action="/speiseplan/naechste-woche/vote/<?php echo (int) $dish['id']; ?>/up">
                        <button type="submit" class="vote-button vote-button--up<?php echo $choice === 'up' ? ' vote-button--active' : ''; ?>">
                            👍 <?php echo (int) ($votes[$dish['id']]['up'] ?? 0); ?>
                        </button>
                    </form>
                    <form method="post" action="/speiseplan/naechste-woche/vote/<?php echo (int) $dish['id']; ?>/down">
                        <button type="submit" class="vote-button vote-button--down<?php echo $choice === 'down' ? ' vote-button--active' : ''; ?>">
                            👎 <?php echo (int) ($votes[$dish['id']]['down'] ?? 0); ?>
                        </button>
                    </form>
                </span>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>

<div class="card" id="wunsch">
    <h2 class="menu-section-title">Dein Wunsch</h2>
    <p class="intro">Fehlt dir ein Gericht? Schreib uns, was du gern essen möchtest.</p>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <p class="flash-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></p>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <p class="flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></p>
    <?php endif; ?>

    <form method="post" action="/speiseplan/naechste-woche/wunsch" class="wish-form">
        <textarea name="wish" rows="3" maxlength="<?php echo Wish::MAX_LENGTH; ?>" placeholder="z. B. Lasagne, mehr vegane Nachtische …" required></textarea>
        <button type="submit">Wunsch senden</button>
    </form>
</div>

<?php if ($isStaff): ?>
    <div class="card">
        <h2 class="menu-section-title">Eingegangene Wünsche</h2>
        <?php if (!$wishes): ?>
            <p class="menu-matrix-empty">Noch keine Wünsche.</p>
        <?php else: ?>
            <ul class="wish-list">
            <?php foreach ($wishes as $wish): ?>
                <li>
                    <p><?php echo nl2br(htmlspecialchars($wish['text'])); ?></p>
                    <span class="wish-meta"><?php echo htmlspecialchars($wish['account_name'] ?? 'Gelöschter Account'); ?> · <?php echo htmlspecialchars(substr($wish['created_at'], 0, 16)); ?></span>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>