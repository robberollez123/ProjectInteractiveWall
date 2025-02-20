<?php
session_start();

// Controleer of de gebruiker is ingelogd
$ingelogd = isset($_SESSION["user"]);
$adminUser = ($ingelogd && $_SESSION["user"] === "robberollez");

// Databaseverbinding
$link = mysqli_connect("localhost", "root", "", "interactivewall") or die("Error: " . mysqli_connect_error());

// Controleer of het formulier is verzonden en of de gebruiker "robberollez" is
if ($adminUser && isset($_POST["submit"])) {
    if (!empty($_POST["naam"]) && !empty($_POST["link"])) {
        // Gebruik prepared statements voor veiligheid
        $query = "INSERT INTO spellen (naam, link) VALUES (?, ?)";
        $stmt = mysqli_prepare($link, $query);
        mysqli_stmt_bind_param($stmt, "ss", $_POST["naam"], $_POST["link"]);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "<p class='success'>Spel succesvol toegevoegd!</p>";
        } else {
            $message = "<p class='error'>Fout bij toevoegen: " . mysqli_error($link) . "</p>";
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
    <title>Spellen</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="gamesTable.css">
    <link rel="stylesheet" href="headerStyle.css">
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <header>
        <nav>
            <a href="home.php" class="nav-title">Interactive Wall</a>
            <ul>
            <li><a href="../PHP/home.php">Home</a></li>
                <li><a href="#">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="../../SERIALCONNECTION/serialconnection.php">Seriële connectie</a></li>
                <?php endif; ?>
                <?php if ($ingelogd): ?>
                    <a href="../../LOGIN/logout.php" class="btn btn-logout">Uitloggen</a>
                <?php else: ?>
                    <a href="../../LOGIN/login.php" class="btn btn-login">Inloggen</a>
                    <a href="../../LOGIN/register.php" class="btn btn-register">Registreren</a>
                <?php endif; ?>
            </ul>
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
    <?php elseif ($ingelogd): ?>
        <p class="error">Je hebt geen rechten om spellen toe te voegen.</p>
    <?php endif; ?>

    <h1>Alle spellen</h1>
    <table class="games-table">
        <tr>
            <th>Spelnaam</th>
            <th>Actie</th>
        </tr>
        <?php
            $result = mysqli_query($link, "SELECT * FROM spellen");
            while ($record = mysqli_fetch_array($result)) {
                echo "<tr>
                        <td>{$record['naam']}</td>
                        <td><a class='game-button' href=\"{$record['link']}\" target='_blank'>Speel</a></td>
                     </tr>";
            }
        ?>
    </table>

    <footer>&copy; Vives 2025 - Interactive Wall</footer>
</body>
</html>
