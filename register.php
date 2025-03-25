<?php
require ("functions/auth/auth.php");

$errors = [];
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $result = register_admin($_POST["full_name"], $_POST["email"], $_POST["password"]);

    if ($result === true) {
        $successMessage = "Registrace proběhla úspěšně!";
        $_POST = []; // Vymaže hodnoty formuláře
    } else {
        $errors = $result;
    }
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

<?php if (!empty($successMessage)): ?>
    <p style="color: green;"><?= htmlspecialchars($successMessage) ?></p>
<?php endif; ?>

<?php if (!empty($errors)): ?>
    <ul style="color: red;">
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
