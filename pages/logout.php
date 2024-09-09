<?php
// Startet die Sitzung
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Terminiert die Sitzung
session_destroy();

// DB Verbindung wird getrennt wenn vorhanden
if (isset($db_connection)) {
    $db_connection->close();
}

// Schickt auf die Main Page
header("Location: main.php");
exit;
?>