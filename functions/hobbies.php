<?php

function add_hobby() {
    global $pdo;

    // Validace a získání dat z formuláře
    $hobby_name = $_POST['name'] ?? null;
    $position = $_POST['position'] ?? null;

    // Zkontrolujeme, jestli jsou všechna data
    if (!$hobby_name || !$position) {
        echo "Nesprávná data.";
        exit();
    }

    // Vložení koníčku do DB
    $stmt = $pdo->prepare("INSERT INTO golemos_hobbies (name, position) VALUES (?, ?)");
    $stmt->execute([$hobby_name, $position]);

    // Přesměrování na stránku
    header('Location: hobbies.php');
    exit();
}


function delete_hobby() {
    global $pdo;

    // Získání ID koníčku pro smazání
    $hobby_id = $_POST['hobby_id'] ?? null;

    // Validace ID
    if (!$hobby_id) {
        echo "ID koníčku není platné.";
        exit();
    }

    // Smazání koníčku z DB
    $stmt = $pdo->prepare("DELETE FROM golemos_hobbies WHERE id = ?");
    $stmt->execute([$hobby_id]);

    // Přesměrování na stránku
    header('Location: hobbies.php');
    exit();
}