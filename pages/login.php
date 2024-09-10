<?php
require_once "../composables/db.php";
session_start();
$message = '';
$attempts_limit = 8; // Maximum attempts in the given time window
$time_window = 300; // 5 minutes in seconds
$block_time = 120; // 2 minutes in seconds

function getLoginAttempts($key) {
    if (file_exists($key)) {
        $data = json_decode(file_get_contents($key), true);
        if (!is_array($data) || !isset($data['attempts']) || !isset($data['blocked_until'])) {
            $data = ['attempts' => [], 'blocked_until' => time() - 1];
        }
    } else {
        $data = ['attempts' => [], 'blocked_until' => time() - 1];
    }
    return $data;
}

function saveLoginAttempts($key, $data) {
    createDirectory(dirname($key));
    file_put_contents($key, json_encode($data), LOCK_EX);
}

function resetLoginAttempts($key) {
    if (file_exists($key)) {
        unlink($key);
    }
}

function createDirectory($path) {
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
}

function isBlocked($key, $block_time, $attempts_limit, $time_window) {
    $data = getLoginAttempts($key);
    $time = time();

    // Clean up old attempts
    $data['attempts'] = array_filter($data['attempts'], function($timestamp) use ($time, $time_window) {
        return ($time - $timestamp) <= $time_window;
    });

    // Check if current time is within the block period
    if ($time < $data['blocked_until']) {
        return true;
    } else {
        // Checking if the login attempts exceed the threshold
        if (count($data['attempts']) >= $attempts_limit) {
            $data['blocked_until'] = $time + $block_time;
            saveLoginAttempts($key, $data);
            return true;
        }
    }
    return false;
}

function logAttempt($key) {
    $data = getLoginAttempts($key);
    $data['attempts'][] = time();
    saveLoginAttempts($key, $data);
}

// Define the login attempts key based on remote address
$attempts_file = sys_get_temp_dir() . "/login_attempts_" . preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_SERVER['REMOTE_ADDR']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (isBlocked($attempts_file, $block_time, $attempts_limit, $time_window)) {
        header("HTTP/1.1 429 Too Many Requests");
        echo "You've exceeded the number of login attempts. We've blocked IP address {$_SERVER['REMOTE_ADDR']} for 2 minutes.";
        exit();
    }

    // Prepare and execute the statement to fetch user data
    $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['userid'] = $user['id'];
        resetLoginAttempts($attempts_file);
        header('Location: index.php');
        exit();
    } else {
        logAttempt($attempts_file);
        $message = 'Ungültiger Benutzername oder Passwort';
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
            <p class="text-danger text-center mt-3"><?php echo $message; ?></p>
            <p class="text-center mt-3">
                Noch kein Konto? <a href="register.php">Account erstellen</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>