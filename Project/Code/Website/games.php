<?php
session_start();

// $linkDB = mysqli_connect("localhost", "root", "", "interactivewall") or die("Error: ".mysqli_connect_error());
require_once __DIR__ . '/config/database.php';
// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1;

if ($adminUser && isset($_POST["submit"])) {
    if (!empty($_POST["naam"]) && !empty($_POST["link"]) && !empty($_POST["uitleg"])) {
        // Gebruik prepared statements voor veiligheid
        $query = "INSERT INTO spellen (naam, link, uitleg) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "sss", $_POST["naam"], $_POST["link"], $_POST["uitleg"]);
        
        if (!mysqli_stmt_execute($stmt)) {
            $message = "<p class='error'>Fout bij toevoegen: " . mysqli_error($linkDB) . "</p>";
        }
        
        mysqli_stmt_close($stmt);
    }
}

// Verwijder een spel als de admin op de knop drukt
if ($adminUser && isset($_GET["delete"])) {
    $gameId = $_GET["delete"];
    $deleteQuery = "DELETE FROM spellen WHERE id = ?";
    $stmt = mysqli_prepare($linkDB, $deleteQuery);
    mysqli_stmt_bind_param($stmt, "i", $gameId);
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spellen</title>
    <link rel="stylesheet" href="NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/games.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<header>
    <nav>
        <a href="home.php" class="nav-title">Interactive Wall</a>
        <button class="menu-toggle">&#9776;</button>
        <ul class="nav-links">
            <li><a href="home.php">Home</a></li>
            <li><a href="#">Spellen</a></li>
            <?php if ($ingelogd): ?>
                <li><a href="SERIALCONNECTION/serialconnection.php">Seriele connectie</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            <?php endif; ?>
            <?php if ($adminUser): ?>
                <li><a href="LOGIN/add-account.php">Account toevoegen</a></li>
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
                        <a href="#">Instellingen</a>
                        <a href="LOGIN/logout.php">Uitloggen</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="LOGIN/login.php" class="btn btn-login">Inloggen</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<main>
    <h1>Info</h1>
<p>Hier kan je een spel uitkiezen en spelen.</p>

<?php if ($adminUser): ?>
    <h1>Voeg een nieuw spel toe</h1>
    <div id="add-game-container">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <label for="naam">Spelnaam</label>
            <input type="text" id="naam" name="naam" placeholder="Vul de naam van het spel in..." required>

            <label for="link">Spel Link</label>
            <input type="url" id="link" name="link" placeholder="Vul de link naar het spel in..." required>

            <label for="uitleg">Uitleg</label>
            <textarea id="uitleg" name="uitleg" placeholder="Geef een korte uitleg over het spel..." required></textarea>

            <input class="game-button" type="submit" name="submit" value="Toevoegen">
        </form>
    </div>
    <?php if (isset($message)) echo $message; ?>
<?php endif; ?>

<h1>Spellen</h1>
<div class="games-container">
    <?php
    $result = mysqli_query($linkDB, "SELECT * FROM spellen");
    while ($record = mysqli_fetch_array($result)) {
        echo "<div class='spel-wrapper'>
                <h2>{$record['naam']}</h2>
                <p>{$record['uitleg']}</p>
                <div class='admin-buttons'>
                    <a class='game-btn' href='{$record['link']}' target='_blank'>Speel</a>";

        if ($adminUser) {
            echo "<a class='game-btn edit' href='change-game.php?id={$record['id']}'>Wijzig</a>
                  <a class='game-btn delete' href='?delete={$record['id']}' onclick='return confirm(\"Weet je zeker dat je dit spel wilt verwijderen?\")'>Verwijder</a>";
        }

        echo "</div></div>";
    }
    ?>
</div>
</main>

<footer>&copy; Vives 2025 - Interactive Wall</footer>

<script src="NAV PAGES/SCRIPTS/nav.js" defer></script>
</body>
</html>
