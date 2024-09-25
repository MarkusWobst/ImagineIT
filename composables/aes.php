<?php
require_once "db.php";
function encrypt(string $message)
{
    $userid = $_SESSION['userid'];
    $key = $_SESSION['keySHA256'];
    $iv = base64_decode(db()->query("SELECT iv FROM users WHERE id = '{$userid}'")->fetchColumn());

    return openssl_encrypt($message, 'AES-256-CBC', $key, 0, $iv);
}

function decrypt(string $message)
{
    $key = $_SESSION['keySHA256'];
    $userid = $_SESSION['userid'];
    $iv = base64_decode(db()->query("SELECT iv FROM users WHERE id = '{$userid}'")->fetchColumn());

    return openssl_decrypt($message, 'AES-256-CBC', $key, 0, $iv);
}

function changekey($newpassword)
{
    $userid = $_SESSION['userid'];
    $oldkey = $_SESSION['keySHA256'];

    $stuff = db()->query("SELECT iv, pepper, aeskey FROM users WHERE id = '{$userid}'")->fetchColumn();
    $iv = base64_decode($stuff['iv']);
    $pepper = base64_decode($stuff['pepper']);
    $aeskey = $stuff['aeskey'];
    
    $encryptionkey = openssl_decrypt($aeskey, 'AES-256-CBC', $oldkey, 0, $iv);
    $newaeskey = openssl_encrypt($encryptionkey, 'AES-256-CBC', hash('SHA256', $pepper.$newpassword), 0, $iv);
    db()->query("UPDATE users SET aeskey = '{$newaeskey}' WHERE id = '{$userid}'");
}

?>