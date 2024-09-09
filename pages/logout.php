<?php
// startet die sitzung
session_start();

$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// terminiert die sitzung
session_destroy();

// db verbinndung wird getrrennt wenn vorhanden
if (isset($db_connection)) {
    $db_connection->close();
}

// schickt auf Main page
header("Location: main.php");
exit;
?>