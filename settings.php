<?php
require 'header.php'; // Připojení k databázi
session_start();

$admin_id = $_SESSION['user_id'] ?? null; // Ověření, že je admin přihlášený
if (!$admin_id) {
    header('Location: login.php'); // Pokud není admin přihlášený, přesměrujeme ho na přihlášení
    exit();
}

// Načteme aktuální údaje o administrátorovi pro zobrazení
$stmt = $pdo->prepare("SELECT full_name FROM golemos_admins WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Pokud je něco odesláno do funkce, zpracujeme to zde
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}

?>
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<div class="container mt-4">
    <h2>Nastavení účtu</h2>

    <!-- Formulář pro změnu jména -->
    <div class="mb-4">
        <h4>Změnit jméno</h4>
        <form action="functions/account.php" method="POST">
            <div class="mb-3">
                <label for="full_name" class="form-label">Nové jméno</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Uložit změny</button>
            <input type="hidden" name="action" value="update_name">
        </form>
    </div>

    <!-- Formulář pro změnu hesla -->
    <div class="mb-4">
        <h4>Změnit heslo</h4>
        <form action="functions/account.php" method="POST">
            <div class="mb-3">
                <label for="current_password" class="form-label">Původní heslo</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Nové heslo</label>
                <input type="password" name="new_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Nové heslo znovu</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>

            <?php
            // Zobrazíme případnou chybu
            if (isset($error_message)) {
                echo "<div class='alert alert-danger'>{$error_message}</div>";
            }
            ?>

            <button type="submit" class="btn btn-primary">Uložit změny</button>
            <input type="hidden" name="action" value="update_password">
        </form>
    </div>
</div>
</main>
