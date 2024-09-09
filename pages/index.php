<?php

require_once "../composables/db.php";

session_start();

// Überprüfe, ob der Benutzer eingeloggt ist
if (!isset($_SESSION['username'])) {
    header('Location: start.php');
    exit();
}

$username = $_SESSION['username'];
$userid = $_SESSION['userid'];

// Hole alle Chats, die zur aktuellen userid gehören
$chat_stmt = db()->prepare('SELECT chat_id FROM chat_records WHERE user_id = :userid');
$chat_stmt->bindValue(':userid', $userid);
$chat_stmt->execute();

// $chats = [];

$chats = $chat_stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($chats as $chat) {
    $chats_array[] = $chat['chat_id'];
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hauptseite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand btn btn-outline-primary" href="index.php" style="background: transparent; border-color: transparent;">Main Page</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="btn btn-sm btn-outline-danger" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Willkommen, <?php echo htmlspecialchars($username); ?>!</h2>
            <p class="text-center">Dies ist eine geschützte Seite, nur für eingeloggte Benutzer.</p>

            <h3 class="text-center mt-4">Deine Chats</h3>
            <div class="chats-container mt-3 p-3 border border-secondary rounded">
                <?php if (empty($chats)): ?>
                    <p class="text-center">Keine Chats vorhanden.</p>
                <?php else: ?>
                    <?php foreach ($chats as $chatid): ?>
                        <div class="chat-id bg-light p-2 mb-2 text-center">
                            <?php echo htmlspecialchars($chatid['chat_id']); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>