<?php
session_start();
require 'config/connect.php'; // Připojení k databázi
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikace Golemos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body>

<!-- Horní menu -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Aplikace Golemos</span>
        <!-- Odkazy pro Nastavení a Odhlášení -->
        <div class="d-flex ms-auto gap-2">
            <a href="settings.php" class="btn btn-primary">Nastavení</a>
            <a href="logout.php" class="btn btn-danger">Odhlásit se</a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Levé menu -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Uživatelé</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Objednávky</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="hobbies.php">Koníčky</a>
                    </li>
                </ul>
            </div>
        </nav>