<?php
require '../config/connect.php'; // Připojení k databázi

$action = $_POST['action'] ?? null;

switch ($action) {
    case 'add':
        $full_name = $_POST['full_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $hobbies = $_POST['hobbies'] ?? []; // Pokud nejsou vybrány žádné koníčky, bude to prázdné pole
        $hobbies_string = implode(',', $hobbies); // Více koníčků jako pole, spojíme je do řetězce
        insert_user($full_name, $date_of_birth, $hobbies_string);
        header('Location: users.php');
        break;

    case 'edit':
        $user_id = $_POST['user_id'];
        $full_name = $_POST['full_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $hobbies = $_POST['hobbies'] ?? [];
        $hobbies_string = implode(',', $hobbies);
        edit_user($user_id, $full_name, $date_of_birth, $hobbies_string);
        header('Location: users.php');
        break;

    case 'delete':
        if ($_POST['confirm_delete'] === 'smazat') {
            $user_id = $_POST['user_id'];
            delete_user($user_id);
        }
        header('Location: users.php');
        break;

    default:
        header('Location: ../index.php');
        break;
}


function insert_user($full_name, $date_of_birth, $hobbies) {
    global $pdo;
    // Uložení koníčků jako čárkami oddělený řetězec
    $stmt = $pdo->prepare("INSERT INTO golemos_users (full_name, date_of_birth, hobbies) VALUES (?, ?, ?)");
    $stmt->execute([$full_name, $date_of_birth, $hobbies]);
}

function edit_user($user_id, $full_name, $date_of_birth, $hobbies) {
    global $pdo;
    // Uložení koníčků jako čárkami oddělený řetězec
    $stmt = $pdo->prepare("UPDATE golemos_users SET full_name = ?, date_of_birth = ?, hobbies = ? WHERE id = ?");
    $stmt->execute([$full_name, $date_of_birth, $hobbies, $user_id]);
}

function delete_user($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM golemos_users WHERE id = ?");
    $stmt->execute([$user_id]);
}
?>
