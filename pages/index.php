<?php

require_once "../composables/db.php";

$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

// Prüfe, ob der Button "Neuer Chat" gedrückt wurde
if (isset($_POST['create_chat'])) {
    $chat_title = $_POST['chat_title'];
    $ai_type = $_POST['ai_type'];

    // Füge den neuen Chat zur Datenbank hinzu
    $stmt = db()->prepare('INSERT INTO chat_records (user_id, title, ai_type) VALUES (:userid, :title, :ai_type)');
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->bindValue(':title', $chat_title, PDO::PARAM_STR);
    $stmt->bindValue(':ai_type', $ai_type, PDO::PARAM_STR);
    $stmt->execute();

    // Hole die ID des neu erstellten Chats
    $chat_id = db()->lastInsertId();

    // Nach dem Erstellen des Chats zur neuen Chat-Seite umleiten
    header('Location: /chat?chat_id=' . $chat_id);
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
        header('Location: /error');
        exit();
    }

    // Nach dem Löschen des Chats Seite neu laden und zu index umleiten
    header('Location: /index');
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
        justify-content: space-between;
        align-items: center; /* Center align items vertically */
        }

        .chat-card button {
        background: #007bff;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        padding: 10px 40px; /* Make the button wider */
        transition: background 0.3s;
        flex-grow: 1; /* Allow the button to grow and take available space */
        text-align: center; /* Center the text */
        }

        .chat-card button:hover {
            background: #0056b3;
        }

        .btn-delete {
            background: none;
            color: #dc3545;
            border: none;
            padding: 0;
            font-size: 1rem;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            height: 42px; /* Slightly higher than before */
            width: 35px; /* Keep the width as is */
            justify-content: center;
            margin-left: auto; /* Align to the right */
        }

        .btn-delete i {
            font-size: 1.5rem;
        }

        .btn-delete:hover {
            color: #c82333;
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
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="dropdown-menu" id="settingsDropdown" style="display:none; position: absolute; top: 60px; right: 20px;">
                        <a class="dropdown-item" href="/settings">Einstellungen</a>
                        <a class="dropdown-item" href="/help">Hilfe</a>
                        <a class="dropdown-item text-danger" href="/logout">Logout</a>
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
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i></button>
                    </form>
                    <button type="button" class="btn btn-new-chat" data-bs-toggle="modal" data-bs-target="#newChatModal">Neuer Chat</button>
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
                                    <form action="/chat" method="get">
                                        <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
                                        <button type="submit">öffnen</button>
                                    </form>
                                    <form method="POST">
                                        <input type="hidden" name="chat_id" value="<?= $chat['id'] ?>">
                                        <button type="submit" name="delete_chat" class="btn btn-delete"><i class="fas fa-trash"></i></button>
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

<!-- Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Neuer Chat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="chat_title" class="form-label">Chat Titel</label>
                        <input type="text" name="chat_title" class="form-control" id="chat_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="ai_type" class="form-label">AI-Typ</label>
                        <select class="form-select" name="ai_type" id="ai_type" required>
                            <option value="storyteller">Geschichtenerzähler</option>
                            <option value="picture_to_text">Bild zu Text</option>
                            <option value="song_writer">Song Writer</option>
                        </select>
                    </div>
                    <button type="submit" name="create_chat" class="btn btn-primary">Chat Erstellen</button>
                </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>