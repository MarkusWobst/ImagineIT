<?php

require_once "../composables/db.php";
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$password = $data['password'];
$credentialId = $data['credentialId'];
$publicKeyBytes = $data['publicKeyBytes'];

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
$stmt->bindValue(':credentialid', json_encode($data['credentialId']));
$stmt->bindValue(':publickeybytes', json_encode($data['publicKeyBytes']));
$stmt->execute();
var_dump($stmt);
die;

// User successfully registered, set session variables and redirect to the homepage
$_SESSION['username'] = $username;
$_SESSION['userid'] = db()->lastInsertId();

echo json_encode([
    'success' => true
]);