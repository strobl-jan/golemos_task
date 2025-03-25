<?php
require '../config/connect.php'; // Připojení k databázi
session_start();
$admin_id = $_SESSION['user_id'] ?? null; // Ověření, že je admin přihlášený

// Pokud není admin přihlášený, přesměrujeme ho na přihlášení
if (!$admin_id) {
    header('Location: ../login.php');
    exit();
}

// Zpracování změny jména
if (isset($_POST['action']) && $_POST['action'] === 'update_name') {
    change_account_name($admin_id, $_POST['full_name']);
}

// Zpracování změny hesla
if (isset($_POST['action']) && $_POST['action'] === 'update_password') {
    change_admin_pass($admin_id, $_POST['current_password'], $_POST['new_password'], $_POST['confirm_password']);
}

// Funkce pro změnu jména
function change_account_name($admin_id, $new_name)
{
    global $pdo;

    // Ověření, zda je jméno platné
    if (empty(trim($new_name))) {
        header('Location: ../settings.php?error=Jméno nemůže být prázdné.');
        exit();
    }

    // Aktualizace jména v databázi
    $stmt = $pdo->prepare("UPDATE golemos_admins SET full_name = ? WHERE id = ?");
    if ($stmt->execute([$new_name, $admin_id])) {
        header('Location: ../settings.php');
        exit();
    } else {
        header('Location: ../settings.php?error=Chyba při změně jména.');
        exit();
    }
}

// Funkce pro změnu hesla
function change_admin_pass($admin_id, $current_password, $new_password, $confirm_password)
{
    global $pdo;

    // Ověření, zda je nové heslo stejné jako potvrzené heslo
    if ($new_password !== $confirm_password) {
        header('Location: ../settings.php?error=Nové heslo a potvrzení hesla se neshodují.');
        exit();
    }

    // Ověření původního hesla
    $stmt = $pdo->prepare("SELECT password FROM golemos_admins WHERE id = ?");
    $stmt->execute([$admin_id]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($current_password, $admin['password'])) {
        header('Location: ../settings.php?error=Původní heslo je nesprávné.');
        exit();
    }

    // Hashování nového hesla
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Změna hesla v databázi
    $stmt = $pdo->prepare("UPDATE golemos_admins SET password = ? WHERE id = ?");
    if ($stmt->execute([$hashed_password, $admin_id])) {
        header('Location: ../settings.php');
        exit();
    } else {
        header('Location: ../settings.php?error=Chyba při změně hesla.');
        exit();
    }
}
