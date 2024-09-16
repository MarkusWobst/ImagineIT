<?php

// datenbank verbindng trennen
if (isset($db_connection)) {
    $db_connection->close();
}

$_SESSION = array();

// cookies gelöcht
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
header("Location: /start");
exit;
?>