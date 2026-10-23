<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MensaPlaner</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>

<?php if (!empty($_SESSION['account_id'])): ?>
<nav>
    <div class="nav-inner">
        <?php
            $isImpersonating = !empty($_SESSION['impersonate_id']);
            $displayName = $isImpersonating ? $_SESSION['impersonate_name'] : ($_SESSION['account_name'] ?? '');
            $displayRole = $isImpersonating ? $_SESSION['impersonate_role'] : ($_SESSION['account_role'] ?? '');
        ?>
        <div class="nav-brand">
            <a href="/speiseplan" class="nav-logo"><img src="/img/logo.svg" alt="MensaPlaner" height="40"></a>
        </div>
        <div class="nav-links">
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
            <?php if (in_array($displayRole, ['mitarbeiter', 'admin'], true)): ?>
                <div class="nav-dropdown">
                    <a href="/speiseplan/bestellungen" class="nav-dropdown-toggle">Verwaltung</a>
                    <div class="nav-dropdown-menu">
                        <div class="nav-dropdown-menu-inner">
                            <a href="/speiseplan/bestellungen">Bestellungen</a>
                            <a href="/speiseplan/bestellungen/alle">Alle Bestellungen</a>
                            <a href="/speiseplan/verwaltung">Gerichte verwalten</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($displayRole === 'admin'): ?>
                <a href="/users">Benutzer</a>
            <?php endif; ?>
        </div>
        <div class="nav-account-actions">
            <span class="nav-account">Hallo, <?php echo htmlspecialchars($displayName); ?>! <span class="nav-role role-<?php echo htmlspecialchars($displayRole); ?>"><?php echo htmlspecialchars($displayRole); ?></span></span>
            <a href="/logout" class="nav-logout">Logout</a>
            <?php if ($isImpersonating): ?>
                <form method="post" action="/stop-impersonate" class="nav-impersonate-form">
                    <button type="submit" class="nav-impersonate-stop">Als <?php echo htmlspecialchars($_SESSION['account_name']); ?> zurück</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="container<?php echo !empty($wide) ? ' container--wide' : ''; ?><?php echo empty($_SESSION['account_id']) ? ' container--auth' : ''; ?>">
