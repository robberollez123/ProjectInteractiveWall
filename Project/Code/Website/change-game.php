<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Controleer of de gebruiker is ingelogd en admin is
$ingelogd = isset($_SESSION["user"]);
$adminUser = ($ingelogd && in_array($_SESSION["user"], ["robbe-admin", "jelle-admin"]));

// Zorg ervoor dat het spel-ID aanwezig is
if (!$adminUser || !isset($_GET["id"])) {
    header("Location: spellen.php"); // Terugsturen naar de spellenpagina als de gebruiker geen admin is of geen id heeft opgegeven
    exit();
}

$gameId = $_GET["id"];
$game = null;

// Haal het bestaande spel op
$query = "SELECT * FROM spellen WHERE id = ?";
$stmt = mysqli_prepare($linkDB, $query);
mysqli_stmt_bind_param($stmt, "i", $gameId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $game = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $naam = $_POST["naam"];
    $link = $_POST["link"];

    if (!empty($naam) && !empty($link)) {
        // Gebruik prepared statements voor veiligheid
        $updateQuery = "UPDATE spellen SET naam = ?, link = ? WHERE id = ?";
        $stmt = mysqli_prepare($linkDB, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssi", $naam, $link, $gameId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<p class='success'>Spel succesvol bijgewerkt.</p>";
            header("Location: games.php");
        } else {
            $message = "<p class='error'>Fout bij bijwerken: " . mysqli_error($linkDB) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<p class='error'>Vul zowel de naam als de link in.</p>";
    }
}

mysqli_close($linkDB);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wijzig Spel | Interactive Wall</title>
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

<h1>Wijzig Spel</h1>

<?php if (isset($message)) echo $message; ?>

<?php if ($game): ?>
    <div id="add-game-container">
        <form action="<?php echo $_SERVER["PHP_SELF"] . "?id={$gameId}"; ?>" method="post">
            <input type="text" name="naam" value="<?php echo htmlspecialchars($game['naam']); ?>" placeholder="Vul de naam van het spel in..." required>
            <input type="url" name="link" value="<?php echo htmlspecialchars($game['link']); ?>" placeholder="Vul de link naar het spel in..." required>
            <input class="game-button" type="submit" name="submit" value="Bijwerken">
        </form>
    </div>
<?php else: ?>
    <p class="error">Dit spel bestaat niet.</p>
<?php endif; ?>

<footer>&copy; Vives 2025 - Interactive Wall</footer>

<script src="../NAV PAGES/SCRIPTS/nav.js" defer></script>
</body>
</html>
