<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$adminUser = ($ingelogd && in_array($_SESSION["user"], ["robbe-admin", "jelle-admin"]));

// Databaseverbinding
require_once __DIR__ . '/../config/database.php';

// Controleer of het formulier is verzonden en of de gebruiker een admin is
if ($adminUser && isset($_POST["submit"])) {
    if (!empty($_POST["naam"]) && !empty($_POST["link"])) {
        // Gebruik prepared statements voor veiligheid
        $query = "INSERT INTO spellen (naam, link) VALUES (?, ?)";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "ss", $_POST["naam"], $_POST["link"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "<p class='success'>Spel succesvol toegevoegd!</p>";
        } else {
            $message = "<p class='error'>Fout bij toevoegen: " . mysqli_error($linkDB) . "</p>";
        }
        
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SerialPort Website Test</title>
    <link rel="stylesheet" href="../NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="serialDataTable.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="serialConnection.css">
</head>
<body>
<header>
        <nav>
            <a href="../NAV PAGES/PHP/home.php" class="nav-title">Interactive Wall</a>
            <button class="menu-toggle">&#9776;</button>
            <ul class="nav-links">
                <li><a href="../home.php">Home</a></li>
                <li><a href="../games.php">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="#">Seriele connectie</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-buttons">
                <?php if ($ingelogd): ?>
                    <span class="user-badge"><?php echo htmlspecialchars($_SESSION["user"]); ?></span>
                    <a href="../LOGIN/logout.php" class="btn btn-logout">Uitloggen</a>
                <?php else: ?>
                    <a href="../LOGIN/login.php" class="btn btn-login">Inloggen</a>
                    <a href="../LOGIN/register.php" class="btn btn-register">Registreren</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <h1>Serial Port Website Test</h1>

    <div class="container">
        <button id="connectButton">Connect</button>
        <div id="connectionStatus">Status: Not connected</div>
        <div id="receivedData">Received Data: <span id="dataDisplay">None</span></div>
    </div>
    <!-- Data Table -->
    <!-- Container voor de ingelezen data tabel -->
    <div id="data-table-container">
        <table>
            <thead>
                <tr>
                    <th id="sortTime" style="cursor: pointer;">Tijdstip</th>
                    <th id="sortData" style="cursor: pointer;">Ontvangen Data</th>
                </tr>
            </thead>            
            <tbody>
                <!-- De rijen met data worden hier dynamisch toegevoegd -->
            </tbody>
        </table>
        
        <!-- Paginering -->
        <div id="pagination">
            <button id="prevPage" class="pagination-button" disabled>Vorige</button>
            <span id="pageNumber">Pagina 1 van 1</span>
            <button id="nextPage" class="pagination-button" disabled>Volgende</button>
        </div>
    </div>


    
    <footer>&copy; Vives 2025 - Interactive Wall</footer>
    <script src="script.js"></script>
    <script src="../NAV PAGES/SCRIPTS/nav.js"></script>
</body>
</html>
