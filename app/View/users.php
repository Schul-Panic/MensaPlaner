<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Benutzerliste</title>
</head>
<body>

<h1>Benutzerliste</h1>

<ul>
<?php foreach ($users as $user): ?>
    <li><?php echo $user['name']; ?></li>
<?php endforeach; ?>
</ul>

</body>
</html>