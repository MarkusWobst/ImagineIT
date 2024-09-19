<?php
require_once "db.php";
function encrypt(string $message) {
    $userid = $_SESSION['userid'];
    $key = $_SESSION['keySHA256']; // 32 bytes (256 bits) for AES-256
    $iv = base64_decode(db()->query("SELECT iv FROM users WHERE id = '{$userid}'")->fetchColumn());
    
    return openssl_encrypt($message, 'AES-256-CBC', $key, 0, $iv); //ENCRYPTION
}

function decrypt (string $message) {
    $userid = $_SESSION['userid'];
    $key = $_SESSION['keySHA256']; // 32 bytes (256 bits) for AES-256
    $iv = base64_decode(db()->query("SELECT iv FROM users WHERE id = '{$userid}'")->fetchColumn());
    
    return openssl_decrypt($message, 'AES-256-CBC', $key, 0, $iv); //DECRYPTION
}

?>