<?php

// Nastavení připojení k databázi
$host = 'localhost';
$db = '';
$user = '';
$pass = '';
$charset = 'utf8mb4';

// Nastavení PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Zobrazení chyb
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // Výchozí režim načítání dat
    PDO::ATTR_EMULATE_PREPARES => false,                   // Zajištění použití skutečných připravených dotazů
];

try {
    // Vytvoření instance PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {

    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

