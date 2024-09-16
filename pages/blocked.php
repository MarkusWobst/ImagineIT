<?php

// Set the block duration in seconds (2 minutes)
$block_duration = 120;

// Check if the block start time is set in the session
if (!isset($_SESSION['block_start_time'])) {
    $_SESSION['block_start_time'] = time();
}

// Calculate the remaining block time
$elapsed_time = time() - $_SESSION['block_start_time'];
$remaining_time = $block_duration - $elapsed_time;

// If the block time has expired, reset the block start time and allow retry
if ($remaining_time <= 0) {
    unset($_SESSION['block_start_time']);
    $remaining_time = 0;
    $show_retry_button = true;
} else {
    $show_retry_button = false;
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
            display: <?php echo $show_retry_button ? 'inline-block' : 'none'; ?>;
        }
    </style>
</head>
<body>
    <div class="blocked-container">
        <h1>Zugriff blockiert</h1>
        <p>Sie haben die maximale Anzahl von Anmeldeversuchen überschritten. Wir haben die IP-Adresse <?php echo $_SERVER['REMOTE_ADDR']; ?> für 2 Minuten blockiert.</p>
        <p>Bitte warten Sie <span id="countdown"><?php echo $remaining_time; ?></span> Sekunden, bevor Sie es erneut versuchen.</p>
        <a href="/login" id="retry-button" class="btn btn-primary">Zurück zur Anmeldung</a>
    </div>

    <?php
    // Refresh the page every second to update the countdown
    if (!$show_retry_button) {
        echo '<meta http-equiv="refresh" content="1">';
    }
    ?>
</body>
</html>