<?php
require "config/connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['token'], $_POST['password'])) {
    $token = $_POST['token'];
    $newPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("SELECT id FROM golemos_admins WHERE reset_token = ? AND token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $stmt = $pdo->prepare("UPDATE golemos_admins SET password = ?, reset_token = NULL, token_expires = NULL WHERE id = ?");
        $stmt->execute([$newPassword, $user['id']]);

        $message = "Vaše heslo bylo úspěšně obnoveno.";
    } else {
        $error = "Neplatný nebo vypršený token.";
    }
} elseif (!isset($_GET['token'])) {
    die("Neplatný přístup.");
}
require "header-log.php";
?>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
<div class="card p-4 shadow" style="width: 350px;">
    <h2 class="text-center">Reset hesla</h2>
    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form action="reset_password.php" method="post">
        <div class="mb-3">
        <input type="hidden" name="token" class="form-control" value="<?= htmlspecialchars($_GET['token']) ?>">
        </div>
        <div class="mb-3">
        <label for="password" class="form-control">Nové heslo:</label>
        <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Obnovit heslo</button>
        <br />
        <a href="login.php" type="submit" class="btn btn-secondary w-100">Zpět na přihlášení</a>
    </form>

</div>
</body>
</html>
