<?php
session_start();

// Sessie beëindigen
session_destroy();

// Zorg ervoor dat de gebruiker wordt omgeleid naar de homepagina
header("Location: ../NAV PAGES/PHP/home.php");
exit; // Stop verder uitvoeren van de script na de redirect
?>
