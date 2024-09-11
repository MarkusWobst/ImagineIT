<?php

require_once "../composables/db.php";

session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    header('Location: start.php');
    exit();
}

$userid = $_SESSION['userid'];
$chatid = $_GET["chat_id"];

$ai_type = "";
$messages = [];

try {
    $chat_stmt = db()->prepare('SELECT id, ai_type FROM chat_records WHERE user_id = :userid AND id = :chatid');
    $chat_stmt->bindValue(':userid', $userid);
    $chat_stmt->bindValue(':chatid', $chatid);
    $chat_stmt->execute();
    $chat = $chat_stmt->fetch(PDO::FETCH_ASSOC);
    if ($chat) {
        $ai_type = $chat['ai_type'];
    } else {
        throw new Exception("you shall not pass!!!");
    }
} catch (\Throwable $th) {
    header('Location: logout.php');
    session_abort();
}

// Generate system prompt based on AI type
$system_prompt = "You are ";
switch ($ai_type) {
    case 'storyteller':
        $system_prompt .= "a Storyteller AI. Please craft engaging and captivating stories.";
        break;
    case 'image_generator':
        $system_prompt .= "an Image Generator AI. Please describe images or generate image concepts.";
        break;
    case 'picture_to_text':
        $system_prompt .= "a Picture to Text AI. Please convert visuals into textual descriptions.";
        break;
    case 'song_writer':
        $system_prompt .= "a Song Writer AI. Please create lyrics and melodies.";
        break;
    default:
        $system_prompt .= "an AI. Please assist with your specific capabilities.";
        break;
}

if (isset($_GET['chat_id'])) {
    $chat = db()->query("SELECT * FROM `chat_records` WHERE `id` = '{$_GET['chat_id']}'")->fetch();
    if ($chat) {
        $messages = db()->query("SELECT * FROM `messages` WHERE `chat_id` = '{$_GET['chat_id']}' ORDER BY created_at DESC")->fetchAll();
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Type: <?= htmlspecialchars($ai_type) ?></title>
    <!-- Bootstrap CSS -->
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

        .chat-history {
            max-height: 60vh;
            overflow-y: auto;
            padding: 20px;
            border-top: 1px solid #ddd;
        }

        .user-message {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .assistant-message {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 10px;
        }

        .message-content {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
        }

        .user-message .message-content {
            background-color: #d1e7ff;
            color: #004085;
        }

        .assistant-message .message-content {
            background-color: #d4edda;
            color: #155724;
        }

        .input-group {
            position: sticky;
            bottom: 0;
        }

        .input-group input[type="file"] {
            position: absolute;
            left: -9999px;
            opacity: 0;
            cursor: default;
            pointer-events: none;
        }

        .dowload-icon {
            padding-left: 10px;
            padding-right: 10px;
        }

        .btn-send {
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background 0.3s;
        }

        .btn-send:hover {
            background: #0056b3;
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
        <a class="navbar-brand" href="#">Chat Type: <?= htmlspecialchars($ai_type) ?></a>
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
            <div class="main-content">
                <div class="chat-history">
                    <h5>Chat History</h5>
                    <ul class="list-unstyled">
                        <?php if (!empty($messages)) { ?>
                            <?php foreach ($messages as $message) { ?>
                                <li class="<?= $message['role'] === 'user' ? 'user-message' : 'assistant-message' ?>">
                                    <div class="message-content">
                                        <?= htmlspecialchars($message['content']) ?>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>No messages found.</li>
                        <?php } ?>
                    </ul>
                </div>

                <div class="card-footer">
                    <form action="process-message.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="chat_id" value="<?= $_GET['chat_id'] ?>">
                        <input type="hidden" name="system_prompt" value="<?= htmlspecialchars($system_prompt) ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="message" placeholder="Nachricht ..." required>
                            <label for="file-input">
                                <i class="bi bi-upload fs-4 dowload-icon"> </i>
                            </label>
                            <input type="file" class="form-control input-sm" name="image" id="file-input">
                            <button class="btn btn-send" type="submit">Senden</button>
                        </div>
                    </form>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>