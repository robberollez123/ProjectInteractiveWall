<?php
session_start();

// Controleer of het formulier is verzonden
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["user"]); // Whitespace verwijderen
    $password = $_POST["password"];

    // Verbinding maken met de database
    require_once __DIR__ . '/../config/database.php';

    // Controleer of de verbinding gelukt is
    if (!$linkDB) {
        die("Databaseverbinding mislukt: " . mysqli_connect_error());
    }

    // Gebruik een prepared statement om SQL-injectie te voorkomen
    $query = "SELECT gebruikersnaam, wachtwoord, isAdmin FROM gebruiker WHERE gebruikersnaam = ?";
    $stmt = mysqli_prepare($linkDB, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Controleer of de gebruiker bestaat
    if ($row = mysqli_fetch_assoc($result)) {
        $hashedPassword = $row["wachtwoord"];

        // Controleer of het ingevoerde wachtwoord correct is
        if (password_verify($password, $hashedPassword)) {
            // Sessiegegevens opslaan (maar NIET het wachtwoord!)
            $_SESSION["user"] = $username;
            $_SESSION["isAdmin"] = isset($row["isAdmin"]) ? (int)$row["isAdmin"] : 0; // Zorg ervoor dat het een integer is

            // Stuur de gebruiker door naar de homepagina
            header("Location: ../home.php");
            exit;
        } else {
            $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
        }
    } else {
        $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
    }

    // Sluit de statement en databaseverbinding
    mysqli_stmt_close($stmt);
    mysqli_close($linkDB);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="login-style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <label for="user">Gebruikersnaam:</label>
                <input type="text" name="user" id="user" required>
            </div>
            <div class="input-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="button-group">
                <input type="submit" value="Login" class="btn-submit">
                <a href="../home.php" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</body>
</html>