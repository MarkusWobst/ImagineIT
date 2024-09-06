<?php
session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = new SQLite3('../db/identifier.sqlite');
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Bereite die SQL-Abfrage vor
    $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
    $stmt->bindValue(':username', $username, SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        header('Location: main.php');
        exit();
    } else {
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
</head>
<body>
    <h2>Login</h2>
    <form action="login.php" method="POST">
        <label for="username">Benutzername:</label>
        <input type="text" name="username" required>
        <br>
        <label for="password">Passwort:</label>
        <input type="password" name="password" required>
        <br>
        <button type="submit">Einloggen</button>
    </form>
    <p><?php echo $message; ?></p>
</body>
</html>