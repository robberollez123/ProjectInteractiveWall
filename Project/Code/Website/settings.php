<?php
session_start();

$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['change_username'])) {
        $newUsername = $_POST['username'];

        // Verbind met de database
        require_once __DIR__ . '/config/database.php'; // Zorg ervoor dat je database verbinding correct is

        // Controleer of de nieuwe gebruikersnaam al bestaat
        $query = "SELECT * FROM gebruiker WHERE gebruikersnaam = ?";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "s", $newUsername);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $error_message = "De gebruikersnaam is al in gebruik. Kies een andere.";
        } else {
            // Update de gebruikersnaam in de database
            $username = $_SESSION["user"]; // De huidige gebruiker die is ingelogd
            $updateQuery = "UPDATE gebruiker SET gebruikersnaam = ? WHERE gebruikersnaam = ?";
            $updateStmt = mysqli_prepare($linkDB, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, "ss", $newUsername, $username);

            if (mysqli_stmt_execute($updateStmt)) {
                $_SESSION["user"] = $newUsername; // Werk de gebruikersnaam in de sessie bij
                $success_message = "Gebruikersnaam succesvol gewijzigd!";
            } else {
                $error_message = "Er is iets mis gegaan bij het bijwerken van je gebruikersnaam.";
            }

            // Sluit de update statement
            mysqli_stmt_close($updateStmt);
        }

        // Sluit de check statement
        mysqli_stmt_close($stmt);
        mysqli_close($linkDB);
    } elseif (isset($_POST['change_password'])) {
        $newPassword = $_POST['password'];

        // Verbind met de database
        require_once __DIR__ . '/config/database.php'; // Zorg ervoor dat je database verbinding correct is

        // Hash het nieuwe wachtwoord
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Update het wachtwoord in de database
        $username = $_SESSION["user"]; // De huidige gebruiker die is ingelogd

        // Gebruik een prepared statement om SQL-injecties te voorkomen
        $query = "UPDATE gebruiker SET wachtwoord = ? WHERE gebruikersnaam = ?";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $username);

        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Wachtwoord succesvol bijgewerkt!";
        } else {
            $error_message = "Er is iets mis gegaan bij het bijwerken van je wachtwoord.";
        }

        // Sluit de databaseverbinding
        mysqli_stmt_close($stmt);
        mysqli_close($linkDB);
    }
}

// De standaard themazetting is 'light'
$currentTheme = 'light';
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instellingen</title>
    <link rel="stylesheet" href="NAV PAGES/CSS/settings.css">
</head>
<body class="<?php echo $currentTheme; ?>">

<header>
    <h1>Instellingen</h1>
</header>

<main>
    <section>
        <h2>Wijzig Gebruikersnaam</h2>
        <form method="POST" action="settings.php">
            <label for="username">Nieuwe Gebruikersnaam:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION["user"]); ?>" required>
            <button type="submit" name="change_username">Wijzig Gebruikersnaam</button>
        </form>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif (isset($success_message)): ?>
            <p class="success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </section>

    <section>
        <h2>Wijzig Wachtwoord</h2>
        <form method="POST" action="settings.php">
            <label for="password">Nieuw Wachtwoord:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit" name="change_password">Wijzig Wachtwoord</button>
        </form>
        <?php if (isset($success_message)): ?>
            <p class="success"><?php echo htmlspecialchars($success_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php elseif (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </section>

    <!-- Knop om terug te keren -->
    <section>
        <a href="home.php" class="btn-back">Terug naar Home</a>
    </section>
</main>

<footer>
    &copy; Vives 2025 - Interactive Wall
</footer>

</body>
</html>
