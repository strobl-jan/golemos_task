<?php require "header.php"; ?>



        <!-- Hlavní obsah -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h2 class="mt-4">Seznam uživatelů</h2>

            <!-- Tlačítko pro přidání uživatele -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Přidat uživatele</button>

            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Jméno</th>
                    <th>Datum narození</th>
                    <th>Seznam koníčků</th>
                    <th>Akce</th>
                </tr>
                </thead>
                <tbody>
                <?php
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
                    echo "<tr>
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
    </div>
</div>

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
    echo "
    <div class='modal fade' id='editUserModal{$user['id']}' tabindex='-1' aria-labelledby='editUserModalLabel{$user['id']}' aria-hidden='true'>
        <div class='modal-dialog'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class='modal-title' id='editUserModalLabel{$user['id']}'>Upravit uživatele</h5>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <form action='functions/users.php' method='POST'>
                    <div class='modal-body'>
                        <div class='mb-3'>
                            <label for='full_name' class='form-label'>Jméno</label>
                            <input type='text' name='full_name' class='form-control' value='{$user['full_name']}' required>
                        </div>
                        <div class='mb-3'>
                            <label for='date_of_birth' class='form-label'>Datum narození</label>
                            <input type='date' name='date_of_birth' class='form-control' value='{$user['date_of_birth']}' required>
                        </div>
                        <div class='mb-3'>
                            <label for='hobbies' class='form-label'>Koníčky</label>
                            <select name='hobbies[]' class='form-select' multiple required>
                                <option value='sport' ".(in_array('sport', explode(',', $user['hobbies'])) ? 'selected' : '').">Sport</option>
                                <option value='čtení' ".(in_array('čtení', explode(',', $user['hobbies'])) ? 'selected' : '').">Čtení</option>
                                <option value='cestování' ".(in_array('cestování', explode(',', $user['hobbies'])) ? 'selected' : '').">Cestování</option>
                                <option value='hudba' ".(in_array('hudba', explode(',', $user['hobbies'])) ? 'selected' : '').">Hudba</option>
                            </select>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Zrušit</button>
                        <button type='submit' class='btn btn-primary'>Upravit</button>
                    </div>
                    <input type='hidden' name='action' value='edit'> <!-- Určuje akci -->
                    <input type='hidden' name='user_id' value='{$user['id']}'> <!-- Určuje ID uživatele -->
                </form>
            </div>
        </div>
    </div>";
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

