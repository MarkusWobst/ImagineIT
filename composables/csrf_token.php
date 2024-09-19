<?php

// Generate a CSRF token and store it in the session
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function verify_csrf_token($token) {
    $verified = false;
    $verified = $token === $_SESSION['csrf_token'];
    if (!$verified) {
        throw new Exception('Invalid CSRF token');
    }
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $verified;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'];
    if (!verify_csrf_token($csrf_token)) {
        throw new Exception('Invalid CSRF token');
    }
}

?>