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
            display: none; /* Initially hide the button */
        }
    </style>
</head>
<body>
    <div class="blocked-container">
        <h1>Zugriff blockiert</h1>
        <p>Sie haben die maximale Anzahl von Anmeldeversuchen überschritten. Wir haben die IP-Adresse <?php echo $_SERVER['REMOTE_ADDR']; ?> für 2 Minuten blockiert.</p>
        <p>Bitte warten Sie <span id="countdown">120</span> Sekunden, bevor Sie es erneut versuchen.</p>
        <a href="login.php" id="retry-button" class="btn btn-primary">Zurück zur Anmeldung</a>
    </div>

    <script>
        // Countdown timer
        var countdownElement = document.getElementById('countdown');
        var retryButton = document.getElementById('retry-button');
        var countdown = 120; // 2 minutes in seconds

        var interval = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                retryButton.style.display = 'inline-block'; // Show the button
            }
        }, 1000);
    </script>
</body>
</html>