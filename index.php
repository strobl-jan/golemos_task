<?php require "header.php"; ?>
?>
<!-- Horní menu -->
<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Aplikace Golemos</span>
        <a href="logout.php" class="btn btn-danger">Odhlásit se</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Levé menu -->
        <nav class="col-md-2 d-none d-md-block bg-light sidebar">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#">Uživatelé</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Objednávky</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Koníčky</a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Hlavní obsah -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <h2 class="mt-4">Seznam uživatelů</h2>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th>Jméno</th>
                    <th>Datum narození</th>
                    <th>Seznam koníčků</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>Jan Novák</td>
                    <td>1990-05-14</td>
                    <td>Fotbal, Čtení</td>
                </tr>
                <tr>
                    <td>Petra Dvořáková</td>
                    <td>1985-09-23</td>
                    <td>Cestování, Malování</td>
                </tr>
                <tr>
                    <td>Martin Kučera</td>
                    <td>1992-12-01</td>
                    <td>Programování, Hudba</td>
                </tr>
                </tbody>
            </table>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
