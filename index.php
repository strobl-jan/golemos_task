<?php require "header.php"; ?>

<!-- Hlavní obsah -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <h2 class="mt-4">Seznam uživatelů</h2>

    <!-- Filtr -->
    <div class="mb-3">
        <label for="nameFilter" class="form-label">Filtrovat podle jména</label>
        <input type="text" id="nameFilter" class="form-control" placeholder="Zadejte jméno..." onkeyup="filterUsers()">
    </div>

    <!-- Filtrování dle data narození (rozmezí) -->
    <div class="mb-3">
        <label for="dobStartFilter" class="form-label">Od data narození</label>
        <input type="date" id="dobStartFilter" class="form-control" onchange="filterUsers()">
    </div>
    <div class="mb-3">
        <label for="dobEndFilter" class="form-label">Do data narození</label>
        <input type="date" id="dobEndFilter" class="form-control" onchange="filterUsers()">
    </div>

    <!-- Tlačítko pro přidání uživatele -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Přidat uživatele</button>

    <table class="table table-striped" id="usersTable">
        <thead>
        <tr>
            <th>
                <a href="#" onclick="sortTable('name')">Jméno</a>
            </th>
            <th>
                <a href="#" onclick="sortTable('dob')">Datum narození</a>
            </th>
            <th>Seznam koníčků</th>
            <th>Akce</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Předdefinovaný SQL dotaz pro získání všech uživatelů
        $stmt = $pdo->query("SELECT * FROM golemos_users");
        while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Rozdělení koníčků podle čárky (ID koníčků)
            $hobbies_ids = explode(',', $user['hobbies']);

            // Načtení názvů koníčků podle ID
            $hobbies_names = [];
            foreach ($hobbies_ids as $hobby_id) {
                $hobby_stmt = $pdo->prepare("SELECT name FROM golemos_hobbies WHERE id = ?");
                $hobby_stmt->execute([$hobby_id]);
                $hobby = $hobby_stmt->fetch(PDO::FETCH_ASSOC);
                if ($hobby) {
                    $hobbies_names[] = $hobby['name'];
                }
            }

            // Sloučení názvů koníčků do jednoho řetězce
            $hobbies_list = implode(', ', $hobbies_names);

            // Zobrazení data narození ve formátu DD.MM.YYYY
            echo "<tr data-name='{$user['full_name']}' data-dob='{$user['date_of_birth']}'>
                <td>{$user['full_name']}</td>
                <td>" . date('d.m.Y', strtotime($user['date_of_birth'])) . "</td>
                <td>{$hobbies_list}</td>
                <td>
                   <button class='btn btn-warning btn-sm' data-bs-toggle='modal' data-bs-target='#editUserModal{$user['id']}'>
                    <i class='bi bi-pencil'></i>
                   </button>
                   <button class='btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteUserModal{$user['id']}'>
                    <i class='bi bi-trash'></i> 
                   </button>
                </td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</main>

<script>
    // Filtrování uživatelů podle jména a data narození
    function filterUsers() {
        var nameFilter = document.getElementById('nameFilter').value.toLowerCase();
        var dobStartFilter = document.getElementById('dobStartFilter').value;
        var dobEndFilter = document.getElementById('dobEndFilter').value;
        var rows = document.getElementById('usersTable').getElementsByTagName('tr');

        for (var i = 1; i < rows.length; i++) {
            var nameCell = rows[i].getAttribute('data-name').toLowerCase();
            var dobCell = rows[i].getAttribute('data-dob');

            // Kontrola jména
            var nameMatch = nameCell.indexOf(nameFilter) > -1;

            // Kontrola data narození v rozmezí
            var dobMatch = true;
            if (dobStartFilter && new Date(dobCell) < new Date(dobStartFilter)) {
                dobMatch = false;
            }
            if (dobEndFilter && new Date(dobCell) > new Date(dobEndFilter)) {
                dobMatch = false;
            }

            // Zobrazit řádek pokud jméno a datum odpovídají filtru
            if (nameMatch && dobMatch) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }

    // Řazení tabulky
    function sortTable(column) {
        var table = document.getElementById("usersTable");
        var rows = Array.from(table.rows).slice(1);
        var isAscending = table.querySelector(`th a[onclick="sortTable('${column}')"]`).classList.toggle('asc', !table.querySelector('th a').classList.contains('asc'));

        rows.sort(function(a, b) {
            var cellA = a.getAttribute(`data-${column}`).toLowerCase();
            var cellB = b.getAttribute(`data-${column}`).toLowerCase();

            if (column === 'dob') {
                cellA = new Date(cellA);
                cellB = new Date(cellB);
            }

            if (isAscending) {
                return (cellA < cellB ? -1 : (cellA > cellB ? 1 : 0));
            } else {
                return (cellA > cellB ? -1 : (cellA < cellB ? 1 : 0));
            }
        });

        rows.forEach(row => table.appendChild(row));
    }
</script>


<!-- Modální okno pro přidání uživatele -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Přidat uživatele</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="functions/users.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Jméno</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Datum narození</label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hobbies" class="form-label">Koníčky</label>
                        <select name="hobbies[]" class="form-select" multiple required>
                            <?php
                            // Připojení k databázi
                            require 'config/connect.php';

                            // Načtení koníčků z databáze
                            $stmt = $pdo->query("SELECT id, name FROM golemos_hobbies ORDER BY position");

                            // Výpis koníčků do selectu
                            while ($hobby = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $hobby['id'] . "'>" . $hobby['name'] . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Zrušit</button>
                    <button type="submit" class="btn btn-primary">Přidat</button>
                </div>
                <input type="hidden" name="action" value="add"> <!-- Určuje akci -->
            </form>
        </div>
    </div>
</div>




<!-- Modální okno pro editaci uživatele -->
<?php
$stmt = $pdo->query("SELECT * FROM golemos_users");
while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    ?>
    <div class='modal fade' id='editUserModal<?php echo $user['id']; ?>' tabindex='-1'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title'>Upravit uživatele</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
                </div>
                <form action='functions/users.php' method='POST'>
                    <div class='modal-body'>
                        <div class='mb-3'>
                            <label class='form-label'>Jméno</label>
                            <input type='text' name='full_name' class='form-control' value='<?php echo $user['full_name']; ?>' required>
                        </div>
                        <div class='mb-3'>
                            <label class='form-label'>Datum narození</label>
                            <input type='date' name='date_of_birth' class='form-control' value='<?php echo $user['date_of_birth']; ?>' required>
                        </div>
                        <div class='mb-3'>
                            <label class='form-label'>Koníčky</label>
                            <select name='hobbies[]' class='form-select' multiple required>
                                <?php
                                $hobby_stmt = $pdo->query("SELECT id, name FROM golemos_hobbies");
                                while ($hobby = $hobby_stmt->fetch(PDO::FETCH_ASSOC)) {
                                    $selected = in_array($hobby['id'], explode(',', $user['hobbies'])) ? 'selected' : '';
                                    echo "<option value='{$hobby['id']}' $selected>{$hobby['name']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Zrušit</button>
                        <button type='submit' class='btn btn-primary'>Upravit</button>
                    </div>
                    <input type='hidden' name='action' value='edit'>
                    <input type='hidden' name='user_id' value='<?php echo $user['id']; ?>'>
                </form>
            </div>
        </div>
    </div>
    <?php
}
?>



<!-- Modální okno pro smazání uživatele -->
<?php
$stmt = $pdo->query("SELECT * FROM golemos_users");
while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "
    <div class='modal fade' id='deleteUserModal{$user['id']}' tabindex='-1' aria-labelledby='deleteUserModalLabel{$user['id']}' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='deleteUserModalLabel{$user['id']}'>Potvrzení smazání</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <form action='functions/users.php' method='POST'>
                    <div class='modal-body'>
                        <p>Opravdu chcete smazat uživatele <strong>{$user['full_name']}</strong>? Pro potvrzení napište 'smazat'.</p>
                        <input type='text' name='confirm_delete' class='form-control' required>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Zrušit</button>
                        <button type='submit' class='btn btn-danger'>Smazat</button>
                    </div>
                    <input type='hidden' name='action' value='delete'> <!-- Určuje akci -->
                    <input type='hidden' name='user_id' value='{$user['id']}'> <!-- Určuje ID uživatele -->
                </form>
            </div>
        </div>
    </div>";
}
?>


<?php
include "footer.php";
?>

