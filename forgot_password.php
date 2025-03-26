<?php
require "config/connect.php";
require "config/mail_config.php"; // Soubor pro SMTP konfiguraci
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'assets/smtp/src/Exception.php';
require 'assets/smtp/src/PHPMailer.php';
require 'assets/smtp/src/SMTP.php';
function send_my_mail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = SMTP_SECURE;
        $mail->Port = SMTP_PORT;

        $mail->setFrom(SMTP_FROM, "Podpora");
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->CharSet = "UTF-8";
        $mail->Subject = $subject;
        $mail->Body = $message;

        return $mail->send();
    } catch (Exception $e) {
        return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("SELECT id FROM golemos_admins WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE golemos_admins SET reset_token = ?, token_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $user['id']]);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'];
        $resetLink = $protocol . $host . "/golemos/reset_password.php?token=$token";

        $emailBody = "Klikněte na následující odkaz pro obnovení hesla: <a href='$resetLink'>$resetLink</a>";

        if (send_my_mail($email, "Obnova hesla", $emailBody)) {
            $message = "Na váš email byl odeslán odkaz na obnovení hesla.";
        } else {
            $error = "Nepodařilo se odeslat email.";
        }
    } else {
        $error = "Tento email není v naší databázi.";
    }
}
require "header-log.php";
?>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
<div class="card p-4 shadow" style="width: 350px;">
    <h2 class="text-center">Obnova hesla</h2>
    <?php if (!empty($message)) echo "<p class='success'>$message</p>"; ?>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form action="forgot_password.php" method="post">
        <div class="mb-3">
        <label for="email" class="form-label">Zadejte svůj email:</label>
        <input type="email" name="email" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Odeslat</button>
    </form>
</div>
</body>
</html>
