<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1; // Haal adminstatus correct op uit de sessie

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
    <title>Data test</title>
    <link rel="stylesheet" href="../NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="serialDataTable.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="serialConnection.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
                    <li><a href="../feedback.php">Feedback</a></li>
                <?php endif; ?>
                <?php if ($adminUser): ?>
                    <li><a href="../LOGIN/add-account.php">Account toevoegen</a></li>
                <?php endif; ?>
            </ul>
            <div class="user-menu">
                <?php if ($ingelogd): ?>
                    <div class="dropdown">
                        <span class="user-badge" onclick="toggleDropdown()">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION["user"]); ?>
                        </span>
                        <div id="dropdown-menu" class="dropdown-content">
                            <!-- TODO: setting pagina -->
                            <a href="../settings.php">Instellingen</a>
                            <a href="../LOGIN/logout.php">Uitloggen</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="../LOGIN/login.php" class="btn btn-login">Inloggen</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

<main>
        <h1>Test het ontvangen van data van de seriele poort.</h1>

    <div class="container">
        <button id="connectButton">Connect</button>
        <div id="connectionStatus">Status: Not connected</div>
        <div id="receivedData">Received Data: <span id="dataDisplay">None</span></div>
        <button id="resetButton" class="pagination-button" disabled>Reset</button>
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
</main>
    <footer>
        <div class="footer-links">
            <p class="copyText">&copy; Vives 2025 - Interactive Wall</p>
            <div class="links">
                <p>Links:</p>
                <ul>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Reacties</a></li>
                </ul>
            </div>
        </div>
    </footer>    <script src="script.js"></script>
    <script src="../NAV PAGES/SCRIPTS/nav.js"></script>
</body>
</html>
