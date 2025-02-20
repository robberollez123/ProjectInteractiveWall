<?php
session_start();

// Wanneer het formulier wordt verzonden, verwerken we de gegevens
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];

    // Controleren of wachtwoorden overeenkomen
    if ($password == $confirmPassword) {
        // Hash het wachtwoord
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Maak verbinding met de database
        $link = mysqli_connect("localhost", "root", "", "interactivewall");

        // Controleer of de verbinding gelukt is
        if (!$link) {
            die("Databaseverbinding mislukt: " . mysqli_connect_error());
        }

        // Voeg de gebruiker toe aan de database met gehashte wachtwoord
        $query = "INSERT INTO gebruiker (gebruikersnaam, wachtwoord) VALUES ('$username', '$hashedPassword')";
        mysqli_query($link, $query);

        // Sluit de databaseverbinding
        mysqli_close($link);

        // Zet de sessie in voor de gebruiker
        $_SESSION["user"] = $username;

        // Gebruiker wordt doorgestuurd naar de homepagina na succesvolle registratie
        header("Location: ../NAV PAGES/PHP/home.php");
        exit;
    } else {
        $error_message = "De wachtwoorden komen niet overeen!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registreren</title>
    <link rel="stylesheet" href="register-style.css">
</head>
<body>
    <div class="register-container">
        <h2>Registreren</h2>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <div class="input-group">
                <label for="username">Gebruikersnaam:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="input-group">
                <label for="confirmPassword">Bevestig Wachtwoord:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required>
            </div>
            <div class="button-group">
                <input type="submit" value="Registreren" class="btn-submit">
                <a href="../NAV PAGES/PHP/home.php" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>
</body>
</html>
