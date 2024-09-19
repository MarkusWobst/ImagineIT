<?php

require_once "../composables/db.php";
require_once "../composables/csrf_token.php";
require_once "../composables/aes.php";

$userid = $_SESSION['userid'];
$chatid = $_GET["chat_id"];

$ai_type = "";
$messages = [];

// Get the chat messages from the database
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
    header('Location: /logout');
    session_abort();
}

// Generate system prompt based on AI type
$system_prompt = "You are ";
switch ($ai_type) {
    case 'storyteller':
        $system_prompt .= "a Storyteller AI. Please craft engaging and captivating stories.";
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

// Handle dropdown toggle
if (isset($_POST['toggle_dropdown'])) {
    $_SESSION['dropdown_visible'] = !isset($_SESSION['dropdown_visible']) || !$_SESSION['dropdown_visible'];
}

// Determine dropdown visibility
$dropdown_visible = isset($_SESSION['dropdown_visible']) && $_SESSION['dropdown_visible'];

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
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background: #343a40;
        }

        .navbar-brand, .nav-link, .btn-outline-danger {
            color: #f8f9fa !important;
        }

        .container {
            flex: 1;
            display: flex;
            flex-direction: column;
            margin-top: 20px;
        }

        .main-content {
            background: #ffffff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            flex: 1;
            overflow: hidden;
        }

        .main-content h3 {
            position: relative;
            z-index: 1;
        }

        .chat-history {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            border-top: 1px solid #ddd;
        }

        .user-message {
            display: -webkit-box;  /* For older Safari and iOS browsers */
            display: -ms-flexbox;  /* For Internet Explorer 10 */
            display: flex;  /* Standard syntax */
            justify-content: flex-end;
            margin-bottom: 10px;
        }

        .message-content {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
            display: flex;
            flex-direction: column;
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
            background: #ffffff;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        .input-group input[type="file"] {
            position: absolute;
            opacity: 0;
            cursor: default;
            pointer-events: none;
        }

        .btn-send {
            background: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background 0.3s;
            margin-left: 10px; /* Add space between the buttons */
        }

        .btn-send:hover {
            background: #0056b3;
        }

        .btn-upload {
            background: #28a745;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            padding: 10px 20px;
            transition: background 0.3s;
            margin-left: 10px;
        }

        .btn-upload:hover {
            background: #218838;
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

        .spinner-border {
            width: 1rem;
            height: 1rem;
            border-width: 0.2em;
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
                    <form method="POST" style="display: inline;">
                        <button class="settings-button" name="toggle_dropdown">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    </form>
                    <div class="dropdown-menu" id="settingsDropdown" style="display: <?= $dropdown_visible ? 'block' : 'none' ?>; position: absolute; top: 60px; right: 20px;">
                        <a class="dropdown-item" href="/index">Home</a>
                        <a class="dropdown-item" href="/settings">Einstellungen</a>
                        <a class="dropdown-item" href="/help">Hilfe</a>
                        <a class="dropdown-item logout" href="/logout">Logout</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row flex-grow-1">
        <div class="col-md-12">
            <div class="main-content">
                <div class="card-footer">
                    <form action="/process-message" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="chat_id" value="<?= $_GET['chat_id'] ?>">
                        <input type="hidden" name="system_prompt" value="<?= htmlspecialchars($system_prompt) ?>">
                        <div class="input-group">
                            <input type="text" class="form-control" name="message" placeholder="Nachricht ..." required>
                            <input type="file" class="form-control input-sm" name="images[]" id="file-input" multiple>
                            <button class="btn btn-upload" type="button" onclick="document.getElementById('file-input').click();"><i class="fas fa-upload"></i></button>
                            <button class="btn btn-send" type="submit" id="sendButton">Senden</button>
                        </div>
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    </form> 
                </div>
                <div class="chat-history">
                    <h5>Chat History</h5>
                    <ul class="list-unstyled">
                        <?php if (!empty($messages)) { ?>
                            <?php foreach ($messages as $message) { ?>
                                <li class="<?= $message['role'] === 'user' ? 'user-message' : 'assistant-message' ?>">
                                    <div class="message-content">
                                        <?php if (!empty($message['images'])) { ?>
                                            <img src="data:image/jpeg;base64,<?php echo decrypt($message['images']) ?>" alt="Uploaded Image" style="max-width: 250px;">
                                        <?php } ?>
                                        <div><?= htmlspecialchars(decrypt($message['content'])) ?></div>
                                    </div>
                                </li>
                            <?php } ?>
                        <?php } else { ?>
                            <li>Sende eine Nachricht um zu Starten</li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const messageForm = document.getElementById('messageForm');
    const sendButton = document.getElementById('sendButton');

    messageForm.addEventListener('submit', function() {
        sendButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Senden...';
        sendButton.disabled = true;
    });
</script>

</body>

</html>