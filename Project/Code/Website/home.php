<?php
session_start();

$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1; // Haal adminstatus correct op uit de sessie

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"]; // Correcte naam uit het formulier
    $password = $_POST["password"];

    // Maak verbinding met de database
    require_once __DIR__ . '/config/database.php'; // Fix voor het pad

    // Gebruik prepared statements om SQL-injectie te voorkomen
    $query = "SELECT * FROM gebruiker WHERE gebruikersnaam = ?";
    $stmt = mysqli_prepare($linkDB, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Controleer of er een gebruiker is gevonden
    if ($row = mysqli_fetch_assoc($result)) {
        $hashedPassword = $row["wachtwoord"];

        // Controleer of het ingevoerde wachtwoord klopt
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user"] = $username; // Bewaar de gebruikersnaam in de sessie
            $_SESSION["isAdmin"] = $row["is_admin"] ?? 0; // Bewaar de adminstatus correct

            header("Location: ../home.php");
            exit;
        } else {
            $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
        }
    } else {
        $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
    }

    // Sluit de databaseverbinding
    mysqli_stmt_close($stmt);
    mysqli_close($linkDB);
}

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$welkomMessage = $ingelogd ? "Welkom, " . $_SESSION["user"] . "!" : "Welkom bij Interactive Wall!";
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/home.css">
</head>
<body>
    <header>
        <nav>
            <a href="#" class="nav-title">Interactive Wall</a>
            <button class="menu-toggle">&#9776;</button>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="games.php">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="SERIALCONNECTION/serialconnection.php">Seriele connectie</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                <?php endif; ?>
                <?php if ($adminUser): ?>
                    <li><a href="LOGIN/add-account.php">Account toevoegen</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-buttons">
                <?php if ($ingelogd): ?>
                    <span class="user-badge"><?php echo htmlspecialchars($_SESSION["user"]); ?></span>
                    <a href="LOGIN/logout.php" class="btn btn-logout">Uitloggen</a>
                <?php else: ?>
                    <a href="LOGIN/login.php" class="btn btn-login">Inloggen</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

<main>
    <h1 class="welcome-message <?php echo $ingelogd ? 'logged-in' : 'logged-out'; ?>">
        <?php echo htmlspecialchars($welkomMessage, ENT_QUOTES, 'UTF-8'); ?>
    </h1>

    <?php if (isset($error_message)): ?>
        <p class="error"> <?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?> </p>
    <?php endif; ?>

    <!-- Menu structuur voor de informatie -->
    <section class="menu">
        <div class="menu-item">
            <h2 class="menu-title">Info</h2>
            <p class="long-text hidden">De Interactive Wall is een innovatief en educatief spelplatform, speciaal ontwikkeld voor kleuters van het derde jaar basisonderwijs. Het platform maakt gebruik van capacitieve drukknoppen waarop kinderen afbeeldingen kunnen plaatsen en interactief spel kunnen spelen. Dit stimuleert de cognitieve en motorische ontwikkeling van kinderen en helpt bij het aanleren van verschillende concepten zoals kleuren, vormen, getallen en meer.</p>
        </div>

        <div class="menu-item">
            <h2 class="menu-title">Hoe werkt het?</h2>
            <p class="long-text hidden">De Interactive Wall is een innovatief educatief spelplatform dat bestaat uit 12 capacitieve drukknoppen waarop verschillende afbeeldingen kunnen worden geplakt. Elke knop activeert een specifieke functie of leertaken die door kinderen kunnen worden gebruikt om bepaalde vaardigheden te ontwikkelen. De wall kan worden gepersonaliseerd met verschillende spellen die aansluiten bij de leerdoelen van de kinderen.</p>
        </div>

        <div class="menu-item">
            <h2 class="menu-title">Voor leerkrachten</h2>
            <p class="long-text hidden">Leerkrachten kunnen zelf spelletjes aanpassen via de website, zodat de Interactive Wall aangepast kan worden aan de lesinhoud. Dit geeft leerkrachten de mogelijkheid om het platform te gebruiken voor verschillende onderwijsdoelen, zoals taal, rekenen, en sociale vaardigheden. De spellen kunnen eenvoudig worden geconfigureerd, zodat ze aansluiten bij de specifieke behoeften van de leerlingen.</p>
        </div>

        <div class="menu-item">
            <h2 class="menu-title">Ontwikkeling</h2>
            <p class="long-text hidden">Dit project is ontwikkeld door vijf studenten van de Hogeschool VIVES in het kader van het vak Project. Het doel was om een educatief platform te creëren dat de interactie tussen kinderen en technologie bevordert. De Interactive Wall combineert hardware en software om een speelse en leerzame ervaring te bieden. Het platform is ontwikkeld met open-source technologieën en is bedoeld om in scholen te worden ingezet voor educatieve doeleinden.</p>
        </div>
    </section>

    <p class="stay-updated">Blijf op de hoogte en ontdek binnenkort de eerste spelletjes!</p>
</main>



    <footer>
        &copy; Vives 2025 - Interactive Wall
    </footer>

    <script src="NAV PAGES/SCRIPTS/nav.js" defer></script>
    <script>
document.querySelectorAll('.menu-title').forEach(title => {
    title.addEventListener('click', function() {
        const menuItem = this.closest('.menu-item');
        const longText = menuItem.querySelector('.long-text');
        
        // Wissel tussen tonen en verbergen van de lange tekst
        longText.style.display = (longText.style.display === 'none' || longText.style.display === '') ? 'block' : 'none';
    });
});

    </script>
</body>
</html>
