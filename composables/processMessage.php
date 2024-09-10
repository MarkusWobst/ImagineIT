<?php

require_once 'db.php';

session_start();

$chatid = $_GET['chat_id'] ?? null;
$_SESSION['chat_id'] = $chatid;
$userid = $_SESSION['user_id'];

$messages = [];
if ($chatid) {
    $messages = db()->query("SELECT * FROM `messages` WHERE `id` = '{$chatid}' ORDER BY created_at")->fetchAll();
} else {
    db()->exec("INSERT INTO `chat_records` (title, user_id) VALUES ('Neuer Chat', '{$userid}')");
    $chatid = db()->lastInsertId();
}

$body = [
    'model' => 'phi3',
    'stream' => false,
    'messages' => []
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

$content = SQLite3::escapeString($_POST['message']);
$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at) VALUES ({$chatid}, 'user', '{$content}', CURRENT_TIMESTAMP)");

$ch = curl_init(); // such as http://example.com/example.xml
curl_setopt_array($ch, [
    CURLOPT_URL => 'http://localhost:11434/api/chat',
    CURLOPT_RETURNTRANSFER => true,
    /**
     * Specify POST method
     */
    CURLOPT_POST => true,

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
