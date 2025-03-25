<?php
require 'config/connect.php'; // Připojení k databázi

function register_admin($full_name, $email, $password)
{
    global $pdo; // Připojení k databázi
    $errors = [];

    // Ověření e-mailu
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Neplatný email.";
    }

    // Ověření hesla
    if (strlen($password) < 6) {
        $errors[] = "Heslo musí mít alespoň 6 znaků.";
    }

    // Ověření jména
    if (empty(trim($full_name))) {
        $errors[] = "Musíte zadat celé jméno.";
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

            // Uložení do databáze
            $stmt = $pdo->prepare("INSERT INTO golemos_admins (full_name, email, password, reset_token) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$full_name, $email, $hashedPassword, $resetToken])) {
                return true; // Úspěšná registrace
            } else {
                $errors[] = "Chyba při registraci.";
            }
        }
    }

    return $errors;
}


function login_admin($email, $password)
{
    global $pdo; // Připojení k databázi

    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Neplatná emailová adresa.";
    }

    if (empty($password)) {
        $errors[] = "Heslo je povinné.";
    }

    if (empty($errors)) {
        // Kontrola existence uživatele
        $stmt = $pdo->prepare("SELECT id, password FROM golemos_admins WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Uložení přihlášení do session a přesměrování
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header("Location: index.php");
            exit();
        } else {
            $errors[] = "Neplatný email nebo heslo.";
        }
    }

    return $errors; // Vrátí chyby, pokud přihlášení selže
}


?>
