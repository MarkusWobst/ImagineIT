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

try {
    $chat_stmt = db()->prepare('SELECT id FROM chat_records WHERE user_id = :userid AND id = :chatid');
    $chat_stmt->bindValue(':userid', $userid);
    $chat_stmt->bindValue(':chatid', $chatid);
    $chat_stmt->execute();
    $chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($chats) == 0) {
        throw new Exception("you shall not pass!!!");
    }
} catch (\Throwable $th) {
    header('Location: logout.php');
    session_abort();
}

$messages = [];
if (isset($_GET['chat_id'])) {
    $chat = db()->query("SELECT * FROM `chat_records` WHERE `id` = '{$_GET['chat_id']}'")->fetch();
    $messages = db()->query("SELECT * FROM `messages` WHERE `chat_id` = '{$_GET['chat_id']}' ORDER BY created_at DESC")->fetchAll();
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Id:
        <?= $chatid ?>
    </title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url("https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css");

        body {
            background-size: cover;
            margin: 0;
        }

        .chat-history {
            max-height: 80vh;
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
            background-color: #e6f2ff;
            color: #007bff;
        }

        .assistant-message .message-content {
            background-color: #e6f9e6;
            color: #28b463;
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
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Chat ID:
                    <?= $chat['id'] ?>
                </h5>
            </div>

            <div class="card-footer">
                <form action="/process-message.php" method="post" enctype="multipart/form-data">
                    <?php if ($chatid) { ?>
                        <input type="hidden" name="chat_id" value="<?= $_GET['chat_id'] ?>">
                    <?php } ?>

                    <div class="input-group">
                        <input type="text" class="form-control" name="message" placeholder="Nachricht ..." required>
                        <label for="file-input">
                            <i class="bi bi-upload fs-4 dowload-icon"> </i>
                        </label>
                        <input type="file" class="form-control input-sm" name="image"
                            id="file-input">
                        <!-- <button class="btn btn-primary" type="file">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                class="bi bi-upload" viewBox="0 0 16 16">
                                <path
                                    d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                <path
                                    d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
                            </svg>
                        </button> -->

                        <button class="btn btn-primary" type="submit">Senden</button>
                    </div>
                </form>
            </div>

            <div class="chat-history">
                <h5>Chat History</h5>
                <ul class="list-unstyled">
                    <?php foreach ($messages as $message) { ?>
                        <li class="<?= $message['role'] === 'user' ? 'user-message' : 'assistant-message' ?>">
                            <div class="message-content">
                                <?= $message['content'] ?>
                            </div>
                        </li>
                    <?php } ?>
                </ul>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js">    </script>
</body>

</html>