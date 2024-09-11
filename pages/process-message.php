<?php

require_once '../composables/db.php';

session_start();

$chatid = $_POST["chat_id"] ?? null;
$_SESSION['chat_id'] = $chatid;
$userid = $_SESSION['userid'];

$messages = [];
if ($chatid) {
    $messages = db()->query("SELECT * FROM `messages` WHERE `id` = '{$chatid}' ORDER BY created_at")->fetchAll();
} else {
    db()->exec("INSERT INTO `chat_records` (title, user_id) VALUES ('Neuer Chat', {$userid})");
    $chatid = db()->lastInsertId();
}

// file input atuff
$fileattached = true;
$imagestring = NULL;
try {
    $target_file = file_get_contents($_FILES["image"]["tmp_name"]);

    $imageFileType = (mime_content_type($_FILES["image"]["tmp_name"]));

    // Check if file already exists
    if (!file_exists($_FILES["image"]["tmp_name"])) {
        throw new Exception("no file uoloaded --> use normal chat");
    }

    // Check file size
    if ($_FILES["image"]["size"] > 5000000) { // 5MB = 5000000
        throw new Exception("file is too large");
    }

    // Allow certain file formats
    if (
        $imageFileType != "image/jpg"
        && $imageFileType != "image/png"
        && $imageFileType != "image/jpeg"
    ) {
        throw new Exception("file isnt the right format");
    }

    $imagestring = base64_encode($target_file);

    $body = [
        'model' => 'llava-phi3',
        'stream' => false,
        'messages' => []
    ];

    foreach ($messages as $message) {
        $body['messages'][] = [
            'role' => $message['role'],
            'content' => $message['content'],
            'images' => $message['images'],
        ];
    }

    $body['messages'][] = [
        'role' => 'user',
        'content' => $_POST['message'],
        'images' => [$imagestring],
    ];


} catch (\Throwable $th) {
    $fileattached = false;

    $body = [
        'model' => 'llava-phi3',
        'stream' => false,
    ];

    foreach ($messages as $message) {
        $body['messages'][] = [
            'role' => $message['role'],
            'content' => $message['content'],
        ];
    }

    $body['messages'][] = [
        'role' => 'user',
        'content' => $_POST['message'],
    ];

}

$content = SQLite3::escapeString($_POST['message']);

$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at, images) 
    VALUES ({$chatid}, 'user', '{$content}', CURRENT_TIMESTAMP, '{$imagestring}')");

$username = "ollama";
$password = "ollama-sepe";


$ch = curl_init(); // such as http://example.com/example.xml
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://ollama.programado.de/api/chat',
    CURLOPT_RETURNTRANSFER => true,
    /**
     * Specify POST method
     */
    CURLOPT_POST => true,
    CURLOPT_USERPWD => "{$username}:{$password}",

    /**
     * Specify request content
     */
    CURLOPT_POSTFIELDS => json_encode($body)
]);
$data = json_decode(curl_exec($ch), true);
curl_close($ch);

$content = SQLite3::escapeString($data['message']['content'] ?? $data['messages'][0]['content']);
$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at) VALUES ({$chatid}, 'assistent', '{$content}', CURRENT_TIMESTAMP)");

header('Location: chat.php?chat_id=' . $chatid);
