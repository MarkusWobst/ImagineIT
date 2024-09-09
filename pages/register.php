<?php

require_once "../composables/db.php";

session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Prüfe, ob das Passwort und die Passwortbestätigung übereinstimmen
    if ($password !== $confirm_password) {
        $message = 'Die Passwörter stimmen nicht überein.';
    } else {

        // Überprüfe, ob der Benutzername bereits existiert
        $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $message = 'Der Benutzername ist bereits vergeben.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Füge den neuen Benutzer zur Datenbank hinzu
            // $stmt = $db->prepare('INSERT INTO users (username, password) VALUES (:username, :password)');
            // $stmt->bindValue(':username', $username);
            // $stmt->bindValue(':password', $hashed_password);
            // $stmt->execute();

            $user = db()->exec("INSERT INTO users ('username', 'password') VALUES ('{$username}', '{$hashed_password}')");

            // Benutzer erfolgreich registriert, leite zur Login-Seite weiter
            $message = 'Account erfolgreich erstellt! Du kannst dich jetzt einloggen.';
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
            <form action="register.php" method="POST" class="form-signin">
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
            <p class="text-danger text-center mt-3"><?php echo $message; ?></p>
            <p class="text-center mt-3">
                Bereits ein Konto? <a href="login.php">Hier einloggen</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>