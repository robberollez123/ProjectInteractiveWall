<?php
session_start();
require_once __DIR__ . '/config/database.php';

// Controleer of de gebruiker is ingelogd en admin is
$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1; 

// Zorg ervoor dat het spel-ID aanwezig is
if (!$adminUser || !isset($_GET["id"])) {
    header("Location: games.php");
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
    $uitleg = $_POST["uitleg"];
    $iconPath = $game['icon']; // Houd het huidige icon bij voor het geval er geen nieuwe afbeelding wordt geüpload

    if (!empty($naam) && !empty($link)) {
        // Verwerken van de nieuwe afbeelding
        if (isset($_FILES["icon"]) && $_FILES["icon"]["error"] == 0) {
            // Zorg ervoor dat het bestand een afbeelding is
            $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            if (in_array($_FILES["icon"]["type"], $allowedTypes)) {
                // Genereer een unieke bestandsnaam
                $iconName = uniqid("game_icon_", true) . "." . pathinfo($_FILES["icon"]["name"], PATHINFO_EXTENSION);
                $targetDir = "uploads/icons/"; // Zorg ervoor dat deze map bestaat en schrijfbaar is
                $targetFile = $targetDir . $iconName;

                // Beweeg het bestand naar de uploadmap
                if (move_uploaded_file($_FILES["icon"]["tmp_name"], $targetFile)) {
                    $iconPath = $targetFile; // Update het bestandspad
                } else {
                    $message = "<p class='error'>Fout bij het uploaden van de afbeelding.</p>";
                }
            } else {
                $message = "<p class='error'>Alleen JPEG, PNG en GIF bestanden worden toegestaan.</p>";
            }
        }

        // Gebruik prepared statements voor veiligheid
        $updateQuery = "UPDATE spellen SET naam = ?, link = ?, uitleg = ?, icon = ? WHERE id = ?";
        $stmt = mysqli_prepare($linkDB, $updateQuery);
        mysqli_stmt_bind_param($stmt, "ssssi", $naam, $link, $uitleg, $iconPath, $gameId);

        if (mysqli_stmt_execute($stmt)) {
            $message = "<p class='success'>Spel succesvol bijgewerkt.</p>";
            header("Location: games.php");
            exit();
        } else {
            $message = "<p class='error'>Fout bij bijwerken: " . mysqli_error($linkDB) . "</p>";
        }
        mysqli_stmt_close($stmt);
    } else {
        $message = "<p class='error'>Vul zowel de naam, link en uitleg in.</p>";
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
    <link rel="stylesheet" href="NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/games.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/login.css">
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

<h1>Wijzig Spel</h1>

<?php if (isset($message)) echo $message; ?>

<?php if ($game): ?>
    <div id="add-game-container">
        <form action="<?php echo $_SERVER["PHP_SELF"] . "?id={$gameId}"; ?>" method="post" enctype="multipart/form-data">
            <label for="naam">Spelnaam</label>
            <input type="text" name="naam" value="<?php echo htmlspecialchars($game['naam']); ?>" placeholder="Vul de naam van het spel in..." required>
            
            <label for="link">Spel Link</label>
            <input type="url" name="link" value="<?php echo htmlspecialchars($game['link']); ?>" placeholder="Vul de link naar het spel in..." required>
            
            <label for="uitleg">Uitleg</label>
            <textarea name="uitleg" placeholder="Geef een korte uitleg over het spel..." required><?php echo htmlspecialchars($game['uitleg']); ?></textarea>
            
            <label for="icon">Spel Icoon (Optioneel)</label>
            <input type="file" name="icon" accept="image/*">

            <input class="game-button" type="submit" name="submit" value="Bijwerken">
        </form>
    </div>
<?php else: ?>
    <p class="error">Dit spel bestaat niet.</p>
<?php endif; ?>

<footer>&copy; Vives 2025 - Interactive Wall</footer>

<script src="NAV PAGES/SCRIPTS/nav.js" defer></script>
</body>
</html>
