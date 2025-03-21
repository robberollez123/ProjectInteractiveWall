<?php
session_start();
$ingelogd = isset($_SESSION["user"]);
$adminUser = isset($_SESSION["isAdmin"]) && $_SESSION["isAdmin"] == 1; // Haal adminstatus correct op uit de sessie
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="NAV PAGES/CSS/nav.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/main.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/feedback.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/headerStyle.css">
    <link rel="stylesheet" href="NAV PAGES/CSS/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <a href="home.php" class="nav-title">Interactive Wall</a>
            <button class="menu-toggle">&#9776;</button>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="games.php">Spellen</a></li>
                <?php if ($ingelogd): ?>
                    <li><a href="SERIALCONNECTION/serialconnection.php">Seriele connectie</a></li>
                    <li><a href="#">Feedback</a></li>
                <?php endif; ?>
                <?php if ($adminUser): ?>
                    <li><a href="LOGIN/add-account.php">Account toevoegen</a></li>
                <?php endif; ?>
            </ul>
            <div class="user-menu">
                <?php if ($ingelogd): ?>
                    <div class="dropdown">
                        <span class="user-badge" onclick="toggleDropdown()">
                            <i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($_SESSION["user"]); ?>
                        </span>
                        <div id="dropdown-menu" class="dropdown-content">
                            <!-- TODO: setting pagina -->
                            <a href="settings.php">Instellingen</a>
                            <a href="LOGIN/logout.php">Uitloggen</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="LOGIN/login.php" class="btn btn-login">Inloggen</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <h1>Geef je feedback</h1>
        <p>Wij waarderen je mening! Laat ons weten indien er ergens een fout in de website zit!</p>

        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<div class="alert alert-success">Je feedback is succesvol verzonden. Bedankt voor je bijdrage!</div>';
            } elseif ($_GET['status'] == 'failed') {
                echo '<div class="alert alert-failed">Er is iets misgegaan met het verzenden van je feedback. Probeer het later opnieuw.</div>';
            }
        }
        ?>

        <form action="send_feedback.php" method="POST" class="feedback-form">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="message">Feedback:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <button type="submit" class="submit-btn">Verzend Feedback</button>
        </form>
    </main>


    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalMessage"></div>
        </div>
    </div>

    <footer>
        <div class="footer-links">
            <p class="copyText">&copy; Vives 2025 - Interactive Wall</p>
            <div class="links">
                <p>Links:</p>
                <ul>
                    <li><a href="#">Contact</a></li>
                    <li><a href="#">Reacties</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="feedbackscript.js" defer></script>
    <script src="NAV PAGES/SCRIPTS/nav.js" defer></script>
</body>
</html>