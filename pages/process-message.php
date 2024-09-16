<?php

require_once '../composables/db.php';

session_start();

$chatid = $_POST["chat_id"] ?? null;
$_SESSION['chat_id'] = $chatid;
$userid = $_SESSION['userid'];

$messages = [];
if ($chatid) {
    $messages = db()->query("SELECT * FROM `messages` WHERE `chat_id` = '{$chatid}' ORDER BY created_at")->fetchAll();
} else {
    db()->exec("INSERT INTO `chat_records` (title, user_id) VALUES ('Neuer Chat', {$userid})");
    $chatid = db()->lastInsertId();
}

// Fetch the AI type and generate system prompt
$chat_stmt = db()->prepare('SELECT ai_type FROM chat_records WHERE id = :chatid AND user_id = :userid');
$chat_stmt->bindValue(':chatid', $chatid, PDO::PARAM_INT);
$chat_stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
$chat_stmt->execute();
$ai_record = $chat_stmt->fetch(PDO::FETCH_ASSOC);
$ai_type = $ai_record['ai_type'];

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

// File input handling
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
    if ($_FILES["image"]["size"] > 5000000) {
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
        'messages' => [],
        'system' => $system_prompt // Add system prompt here
    ];

    foreach ($messages as $message) {
        $body['messages'][] = [
            'role' => $message['role'],
            'content' => $message['content'],
            'images' => $message['images'] ?? null,
        ];
    }

    $body['messages'][] = [
        'role' => 'user',
        'content' => htmlspecialchars($_POST['message'], ENT_QUOTES),
        'images' => [$imagestring],
    ];

} catch (\Throwable $th) {
    $fileattached = false;

    $body = [
        'model' => 'llava-phi3',
        'stream' => false,
        'system' => $system_prompt // Add system prompt here
    ];

    foreach ($messages as $message) {
        $body['messages'][] = [
            'role' => $message['role'],
            'content' => htmlspecialchars($_POST['message'], ENT_QUOTES),
        ];
    }

    $body['messages'][] = [
        'role' => 'user',
        'content' => htmlspecialchars($_POST['message'], ENT_QUOTES),
    ];

}

$content = SQLite3::escapeString(htmlspecialchars($_POST['message'], ENT_QUOTES));

$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at, images) 
    VALUES ({$chatid}, 'user', '{$content}', CURRENT_TIMESTAMP, '{$imagestring}')");

$username = "ollama";
$password = "ollama-sepe";


$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => 'https://ollama.programado.de/api/chat',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_USERPWD => "{$username}:{$password}",
    CURLOPT_POSTFIELDS => json_encode($body)
]);
$data = json_decode(curl_exec($ch), true);
curl_close($ch);

$content = SQLite3::escapeString($data['message']['content'] ?? $data['messages'][0]['content']);
$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at) VALUES ({$chatid}, 'assistent', '{$content}', CURRENT_TIMESTAMP)");

header('Location: chat.php?chat_id=' . $chatid);

exit();
?>