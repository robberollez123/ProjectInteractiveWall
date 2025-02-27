<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$adminUser = ($ingelogd && in_array($_SESSION["user"], ["robbe-admin", "jelle-admin"]));

// Controleer of het formulier is verzonden en of de gebruiker "robberollez" is
if ($adminUser && isset($_POST["submit"])) {
    if (!empty($_POST["naam"]) && !empty($_POST["link"])) {
        // Gebruik prepared statements voor veiligheid
        $query = "INSERT INTO spellen (naam, link) VALUES (?, ?)";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "ss", $_POST["naam"], $_POST["link"]);
        
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
    
    if (mysqli_stmt_execute($stmt)) {
        $message = "<p class='success'>Spel succesvol verwijderd.</p>";
    } else {
        $message = "<p class='error'>Fout bij verwijderen: " . mysqli_error($linkDB) . "</p>";
    }
    
    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spellen | Interactive Wall</title>
    <link rel="stylesheet" href="../NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/gamesTable.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/login.css">
</head>
<body>
<header>
    <nav>
        <a href="../home.php" class="nav-title">Interactive Wall</a>
        <button class="menu-toggle">&#9776;</button>
        <ul class="nav-links">
            <li><a href="../home.php">Home</a></li>
            <li><a href="#">Spellen</a></li>
            <?php if ($ingelogd): ?>
                <li><a href="../SERIALCONNECTION/serialconnection.php">Seriele connectie</a></li>
                <li><a href="feedback.php">Feedback</a></li>
            <?php endif; ?>
            <?php if ($adminUser): ?>
                <li><a href="../LOGIN/add-account.php">Account toevoegen</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-buttons">
            <?php if ($ingelogd): ?>
                <span class="user-badge"><?php echo htmlspecialchars($_SESSION["user"]); ?></span>
                <a href="../LOGIN/logout.php" class="btn btn-logout">Uitloggen</a>
            <?php else: ?>
                <a href="../LOGIN/login.php" class="btn btn-login">Inloggen</a>
            <?php endif; ?>
        </div>
    </nav>
</header>

<h1>Info</h1>
<p>Hier kan je een spel uitkiezen en spelen.</p>

<?php if ($adminUser): ?>
    <h1>Voeg een nieuw spel toe</h1>
    <div id="add-game-container">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <input type="text" name="naam" placeholder="Vul de naam van het spel in..." required>
            <input type="url" name="link" placeholder="Vul de link naar het spel in..." required>
            <input class="game-button" type="submit" name="submit" value="Toevoegen">
        </form>
    </div>
    <?php if (isset($message)) echo $message; ?>
<?php endif; ?>

<h1>Spellen</h1>
    <table class="games-table">
        <tr>
            <th>Spelnaam</th>
            <th></th>
            <?php if ($adminUser): ?>
                <!-- De wijzig- en verwijderknoppen komen naast de speelknop -->
            <?php endif; ?>
        </tr>
        <?php
            $result = mysqli_query($linkDB, "SELECT * FROM spellen");
            while ($record = mysqli_fetch_array($result)) {
                echo "<tr>
                        <td>{$record['naam']}</td>
                        <td class='action-buttons'>
                            <a class='game-button play' href=\"{$record['link']}\" target='_blank'>Speel</a>";

                if ($adminUser) {
                    // Wijzig- en verwijderknoppen komen hier
                    echo "<a class='game-button edit' href=\"change-game.php?id={$record['id']}\">Wijzig</a>
                        <a class='game-button delete' href=\"?delete={$record['id']}\" onclick='return confirm(\"Weet je zeker dat je dit spel wilt verwijderen?\")'>Verwijder</a>";
                }
                
                echo "</td>
                    </tr>";
            }
        ?>
    </table>
<footer>&copy; Vives 2025 - Interactive Wall</footer>

<script src="../NAV PAGES/SCRIPTS/nav.js" defer></script>
</body>
</html>
