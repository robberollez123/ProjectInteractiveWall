<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spellen</title>
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="gamesTable.css">
    <link rel="stylesheet" href="headerStyle.css">
</head>
<body>
    <header>
        <!-- Navbar -->
        <nav>
            <a href="home.php" class="nav-title">Interactive Wall</a>
            <ul>
                <li><a href="home.php">Home</a></li>
                <li><a href="#">Spellen</a></li>
                <li><a href="serialconnection.php">Seriële connectie</a></li>
                <li><a href="about.php">Over ons</a></li>
            </ul>
        </nav>
    </header>

    <h1>Info</h1>
    <p>Hier kan je een spel uitkiezen. (Dit zijn voorbeeldspellen)</p>

    <h1>Voeg een nieuw spel toe</h2>

    <div id="add-game-container">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="post">
            <input type="text" name="naam" placeholder="Vul de naam van het spel in..." required>
            <input type="url" name="link" placeholder="Vul de link naar het spel in..." required>
            <input class="game-button" type="submit" name="submit" value="Toevoegen">
        </form>
    </div>

    <?php 
        // Leg verbinding met de database
        $link = mysqli_connect("localhost", "root", "", "interactivewall") or die("Error: " . mysqli_connect_error());

        // Controleer of het formulier is verzonden
        if (isset($_POST["submit"])) {
            // Zorg ervoor dat de velden zijn ingevuld
            if (!empty($_POST["naam"]) && !empty($_POST["link"])) {
                // Voorkom SQL-injectie
                $naam = mysqli_real_escape_string($link, $_POST["naam"]);
                $spelLink = mysqli_real_escape_string($link, $_POST["link"]);

                // Voer de query uit
                $query = "INSERT INTO spellen (naam, link) VALUES ('$naam', '$spelLink')";
                if (mysqli_query($link, $query)) {
                    echo "<p>Spel succesvol toegevoegd!</p>";
                } else {
                    echo "<p>Fout bij toevoegen: " . mysqli_error($link) . "</p>";
                }
            }
        }
    ?>

    <h1>Alle spellen</h1>
    <!-- Spellen Tabel -->
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
                        <td><a class='game-button' href=\"$record[link]\">Speel</a></td>
                     </tr>";
            }
            
        ?>
    </table>
</body>
</html>
