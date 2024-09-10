<?php

require_once "../composables/db.php";

session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
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

    // Nach dem Erstellen des Chats Seite neu laden, um die Änderung anzuzeigen
    header('Location: chat.php');
    exit();
}

// Prüfe, ob der Button "Löschen" gedrückt wurde
if (isset($_POST['delete_chat']) && isset($_POST['chat_id'])) {
    $chat_id = $_POST['chat_id'];

    // Lösche den Chat aus der Datenbank
    $stmt = db()->prepare('DELETE FROM chat_records WHERE id = :chat_id AND user_id = :userid');
    $stmt->bindValue(':chat_id', $chat_id, PDO::PARAM_INT);
    $stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    // Nach dem Löschen des Chats Seite neu laden, um die Änderung anzuzeigen
    header('Location: chat.php');
    exit();
}

// Hole alle Chats, die zur aktuellen userid gehören
$chat_stmt = db()->prepare('SELECT * FROM chat_records WHERE user_id = :userid');
$chat_stmt->bindValue(':userid', $userid);
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

        .main-content::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(0, 123, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }

        .main-content::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(40, 167, 69, 0.1);
            border-radius: 50%;
            z-index: 0;
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
            background: #17a2b8;
            border: none;
            transition: background 0.3s;
        }

        .btn-new-chat:hover {
            background: #138496;
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
                        <a class="btn btn-sm btn-outline-danger" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="main-content">
                    <div class="search-bar">
                        <input type="text" class="form-control" placeholder="Suche nach Chats...">
                        <form method="POST">
                            <button type="submit" name="new_chat" class="btn btn-new-chat">Neuer Chat</button>
                        </form>
                    </div>
                    <h3 class="text-center"><i class="fas fa-comments icon"></i>Deine Chats</h3>
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
</body>

</html>