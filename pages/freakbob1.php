<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Freakbob</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('../pictures/Freakbob.png') no-repeat center center fixed;
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
    <div class="container">
        <div class="pre-chat">
            <h1>Willkommen zu Freakbob</h1>
            <form method="post" enctype="multipart/form-data">
                <button class="new-chat-btn" type="submit" name="start_chat">Neuer bob</button>
            </form>
        </div>
        <?php if (isset($_POST['start_chat']) || isset($_POST['send_message']) || isset($_FILES['file_input'])): ?>
        <div class="chat-container" id="chat-container" style="display: flex;">
            <div class="chat-box" id="chat-box">
                <!-- Chat messages will appear here -->
                <?php
                if (isset($_POST['send_message'])) {
                    $userInput = trim(strtolower($_POST['user_input']));
                    echo "<div>Du: " . htmlspecialchars($userInput) . "</div>";

                    // Hier kannst du deine Logik für die Verarbeitung der Nachricht hinzufügen
                    if ($userInput === 'freakbob') {
                        $response = 'Freaky!';
                        $imageUrl = '../pictures/Freakbob.png'; // Pfad zu deinem Bild
                    } else {
                       
                        //Hier Ki Zeug machen <---------------

                    }

                    echo "<div>Freakbob: " . htmlspecialchars($response) . "</div>";
                    if ($imageUrl) {
                        echo "<div><img src='" . htmlspecialchars($imageUrl) . "' style='max-width: 100%; margin-top: 10px;'></div>";
                    }
                }

                if (isset($_FILES['file_input']) && $_FILES['file_input']['error'] === UPLOAD_ERR_OK) {
                    $file = $_FILES['file_input'];
                    $uploadDir = 'uploads/';
                    $uploadFile = $uploadDir . basename($file['name']);
                    if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                        echo "<div><img src='" . htmlspecialchars($uploadFile) . "' style='max-width: 100%; margin-top: 10px;'></div>";
                    } else {
                        echo "<div>Fehler beim Hochladen des Bildes.</div>";
                    }
                } elseif (isset($_FILES['file_input'])) {
                    echo "<div></div>";
                }
                ?>
            </div>
            <form method="post" enctype="multipart/form-data" class="input-box">
                <input type="text" name="user_input" placeholder="Schreibe eine Nachricht...">
                <button type="submit" name="send_message">Senden</button>
                <input type="file" name="file_input" id="file-input" accept="image/*">
                <label for="file-input">Bild hochladen</label>
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>