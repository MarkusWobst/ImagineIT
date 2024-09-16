<?php
// Set the countdown timer in seconds
$countdown = 120;

// Create a session to store the countdown timer
session_start();
$_SESSION['countdown'] = $countdown;

// Check if the countdown timer has expired
if ($_SESSION['countdown'] <= 0) {
    // Show the retry button
    $showButton = true;
} else {
    // Refresh the page every second to update the timer
    header("Refresh: 1");
    $showButton = false;
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zugriff blockiert</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .blocked-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            text-align: center;
        }
        .blocked-container h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .blocked-container p {
            font-size: 1.2rem;
        }
        .blocked-container .btn {
            margin-top: 20px;
            <?php if (!$showButton) { ?>display: none;<?php } ?>
        }
    </style>
</head>
<body>
    <div class="blocked-container">
        <h1>Zugriff blockiert</h1>
        <p>Sie haben die maximale Anzahl von Anmeldeversuchen überschritten. Wir haben die IP-Adresse <?php echo $_SERVER['REMOTE_ADDR']; ?> für 2 Minuten blockiert.</p>
        <p>Bitte warten Sie <span id="countdown"><?php echo $_SESSION['countdown']; ?></span> Sekunden, bevor Sie es erneut versuchen.</p>
        <a href="login.php" id="retry-button" class="btn btn-primary"<?php if ($showButton) { ?> style="display: inline-block;"<?php } ?>>Zurück zur Anmeldung</a>
    </div>

    <?php
    // Decrement the countdown timer
    $_SESSION['countdown']--;
    ?>
</body>
</html>