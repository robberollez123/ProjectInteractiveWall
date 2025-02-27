<?php
session_start();

$ingelogd = isset($_SESSION["user"]);
$adminUser = ($ingelogd && in_array($_SESSION["user"], ["robbe-admin", "jelle-admin"]));

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["user"];
    $password = $_POST["password"];

    // Maak verbinding met de database
    require_once __DIR__ . 'config/database.php';

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
    <title>Home | Interactive Wall</title>
    <link rel="stylesheet" href="../NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="../NAV PAGES/CSS/home.css">
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
                    <li><a href="../SERIALCONNECTION/serialconnection.php">Seriele connectie</a></li>
                    <li><a href="feedback.php">Feedback</a></li>
                <?php endif; ?>
                <?php if ($adminUser): ?>
                    <li><a href="../LOGIN/add-account.php">Account toevoegen</a></li>
                <?php endif; ?>
            </ul>
            <div class="nav-buttons">
                <?php if ($ingelogd): ?>
                    <!-- Gebruikersnaam als badge -->
                    <span class="user-badge"><?php echo htmlspecialchars($_SESSION["user"]); ?></span>
                    <a href="../LOGIN/logout.php" class="btn btn-logout">Uitloggen</a>
                <?php else: ?>
                    <a href="../LOGIN/login.php" class="btn btn-login">Inloggen</a>
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
        <section class="info-card">
                <h2>Welkom bij de Interactive Wall!</h2>
                <p>De <strong>Interactive Wall</strong> is een educatief spelplatform ontworpen voor kleuters van het derde jaar. 
                Met capacitieve drukknoppen kunnen kinderen spelenderwijs leren en hun motorische vaardigheden ontwikkelen.</p>
            </section>

            <section class="info-card">
                <h2>Hoe werkt het?</h2>
                <p>De Interactive Wall bestaat uit 12 drukknoppen waarop afbeeldingen kunnen worden geplakt. 
                De knoppen zijn gekoppeld aan een website op een <strong>Raspberry Pi 5</strong>, verbonden met een scherm. 
                Kinderen kunnen reageren op de spelletjes door op de juiste knoppen te drukken.</p>
            </section>

            <section class="info-card">
                <h2>Voor leerkrachten</h2>
                <p>Leerkrachten kunnen <strong>zelf spelletjes toevoegen</strong> via de website, zodat de Interactive Wall 
                aangepast kan worden aan de lesinhoud. Elk spel heeft een korte uitleg voor de kinderen.</p>
            </section>

            <section class="info-card">
                <h2>De technologie achter het project</h2>
                <p>Dit project is ontwikkeld door vijf studenten van de <strong>Hogeschool VIVES</strong> in het kader van het vak <strong>Project</strong>. 
                Het team bestaat uit:</p>
                <ul>
                    <li>2 ICT-studenten - ontwikkeling van de website en software</li>
                    <li>2 Elektronica-studenten - ontwikkeling van de hardware en knoppen</li>
                    <li>1 AI-student - onderzoek naar slimme interacties</li>
                </ul>
            </section>
            <p class="stay-updated">Blijf op de hoogte en ontdek binnenkort de eerste spelletjes!</p>
        </main>

    <footer>
        &copy; Vives 2025 - Interactive Wall
    </footer>

    <script src="../NAV PAGES/SCRIPTS/nav.js" defer></script>
    </body>
</html>
