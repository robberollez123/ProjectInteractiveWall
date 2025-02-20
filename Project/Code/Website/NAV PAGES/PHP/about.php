<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Over Ons</title>
    <link rel="stylesheet" href="../CSS/nav.css">
    <link rel="stylesheet" href="../CSS/main.css">
</head>
<body>
    <header>
        <!-- Navbar -->
        <nav>
            <a href="home.php" class="nav-title">Interactive Wall</a>
            <ul>
            <li><a href="../PHP/home.php">Home</a></li>
                <li><a href="../PHP/games.php">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="../../SERIALCONNECTION/serialconnection.php">Seriële connectie</a></li>
                    <li><a href="#">Over ons</a></li>
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

    <h1>Over Ons</h1>

    <footer>&copy; Vives 2025 - Interactive Wall</footer>

    <script src="script.js"></script>
</body>
</html>
