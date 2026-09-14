<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MensaPlaner</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<nav>
    <div class="nav-inner">
        <?php if (empty($_SESSION['account_id'])): ?>
            <a href="/">Login</a>
        <?php else: ?>
            <span class="nav-account">Hallo, <?php echo htmlspecialchars($_SESSION['account_name']); ?>! <span class="nav-role"><?php echo htmlspecialchars($_SESSION['account_role'] ?? ''); ?></span></span>
        <?php endif; ?>
        <div class="nav-dropdown">
            <a href="/speiseplan" class="nav-dropdown-toggle">Speiseplan</a>
            <div class="nav-dropdown-menu">
                <div class="nav-dropdown-menu-inner">
                    <a href="/speiseplan">Aktuelle Woche</a>
                    <a href="/speiseplan/naechste">Nächste Woche</a>
                </div>
            </div>
        </div>
        <a href="/speiseplan/naechste-woche">Abstimmung</a>
        <a href="/speiseplan/warenkorb">Warenkorb<?php $cartCount = array_sum($_SESSION['cart'] ?? []); if ($cartCount > 0): ?> <span class="nav-badge"><?php echo (int) $cartCount; ?></span><?php endif; ?></a>
        <?php if (($_SESSION['account_role'] ?? '') === 'admin'): ?>
            <a href="/users">Benutzer</a>
        <?php endif; ?>
        <?php if (!empty($_SESSION['account_id'])): ?>
            <a href="/logout" class="nav-logout">Logout</a>
        <?php endif; ?>
    </div>
</nav>

<div class="container<?php echo !empty($wide) ? ' container--wide' : ''; ?>">
