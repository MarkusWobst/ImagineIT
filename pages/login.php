<?php
require_once "../composables/db.php";
require_once "../composables/login_attempts.php";
require_once "../composables/aes.php";

session_abort();
session_start();

$message = '';
$attempts_limit = 8; // Maximum attempts in the given time window
$time_window = 300; // 5 minutes in seconds
$block_time = 120; // 2 minutes in seconds

// Define the login attempts key based on remote address
$attempts_file = sys_get_temp_dir() . "/login_attempts_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_SERVER['REMOTE_ADDR']);


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Regex patterns
    $username_pattern = '/^[\w!@#$%^&*()\-+=]{3,16}$/'; // Alphanumeric, underscores, and special characters, 3-16 characters
    $password_pattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()\-+=]{8,}$/'; // Minimum 8 characters, at least one letter, one number, and special characters

    // Validate username
    if (!preg_match($username_pattern, $username)) {
        $message = 'Der Benutzername muss 3-16 Zeichen lang sein und darf nur Buchstaben, Zahlen, Unterstriche und Sonderzeichen enthalten.';
    }
    // Validate password
    elseif (!preg_match($password_pattern, $password)) {
        $message = 'Das Passwort muss mindestens 8 Zeichen lang sein und mindestens einen Buchstaben, eine Zahl und ein Sonderzeichen enthalten.';
    } else {
        $blocked = isBlocked($attempts_file, $block_time, $attempts_limit, $time_window);

        // Prepare and execute the statement to fetch user data
        $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($blocked && $user && password_verify($password, $user['password'])) {
            // If blocked but login is successful, reset the login attempts
            resetLoginAttempts($attempts_file);
        }

        if ($user && password_verify($user['salt'] . $password, $user['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = $user['id'];
            resetLoginAttempts($attempts_file);

            // Encrypt the password with SHA256 and save it as a session variable
            $_SESSION['keySHA256'] = openssl_decrypt(
                $user['aeskey'],
                'AES-256-CBC',
                hash('SHA256', $user['pepper'] . $password),
                0,
                base64_decode($user['iv'])
            );

            header('Location: index');
            exit();
        } else {
            if ($blocked) {
                header("Location: blocked");
                exit();
            }
            logAttempt($attempts_file);
            $message = 'Ungültiger Benutzername oder Passwort';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center">Login</h2>
                <form action="login.php" method="POST" class="form-signin">
                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Benutzername</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Passwort</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Einloggen</button>
                </form>
                <p class="text-danger text-center mt-3">
                    <?php echo $message; ?>
                </p>
                <p class="text-center mt-3">
                    Noch kein Konto? <a href="register.php">Account erstellen</a>
                </p>
            </div>
        </div>
    </div>
    <script>
        async function main() {
            const publicKeyCredentialRequestOptions = {
                challenge: Uint8Array.from(
                    "<?= base64_encode(random_bytes(32)) ?>", c => c.charCodeAt(0)),,
                allowCredentials: [{
                    id: Uint8Array.from(
                        "UZSL85T9AFC", c => c.charCodeAt(0)),
                    type: 'public-key',
                    transports: ['usb', 'ble', 'nfc'],
                }],
                timeout: 60000,
            }

            const assertion = await navigator.credentials.get({
                publicKey: publicKeyCredentialRequestOptions
            });
        }

        <?php
        if (true) {
            ?>
            main()
            <?php
        }
        ?>
    </script>
</body>

</html>