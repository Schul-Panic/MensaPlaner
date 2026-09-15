<?php require __DIR__ . '/layout/header.php'; ?>

<h1>Warenkorb</h1>

<?php if (!empty($orderPlaced)): ?>
    <div class="card cart-success">
        <p>Danke! Deine Bestellung für nächste Woche wurde aufgenommen.</p>
    </div>
    <p class="switch-auth"><a href="/speiseplan/naechste">Zurück zum Speiseplan</a></p>
<?php elseif (empty($cartItems)): ?>
    <div class="card">
        <p class="cart-empty">Dein Warenkorb ist leer.</p>
    </div>
    <p class="switch-auth"><a href="/speiseplan/naechste">Gerichte für nächste Woche ansehen</a></p>
<?php else: ?>
    <div class="card">
        <ul class="menu-dish-list">
        <?php foreach ($cartItems as $item): ?>
            <li>
                <span class="menu-dish-name">
                    <?php echo htmlspecialchars($item['name']); ?>
                    <span class="cart-quantity">×<?php echo (int) $item['quantity']; ?></span>
                </span>
                <span class="menu-dish-leader"></span>
                <span class="menu-dish-price"><?php echo htmlspecialchars($item['lineTotal']); ?></span>
                <form method="post" action="/speiseplan/warenkorb/entfernen">
                    <input type="hidden" name="day" value="<?php echo htmlspecialchars($item['day']); ?>">
                    <input type="hidden" name="row" value="<?php echo htmlspecialchars($item['row']); ?>">
                    <input type="hidden" name="column" value="<?php echo htmlspecialchars($item['column']); ?>">
                    <button type="submit" class="cart-remove-button" aria-label="Entfernen">✕</button>
                </form>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>

    <div class="card cart-total">
        <span>Gesamt</span>
        <span class="cart-total-price"><?php echo htmlspecialchars($totalFormatted); ?></span>
    </div>

    <form method="post" action="/speiseplan/warenkorb/bestellen">
        <button type="submit" class="cart-order-button">Bestellen</button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/layout/footer.php'; ?>