<?php

require_once "../composables/db.php";

session_start();

// Überprüfen Sie, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    header('Location: start.php');
    exit();
}

$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

// Handle profile update
if (isset($_POST['update_profile'])) {
    $new_username = $_POST['username'];

    $stmt = db()->prepare('UPDATE users SET username = :username WHERE id = :userid');
    $stmt->bindValue(':username', $new_username);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    $_SESSION['username'] = $new_username;
    header('Location: settings.php');
    exit();
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];

    // Verify current password
    $stmt = db()->prepare('SELECT password FROM users WHERE id = :userid');
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (password_verify($current_password, $user['password'])) {
        // Update password
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = db()->prepare('UPDATE users SET password = :password WHERE id = :userid');
        $stmt->bindValue(':password', $new_password_hashed);
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();

        header('Location: settings.php');
        exit();
    } else {
        $error_message = "Aktuelles Passwort ist falsch.";
    }
}

// Handle account deletion
if (isset($_POST['delete_account'])) {
    // Delete user data from the database
    $stmt = db()->prepare('DELETE FROM users WHERE id = :userid');
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Destroy the session and redirect to the start page
    session_destroy();
    header('Location: start.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einstellungen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #343a40;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
        }

        .navbar {
            background: #343a40;
        }

        .navbar-brand, .nav-link, .btn-outline-danger {
            color: #f8f9fa !important;
        }

        .container {
            margin-top: 20px;
        }

        .main-content {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .main-content h3 {
            position: relative;
            z-index: 1;
            font-size: 24px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: bold;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-primary, .btn-warning, .btn-danger {
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .btn-primary {
            background: #007bff;
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .btn-warning {
            background: #ffc107;
            color: #343a40;
            border: none;
        }

        .btn-warning:hover {
            background: #e0a800;
        }

        .btn-danger {
            background: #dc3545;
            color: #ffffff;
            border: none;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .text-center {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .icon {
            font-size: 40px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .form-section {
            margin-bottom: 30px;
        }

        .form-section hr {
            margin: 20px 0;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 16px;
        }

        .card-body {
            padding: 15px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Willkommen, <?php echo htmlspecialchars($username); ?>!</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="main-content text-center">
                <h3 class="text-center mt-4"><i class="fas fa-user-cog icon"></i>Einstellungen</h3>
                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                <div class="form-section">
                    <div class="card">
                        <div class="card-header">
                            Profil aktualisieren
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="username">Benutzername</label>
                                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($username); ?>" required>
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-primary">Profil aktualisieren</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="card">
                        <div class="card-header">
                            Passwort ändern
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="form-group">
                                    <label for="current_password">Aktuelles Passwort</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Neues Passwort</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-warning">Passwort ändern</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <div class="card">
                        <div class="card-header">
                            Konto löschen
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <button type="submit" name="delete_account" class="btn btn-danger">Konto löschen</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>

</html>