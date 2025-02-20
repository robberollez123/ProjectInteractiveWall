<?php
session_start();

// Controleer of het formulier is ingediend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["user"];
    $password = $_POST["password"];

    // Maak verbinding met de database
    $link = mysqli_connect("localhost", "root", "", "interactivewall");

    // Controleer of de verbinding is gelukt
    if (!$link) {
        die("Databaseverbinding mislukt: " . mysqli_connect_error());
    }

    // Gebruik prepared statements om SQL-injectie te voorkomen
    $query = "SELECT * FROM gebruiker WHERE gebruikersnaam = ?";
    $stmt = mysqli_prepare($link, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Controleer of er een gebruiker is gevonden
    if ($row = mysqli_fetch_assoc($result)) {
        $hashedPassword = $row["wachtwoord"];

        // Controleer of het ingevoerde wachtwoord klopt
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user"] = $username; // Bewaar de gebruikersnaam in de sessie

            // Stuur de gebruiker door naar de homepagina
            header("Location: home.php");
            exit;
        } else {
            $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
        }
    } else {
        $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
    }

    // Sluit de databaseverbinding
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$welkomMessage = $ingelogd ? "Welkom, " . $_SESSION["user"] . "!" : "Welkom bij Interactive Wall!";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="headerStyle.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <nav>
            <a href="#" class="nav-title">Interactive Wall</a>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="games.php">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="serialconnection.php">Seriele connectie</a></li>
                <?php endif; ?>
                <?php if ($ingelogd): ?>
                    <a href="logout.php" class="btn btn-logout">Uitloggen</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-login">Inloggen</a>
                    <a href="register.php" class="btn btn-register">Registreren</a>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <h1 class="welcome-message <?php echo $ingelogd ? 'logged-in' : 'logged-out'; ?>">
        <?php echo htmlspecialchars($welkomMessage, ENT_QUOTES, 'UTF-8'); ?>
    </h1>

    <?php if (isset($error_message)): ?>
        <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <footer>&copy; Vives 2025 - Interactive Wall</footer>
</body>
</html>
