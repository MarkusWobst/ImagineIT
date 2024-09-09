<?php

require_once 'db.php';

$chatId = $_POST['chat_id'] ?? null;

$messages = [];
if ($chatId) {
    $messages = db()->query("SELECT * FROM `messages` WHERE `chat_id` = '{$chatId}' ORDER BY created_at")->fetchAll();
} else {
    db()->exec("INSERT INTO `chats` (title) VALUES ('Neuer Chat')");
    $chatId = db()->lastInsertId();
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
$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at) VALUES ({$chatId}, 'user', '{$content}', CURRENT_TIMESTAMP)");

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
$messages = db()->exec("INSERT INTO messages (chat_id, role, content, created_at) VALUES ({$chatId}, 'assistent', '{$content}', CURRENT_TIMESTAMP)");

header('Location: index.php?chat_id=' . $chatId);
