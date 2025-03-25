<?php
require 'functions/hobbies.php'; // Připojení k databázi
// Zpracování požadavku na přidání nebo smazání koníčku
$action = $_POST['action'] ?? null;

if ($action) {
    switch ($action) {
        case 'add':
            add_hobby();
            break;
        case 'delete':
            delete_hobby();
            break;
        default:
            header('Location: hobbies.php');
            exit();
    }
}
?>

<?php require "header.php"; ?>

        <!-- Hlavní obsah -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
<!-- HTML část pro stránku koníčků -->
    <h2 class="mt-4">Správa koníčků</h2>

    <!-- Formulář pro přidání koníčku -->
    <div class="mb-3">
        <h4>Přidat nový koníček</h4>
        <form action="hobbies.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Název koníčku</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="position" class="form-label">Pozice (pro řazení)</label>
                <input type="number" name="position" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Přidat koníček</button>
            <input type="hidden" name="action" value="add">
        </form>
    </div>

    <!-- Seznam koníčků -->
    <h4>Existující koníčky</h4>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Název</th>
            <th>Pozice</th>
            <th>Akce</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Načteme všechny koníčky z databáze
        $stmt = $pdo->query("SELECT * FROM golemos_hobbies ORDER BY position");
        while ($hobby = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                        <td>{$hobby['name']}</td>
                        <td>{$hobby['position']}</td>
                        <td>
                            <form action='hobbies.php' method='POST' style='display:inline;'>
                                <input type='hidden' name='hobby_id' value='{$hobby['id']}'>
                                <input type='hidden' name='action' value='delete'>
                                <button type='submit' class='btn btn-danger btn-sm'>Smazat</button>
                            </form>
                        </td>
                    </tr>";
        }
        ?>
        </tbody>
    </table>
</main>
</div>
            <?php
            include "footer.php";
            ?>
