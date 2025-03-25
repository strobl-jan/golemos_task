<?php
function register_admin($post_email, $post_pass, $post_full_name)
{
    require 'config/connect.php'; // Připojení k databázi

    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($post_email);
        $password = $post_pass;
        $full_name = trim($post_full_name);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Neplatný email.";
        }

        if (strlen($password) < 6) {
            $errors[] = "Heslo musí mít alespoň 6 znaků.";
        }

        if (empty($full_name)) {
            $errors[] = "Jméno nesmí být prázdné.";
        }

        if (empty($errors)) {
            // Kontrola, zda email už není použitý
            $stmt = $pdo->prepare("SELECT id FROM golemos_admins WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                $errors[] = "Tento email je již registrován.";
            } else {
                // Hash hesla
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $resetToken = bin2hex(random_bytes(32)); // Generování reset tokenu
                $tokenExpires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Platnost tokenu 1 hodina

                // Uložení do databáze
                $stmt = $pdo->prepare("
                    INSERT INTO golemos_admins (email, password, full_name, reset_token, token_expires) 
                    VALUES (?, ?, ?, ?, ?)
                ");
                if ($stmt->execute([$email, $hashedPassword, $full_name, $resetToken, $tokenExpires])) {
                    echo "Registrace úspěšná!";
                    exit;
                } else {
                    $errors[] = "Chyba při registraci.";
                }
            }
        }
    }
}
?>
