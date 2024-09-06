<?php
session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauptseite</title>
</head>
<body>
    <h2>Willkommen auf der Hauptseite, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    <p>Dies ist eine geschützte Seite, nur für eingeloggte Benutzer.</p>
    <a href="logout.php">Logout</a>
</body>
</html>