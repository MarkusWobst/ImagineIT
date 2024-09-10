<?php

require_once "../composables/db.php";

session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: start.php');
    exit();
}

$userid = $_SESSION['userid'];
$chatid = $_POST['chat_id'];

try {
    // Check if the chat exists and belongs to the user
    $chat_stmt = db()->prepare('SELECT id FROM chat_records WHERE user_id = :userid AND id = :chatid');
    $chat_stmt->bindValue(':userid', $userid);
    $chat_stmt->bindValue(':chatid', $chatid);
    $chat_stmt->execute();
    $chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);
    if (count($chats) == 0) {
        throw new Exception("You do not have permission to delete this chat.");
    }

    // Delete associated messages
    $message_stmt = db()->prepare('DELETE FROM messages WHERE chat_id = :chatid');
    $message_stmt->bindValue(':chatid', $chatid);
    $message_stmt->execute();

    // Delete the chat itself
    $chat_delete_stmt = db()->prepare('DELETE FROM chat_records WHERE id = :chatid');
    $chat_delete_stmt->bindValue(':chatid', $chatid);
    $chat_delete_stmt->execute();

    // Redirect to a confirmation page or home page
    header('Location: chats.php');
    exit();

} catch (Exception $e) {
    // Handle errors (e.g., log them, show them to the user)
    echo "Error: " . $e->getMessage();
    // Log error or redirect to an appropriate error page
    header('Location: error.php');
    exit();
}