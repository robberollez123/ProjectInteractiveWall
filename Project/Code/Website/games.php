<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spellen</title>
    <link rel="stylesheet" href="nav.css">
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

    <h1>Spellen</h1>
    <p>Hier kan je een spel uitkiezen. (Dit zijn voorbeeldspellen)</p>
    
    <!-- Spellen Tabel -->
    <table class="games-table">
        <thead>
            <tr>
                <th>Spelnaam</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Puzzle Challenge</td>
                <td><a href="https://www.jigsawplanet.com/" class="game-button">Speel</a></td>
            </tr>
            <tr>
                <td>Word Search</td>
                <td><a href="https://api.razzlepuzzles.com/wordsearch?locale=nl" class="game-button">Speel</a></td>
            </tr>
            <tr>
                <td>Memory Match</td>
                <td><a href="https://www.memorymatching.com/" class="game-button">Speel</a></td>
            </tr>
            <tr>
                <td>Tic-Tac-Toe</td>
                <td><a href="https://playtictactoe.org/" class="game-button">Speel</a></td>
            </tr>
            <tr>
                <td>Snake Game</td>
                <td><a href="https://snake-game.io/" class="game-button">Speel</a></td>
            </tr>
            <tr>
                <td>Sudoku</td>
                <td><a href="https://sudoku.com/" class="game-button">Speel</a></td>
            </tr>
        </tbody>
    </table>

    <script src="script.js"></script>
</body>
</html>
