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
    var_dump($chatid);
    die;
    header('Location: logout.php');

    session_abort();
}

$messages = [];
if (isset($_GET['chat_id'])) {
    $chat = db()->query("SELECT * FROM `chat_records` WHERE `id` = '{$_GET['chat_id']}'")->fetch();
    $messages = db()->query("SELECT * FROM `messages` WHERE `id` = '{$_GET['chat_id']}' ORDER BY created_at")->fetchAll();
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
    <style>
        body {
            font-family: Arial, sans-serif;
            /* background: url('../pictures/Freakbob.png') no-repeat center center fixed; */
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }

        .container h1 {
            margin-bottom: 20px;
        }

        .new-chat-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .new-chat-btn:hover {
            background-color: #0056b3;
        }

        .chat-container {
            display: none;
            flex-direction: column;
            align-items: center;
        }

        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 10px;
            width: 100%;
        }

        .input-box {
            display: flex;
            width: 100%;
        }

        .input-box input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }

        .input-box button {
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        .input-box button:hover {
            background-color: #0056b3;
        }

        .input-box input[type="file"] {
            display: none;
        }

        .input-box label {
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            margin-left: 5px;
        }

        .input-box label:hover {
            background-color: #218838;
        }
    </style>
</head>

<body>
    <div class="container py-4 flex-grow-1">
        <div class="card w-100 h-100">
            <div class="card-body d-flex flex-column h-100">
                <h5 class="card-title">
                    <?= $chat['id'] ?>
                </h5>

                <?php foreach ($messages as $message) { ?>
                    <div
                        class="card mb-3 w-75 <?= $message['role'] === 'user' ? 'align-self-end bg-success-subtle' : 'bg-info-subtle' ?>">
                        <div class="card-body">
                            <p class="card-text m-0">
                                <?= $message['content'] ?>
                            </p>
                            <p class="m-0 text-end text-secondary" style="font-size: 10pt">
                                <?= $message['created_at'] ?>
                            </p>
                        </div>
                    </div>
                <?php } ?>
            </div>

            <div class="card-footer">
                <form action="/process-message.php" method="post">
                    <?php if ($chatid) { ?>
                        <input type="hidden" name="chat_id" value="<?= $_GET['chat_id'] ?>">
                    <?php } ?>

                    <div class="input-group">
                        <input type="text" class="form-control" name="message" placeholder="Nachricht ..." required>

                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">Senden</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>