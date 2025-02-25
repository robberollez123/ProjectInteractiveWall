<?php
session_start();

// Wanneer het formulier wordt verzonden, verwerken we de gegevens
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["user"];
    $password = $_POST["password"];

    // Verbinding maken met de database
    $link = mysqli_connect("localhost", "root", "", "interactivewall");
    
    // Controleren of de verbinding gelukt is
    if (!$link) {
        die("Databaseverbinding mislukt: " . mysqli_connect_error());
    }

    // Zoek naar de gebruiker in de database
    $query = "SELECT * FROM gebruiker WHERE gebruikersnaam = '$username'";
    $result = mysqli_query($link, $query);
    
    // Als er een gebruiker wordt gevonden, controleer het wachtwoord met password_verify
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row["wachtwoord"];

        // Controleer of het ingevoerde wachtwoord overeenkomt met het gehashte wachtwoord uit de database
        if (password_verify($password, $hashedPassword)) {
            // Stel de sessiegegevens in en log de gebruiker in
            $_SESSION["user"] = $username;
            $_SESSION["password"] = $password;  // Optioneel, je zou ook enkel een sessie-id kunnen gebruiken

            // Stuur de gebruiker door naar de homepagina
            header("Location: ../home.php");
            exit;
        } else {
            // Onjuist wachtwoord, toon een foutmelding
            $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
        }
    } else {
        // Gebruiker niet gevonden
        $error_message = "Onjuiste gebruikersnaam of wachtwoord!";
    }

    // Sluit de databaseverbinding
    mysqli_close($link);
}

?>

<!DOCTYPE html>
<html lang="en">
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
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>
        
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
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
