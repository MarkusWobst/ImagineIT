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

// Prüfe, ob der Button "Neuer Chat" gedrückt wurde
if (isset($_POST['new_chat'])) {
    // Füge den neuen Chat zur Datenbank hinzu
    $stmt = db()->prepare('INSERT INTO chat_records (user_id, title) VALUES (:userid, "new chat")');
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Hole die ID des neu erstellten Chats
    $chat_id = db()->lastInsertId();

    // Nach dem Erstellen des Chats zur neuen Chat-Seite umleiten
    header('Location: chat.php?chat_id=' . $chat_id . '&user_id=' . $userid);
    exit();
}

// Prüfe, ob der Button "Löschen" gedrückt wurde und die chat_id gesetzt ist
if (isset($_POST['delete_chat']) && isset($_POST['chat_id'])) {
    $chat_id = $_POST['chat_id'];

    // Überprüfen, ob der Chat dem Benutzer gehört und existiert
    try {
        // Check if the chat exists and belongs to the user
        $chat_stmt = db()->prepare('SELECT id FROM chat_records WHERE user_id = :userid AND id = :chatid');
        $chat_stmt->bindValue(':userid', $userid);
        $chat_stmt->bindValue(':chatid', $chat_id);
        $chat_stmt->execute();
        $chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($chats) == 0) {
            throw new Exception("You do not have permission to delete this chat.");
        }

        // Delete associated messages
        $message_stmt = db()->prepare('DELETE FROM messages WHERE chat_id = :chatid');
        $message_stmt->bindValue(':chatid', $chat_id);
        $message_stmt->execute();

        // Delete the chat itself
        $chat_delete_stmt = db()->prepare('DELETE FROM chat_records WHERE id = :chatid');
        $chat_delete_stmt->bindValue(':chatid', $chat_id);
        $chat_delete_stmt->execute();
    } catch (Exception $e) {
        // Log errors and redirect to an error page
        error_log("Error: " . $e->getMessage());
        header('Location: error.php');
        exit();
    }

    // Nach dem Löschen des Chats Seite neu laden und zu index.php umleiten
    header('Location: index.php');
    exit();
}

// Hole alle Chats, die zur aktuellen userid gehören
$search_query = '';
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    $chat_stmt = db()->prepare('SELECT * FROM chat_records WHERE user_id = :userid AND title LIKE :search');
    $chat_stmt->bindValue(':userid', $userid);
    $chat_stmt->bindValue(':search', '%' . $search_query . '%');
} else {
    $chat_stmt = db()->prepare('SELECT * FROM chat_records WHERE user_id = :userid');
    $chat_stmt->bindValue(':userid', $userid);
}
$chat_stmt->execute();
$chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauptseite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #343a40;
            font-family: 'Arial', sans-serif;
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
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .main-content h3 {
            position: relative;
            z-index: 1;
        }

        .chats-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .chat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .chat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .chat-card h5 {
            color: #343a40;
        }

        .chat-card .btn-group {
            display: flex;
            gap: 10px;
        }

        .chat-card button {
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background 0.3s;
        }

        .chat-card button:hover {
            background: #0056b3;
        }

        .btn-delete {
            background: #dc3545;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background 0.3s;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .btn-new-chat {
            background: #2c3e50;
            color: #ffffff;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            transition: background 0.3s;
            z-index: 1; /* Ensure the button is in the foreground */
        }

        .btn-new-chat:hover {
            background: #1a252f;
        }

        .text-center {
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }

        .icon {
            font-size: 50px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 5px 10px;
        }

        .search-bar button {
            padding: 5px 10px;
        }

        .search-bar form {
            display: flex;
            gap: 10px;
        }

        .settings-button {
            background: none;
            border: none;
            color: #f8f9fa;
            font-size: 20px;
        }

        .settings-button .fa-gear {
            font-size: 24px;
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
                        <i class="fa-solid fa-gear"></i>
                    </button>
                    <div class="dropdown-menu" id="settingsDropdown" style="display:none; position: absolute; top: 60px; right: 20px;">
                        <a class="dropdown-item" href="settings.php">Einstellungen</a>
                        <a class="dropdown-item" href="help.php">Hilfe</a>
                        <a class="dropdown-item" href="logout.php">Logout</a>
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
                <div class="search-bar">
                    <form method="GET" class="d-flex w-100">
                        <input type="text" name="search" class="form-control" placeholder="Suche nach Chats" value="<?= htmlspecialchars($search_query); ?>">
                        <button type="submit" class="btn btn-primary">Suchen</button>
                    </form>
                    <form method="POST">
                        <button type="submit" name="new_chat" class="btn btn-new-chat">Neuer Chat</button>
                    </form>
                </div>
                <h3 class="text-center mt-4"><i class="fas fa-comments icon"></i>Deine Chats</h3>
                <div class="chats-container mt-3">
                    <?php if (empty($chats)): ?>
                        <p class="text-center">Keine Chats vorhanden.</p>
                    <?php else: ?>
                        <?php foreach ($chats as $chat): ?>
                            <div class="chat-card">
                                <h5><?= htmlspecialchars($chat["title"]); ?></h5>
                                <div class="btn-group">
                                    <form action="./chat.php" method="get">
                                        <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $_SESSION['userid'] ?>">
                                        <button type="submit">öffnen</button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
                                        <button type="submit" name="delete_chat" class="btn btn-delete">Löschen</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
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
</body>

</html>