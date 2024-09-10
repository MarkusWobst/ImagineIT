<?php
require_once '../composables/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $chatid = $data['chat_id'] ?? null;

    if ($chatid) {
        $db = db();
        $db->beginTransaction();

        // Delete messages
        $stmt = $db->prepare("DELETE FROM messages WHERE chat_id = :chatid");
        $stmt->bindParam(':chatid', $chatid, PDO::PARAM_INT);
        $stmt->execute();

        // Delete the chat record
        $stmt = $db->prepare("DELETE FROM chat_records WHERE id = :chatid");
        $stmt->bindParam(':chatid', $chatid, PDO::PARAM_INT);
        $stmt->execute();

        $db->commit();

        echo json_encode([
            'success' => true
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid chat ID'
        ]);
    }
} catch (Exception $e) {
    error_log('Error deleting chat: ' . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete chat'
    ]);
}