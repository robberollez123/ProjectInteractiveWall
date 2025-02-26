<?php
require_once "/../config/database.php";

if (isset($_POST["username"])) {
    $username = trim($_POST["username"]);

    $query = "SELECT id FROM gebruiker WHERE gebruikersnaam = ?";
    $stmt = mysqli_prepare($linkDB, $query);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        echo "<span style='color: red;'>❌ Gebruikersnaam is bezet</span>";
    } else {
        echo "<span style='color: green;'>✅ Beschikbaar</span>";
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($linkDB);
?>
