<?php
require ("functions/auth/auth.php");

if ($_POST)
{
    register_admin($_POST["full_name"], $_POST["email"] ,$_POST["password"]);
}

?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrace admina</title>
</head>
<body>
<h2>Registrace admina</h2>

<?php if (!empty($errors)): ?>
    <ul>
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<form action="#" method="post">
    <label>Celé jméno: <input type="text" name="full_name" required></label><br>
    <label>Email: <input type="email" name="email" required></label><br>
    <label>Heslo: <input type="password" name="password" required></label><br>
    <button type="submit">Registrovat</button>
</form>
</body>

</html>
