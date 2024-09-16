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
$show_confirmation = false;
$error_message = "";

// Handle account deletion request
if (isset($_POST['request_delete_account'])) {
    $show_confirmation = true;
}

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
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $error_message = "Die neuen Passwörter stimmen nicht überein.";
    } else {
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
}

// Handle account deletion confirmation
if (isset($_POST['delete_account'])) {
    $confirmation_phrase = $_POST['confirmation_phrase'];
    $required_phrase = "DELETE";

    if ($confirmation_phrase !== $required_phrase) {
        $error_message = "Die Bestätigungsphrase ist falsch. Bitte geben Sie 'DELETE' ein.";
        $show_confirmation = true;
    } else {
        // Delete user data from the database
        $stmt = db()->prepare('DELETE FROM users WHERE id = :userid');
        $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
        $stmt->execute();

        // Destroy the session and redirect to the start page
        session_destroy();
        header('Location: start.php');
        exit();
    }
}

// Pass $show_confirmation value to JavaScript
echo "<script>var showConfirmation = " . json_encode($show_confirmation) . ";</script>";

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

        .overlay-form {
            background: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: none; /* Initially hidden */
            justify-content: center;
            align-items: center;
        }

        .confirmation-form {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            cursor: pointer;
            color: #000;
        }

        .dropdown-menu .dropdown-item.logout {
            color: red;
        }

        .settings-button {
            background: none;
            border: none;
            color: #f8f9fa;
            font-size: 20px;
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Willkommen, <?php echo htmlspecialchars($username); ?>!</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <button class="settings-button" id="welcomeButton">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" id="settingsDropdown" style="display:none; position: absolute; top: 60px; right: 20px;">
                        <a class="dropdown-item" href="index.php">Home</a>
                        <a class="dropdown-item" href="settings.php">Einstellungen</a>
                        <a class="dropdown-item" href="help.php">Hilfe</a>
                        <a class="dropdown-item logout" href="logout.php">Logout</a>
                    </div>
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
                <?php if (!empty($error_message)): ?>
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
                            <form method="POST" onsubmit="return validatePassword();">
                                <div class="form-group">
                                    <label for="current_password">Aktuelles Passwort</label>
                                    <input type="password" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Neues Passwort</label>
                                    <input type="password" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Neues Passwort bestätigen</label>
                                    <input type="password" id="confirm_password" name="confirm_password" required>
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
                                <button type="submit" name="request_delete_account" class="btn btn-danger">Konto löschen</button>
                            </form>
                        </div>
                    </div>
                </div>

                <?php if ($show_confirmation): ?>
                    <div class="overlay-form" id="confirmation-overlay">
                        <div class="confirmation-form">
                            <span class="close-btn" id="close-btn">&times;</span>
                            <h4>Bestätigung erforderlich</h4>
                            <form method="POST">
                                <div class="form-group">
                                    <label for="confirmation_phrase">Bestätigungsphrase eingeben</label>
                                    <input type="text" id="confirmation_phrase" name="confirmation_phrase" required>
                                    <small class="form-text text-muted">Bitte geben Sie 'DELETE' ein, um Ihr Konto zu löschen.</small>
                                </div>
                                <button type="submit" name="delete_account" class="btn btn-danger">Bestätigen</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('welcomeButton').addEventListener('click', function () {
        var dropdown = document.getElementById('settingsDropdown');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (showConfirmation) {
            document.getElementById('confirmation-overlay').style.display = 'flex';
        }

        document.getElementById('close-btn').addEventListener('click', function () {
            document.getElementById('confirmation-overlay').style.display = 'none';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>