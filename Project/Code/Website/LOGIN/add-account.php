<?php
session_start();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $isAdmin = isset($_POST["isAdmin"]) ? 1 : 0;

    if (empty($username) || empty($password) || empty($confirmPassword)) {
        $error_message = "Vul alle velden in!";
    } elseif ($password !== $confirmPassword) {
        $error_message = "De wachtwoorden komen niet overeen!";
    } else {
        // Controleer of de gebruikersnaam al bestaat
        $query = "SELECT id FROM gebruiker WHERE gebruikersnaam = ?";
        $stmt = mysqli_prepare($linkDB, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error_message = "Deze gebruikersnaam is al in gebruik!";
        } else {
            // Hash het wachtwoord en voeg de gebruiker toe
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO gebruiker (gebruikersnaam, wachtwoord, isAdmin) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($linkDB, $query);
            mysqli_stmt_bind_param($stmt, "ssi", $username, $hashedPassword, $isAdmin);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION["user"] = $username;
                header("Location: ../home.php");
                exit;
            } else {
                $error_message = "Account toevoegen mislukt. Probeer opnieuw.";
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($linkDB);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account toevoegen</title>
    <link rel="stylesheet" href="add-account-style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="register-container">
        <h2>Account toevoegen</h2>
        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="input-group">
                <label for="username">Gebruikersnaam:</label>
                <input type="text" name="username" id="username" required onkeyup="checkUsername()">
                <span id="username-status"></span>
            </div>
            <div class="input-group">
                <label for="password">Wachtwoord:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="input-group">
                <label for="confirmPassword">Bevestig Wachtwoord:</label>
                <input type="password" name="confirmPassword" id="confirmPassword" required>
            </div>
            <div class="input-group">
                <div class="checkbox-container">
                    <label for="isAdmin">Admin</label>
                    <input type="checkbox" name="isAdmin" id="isAdmin" value="1">
                </div>
            </div>
            <div class="button-group">
                <input type="submit" value="Registreren" class="btn-submit">
                <a href="../home.php" class="btn-cancel">Annuleren</a>
            </div>
        </form>
    </div>

    <script>
        function checkUsername() {
            let username = document.getElementById("username").value;
            if (username.length > 2) {
                $.post("check-username.php", { username: username }, function (data) {
                    $("#username-status").html(data);
                });
            } else {
                $("#username-status").html("");
            }
        }
    </script>
</body>
</html>