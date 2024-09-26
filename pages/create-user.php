<?php

require_once "../composables/db.php";


$username = $_POST['username'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$credentialid = $asdf;
$publickeybytes = $asdf;

$salt = bin2hex(random_bytes(32));
$pepper = bin2hex(random_bytes(32));
$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
$encryptionKey = random_bytes(32);

$hashed_password = password_hash($salt . $password, PASSWORD_DEFAULT);

// Add the new user to the database
$stmt = db()->prepare('INSERT INTO users (username, password, iv, salt, pepper, aeskey, credentialid, publickeybytes) VALUES (:username, :password, :iv, :salt, :pepper, :aeskey, :credentialid, :publickeybytes)');
$stmt->bindValue(':username', $username);
$stmt->bindValue(':password', $hashed_password);
$stmt->bindValue(':iv', base64_encode($iv));
$stmt->bindValue(':salt', $salt);
$stmt->bindValue(':pepper', $pepper);
$stmt->bindValue(
    ':aeskey',
    openssl_encrypt(
        $encryptionKey,
        'AES-256-CBC',
        hash('SHA256', $pepper . $password),
        0,
        $iv
    )
);
$stmt->bindValue(':credentialid', $data['credentialId']);
$stmt->bindValue(':publickeybytes', $data['publicKeyBytes']);
;
$stmt->execute();

// User successfully registered, set session variables and redirect to the homepage
$_SESSION['username'] = $username;
$_SESSION['userid'] = db()->lastInsertId();

echo json_encode([
    'success' => true
]);