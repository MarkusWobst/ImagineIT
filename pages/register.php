<?php

require_once "../composables/db.php";

$message = '';
$message_class = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Regex patterns
    $username_pattern = '/^[a-zA-Z0-9_]{3,16}$/'; // Alphanumeric and underscores, 3-16 characters
    $password_pattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/'; // Minimum 8 characters, at least one letter and one number

    // Validate username
    if (!preg_match($username_pattern, $username)) {
        $message = 'Der Benutzername muss 3-16 Zeichen lang sein und darf nur Buchstaben, Zahlen und Unterstriche enthalten.';
        $message_class = 'text-danger';
    }
    // Validate password
    elseif (!preg_match($password_pattern, $password)) {
        $message = 'Das Passwort muss mindestens 8 Zeichen lang sein und mindestens einen Buchstaben und eine Zahl enthalten.';
        $message_class = 'text-danger';
    }
    // Check if passwords match
    elseif ($password !== $confirm_password) {
        $message = 'Die Passwörter stimmen nicht überein.';
        $message_class = 'text-danger';
    } else {
        // Check if username already exists
        $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $message = 'Der Benutzername ist bereits vergeben.';
            $message_class = 'text-danger';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Add the new user to the database
            $stmt = db()->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            $stmt->bindValue(':username', $username);
            $stmt->bindValue(':password', $hashed_password);
            $stmt->execute();

            // User successfully registered, set session variables and redirect to the homepage
            $_SESSION['username'] = $username;
            $_SESSION['userid'] = db()->lastInsertId(); // Assuming 'id' is the primary key in the 'users' table
            header('Location: /index');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren</title>
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
            <h2 class="text-center">Account erstellen</h2>
            <form action="/register" method="POST" class="form-signin">
                <div class="form-group mb-3">
                    <label for="username" class="form-label">Benutzername</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="password" class="form-label">Passwort</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group mb-3">
                    <label for="confirm_password" class="form-label">Passwort bestätigen</label>
                    <input type="password" name="confirm_password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Account erstellen</button>
            </form>
            <p class="<?php echo $message_class; ?> text-center mt-3"><?php echo $message; ?></p>
            <p class="text-center mt-3">
                Bereits ein Konto? <a href="/login">Hier einloggen</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>