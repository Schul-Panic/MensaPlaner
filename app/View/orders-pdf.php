<?php

use App\Model\Dish;

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #22292b;
            font-size: 12px;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 4px;
        }

        .week-label {
            font-size: 13px;
            font-weight: normal;
            color: #6b7573;
        }

        h2 {
            font-size: 14px;
            margin: 24px 0 8px;
            border-bottom: 1px solid #e2e6e4;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 6px 8px;
            border-bottom: 1px solid #e2e6e4;
        }

        th {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7573;
        }

        td.quantity, th.quantity {
            width: 100px;
            font-weight: bold;
        }

        .empty {
            color: #6b7573;
            font-style: italic;
        }
    </style>
</head>
<body>
    <h1>Bestellübersicht <span class="week-label"><?php echo htmlspecialchars($weekLabel); ?></span></h1>

    <?php foreach ($dishesByCategory as $category => $dishes): ?>
        <h2><?php echo htmlspecialchars(Dish::ROW_LABELS[$category]); ?></h2>
        <?php if (!$dishes): ?>
            <p class="empty">Noch keine Bestellungen in dieser Kategorie.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Gericht</th>
                        <th class="quantity">Bestellte Menge</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($dishes as $dish): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($dish['name']); ?></td>
                        <td class="quantity"><?php echo (int) $dish['quantity']; ?>x</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($uncategorized): ?>
        <h2>Sonstige</h2>
        <table>
            <thead>
                <tr>
                    <th>Gericht</th>
                    <th class="quantity">Bestellte Menge</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($uncategorized as $dish): ?>
                <tr>
                    <td><?php echo htmlspecialchars($dish['name']); ?></td>
                    <td class="quantity"><?php echo (int) $dish['quantity']; ?>x</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
