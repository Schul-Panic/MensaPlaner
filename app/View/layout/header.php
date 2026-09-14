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
        <?php if (!empty($_SESSION['account_id'])): ?>
            <span class="nav-account">Angemeldet als <?php echo htmlspecialchars($_SESSION['account_name']); ?></span>
            <a href="/logout">Logout</a>
        <?php else: ?>
            <a href="/">Login</a>
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
        <a href="/users">Benutzer</a>
    </div>
</nav>

<div class="container<?php echo !empty($wide) ? ' container--wide' : ''; ?>">
