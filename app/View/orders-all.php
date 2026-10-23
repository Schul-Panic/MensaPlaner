<?php

$wide = true;

require __DIR__ . '/layout/header.php';
?>

<h1>Alle Bestellungen</h1>

<div class="card">
    <?php if (!$orders): ?>
        <p class="menu-matrix-empty">Noch keine Bestellungen.</p>
    <?php else: ?>
        <div class="menu-matrix-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nr.</th>
                        <th>Bestellt am</th>
                        <th>Von</th>
                        <th>Gerichte</th>
                        <th>Summe</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?php echo (int) $order['id']; ?></td>
                        <td><?php echo htmlspecialchars(substr($order['created_at'], 0, 16)); ?></td>
                        <td>
                            <?php if ($order['account_name'] === null): ?>
                                <em>Gelöschter Account</em>
                            <?php else: ?>
                                <?php echo htmlspecialchars($order['account_name']); ?>
                                <span class="nav-role role-<?php echo htmlspecialchars($order['account_role']); ?>"><?php echo htmlspecialchars($order['account_role']); ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php foreach ($order['items'] as $item): ?>
                                <div><?php echo (int) $item['quantity']; ?>x <?php echo htmlspecialchars($item['dish_name']); ?></div>
                            <?php endforeach; ?>
                        </td>
                        <td class="order-quantity"><?php echo htmlspecialchars($order['totalFormatted']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
