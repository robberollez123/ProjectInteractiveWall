<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ingelogd = isset($_SESSION["user"]);

    if($ingelogd){
        $name = $_SESSION["user"];
    }
    else {
        $name = "Onbekend";
    }
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars($_POST["message"], ENT_QUOTES, 'UTF-8');

    // Controleer of het e-mailadres geldig is
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Ongeldig e-mailadres. Probeer opnieuw.");
    }

    $to = "interactivewall@robberollez.be";  // Jouw e-mailadres
    $subject = "Nieuwe feedback van $name";
    $body = "Naam: $name\nE-mail: $email\n\nBericht:\n$message";

    // Verbeterde headers
    $headers = "From: no-reply@robberollez.be\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Verstuur de e-mail
    if (mail($to, $subject, $body, $headers)) {
        header("Location: feedback.php?status=success");
        exit;
    } else {
        header("Location: feedback.php?status=failed"); 
    }
} else {
    header("Location: feedback.php");
    exit;
}
?>
