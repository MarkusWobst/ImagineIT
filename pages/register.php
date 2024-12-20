<?php
require_once "../composables/db.php";
$message = '';
$message_class = '';
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrieren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #f0f0f0;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <h2 class="text-center">Account erstellen</h2>
                <form action="/register" method="POST" class="form-signin">
                    <div class="form-group mb-3">
                        <label for="username" class="form-label">Benutzername</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Passwort</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="confirm_password" class="form-label">Passwort bestätigen</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Account erstellen</button>
                </form>
                <p class="<?php echo $message_class; ?> text-center mt-3">
                    <?php echo $message; ?>
                </p>
                <p class="text-center mt-3">
                    Bereits ein Konto? <a href="/login">Hier einloggen</a>
                </p>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/cbor-js@0.1.0/cbor.min.js"></script>

    <script>
        async function main() {

            const publicKeyCredentialCreationOptions = {
                challenge: Uint8Array.from(
                    "<?= base64_encode(random_bytes(32)) ?>", c => c.charCodeAt(0)),
                rp: {
                    name: "ImagineIT",
                    id: window.location.hostname,
                },
                user: {
                    id: Uint8Array.from(
                        "UZSL85T9AFC", c => c.charCodeAt(0)),
                    name: "<?= $_POST['username'] ?? '' ?>", //beide gleich
                    displayName: "<?= isset($_POST['username']) ? $_POST['username'] : '' ?>", 
                },
                pubKeyCredParams: [], // { alg: -7, type: "public-key" }
                authenticatorSelection: {
                    authenticatorAttachment: "platform",
                    "residentKey": "preferred",
                    "requireResidentKey": false,
                    "userVerification": "preferred"
                },
                timeout: 60000,
                attestation: "none",
                "hints": [],
                "extensions": {
                    "credProps": true
                }
            };
            const credential = await navigator.credentials.create({
                publicKey: publicKeyCredentialCreationOptions
            });

            // decode the clientDataJSON into a utf-8 string
            const utf8Decoder = new TextDecoder('utf-8');
            const decodedClientData = utf8Decoder.decode(
                credential.response.clientDataJSON)

            // parse the string as an object
            const clientDataObj = JSON.parse(decodedClientData);

            const decodedAttestationObject = CBOR.decode(
                credential.response.attestationObject);

            const { authData } = decodedAttestationObject;

            // get the length of the credential ID
            const dataView = new DataView(
                new ArrayBuffer(2));
            const idLenBytes = authData.slice(53, 55);
            idLenBytes.forEach(
                (value, index) => dataView.setUint8(
                    index, value));
            const credentialIdLength = dataView.getUint16();

            // get the credential ID
            const credentialId = authData.slice(  //credentialId into db for users
                55, 55 + credentialIdLength);
            // console.log("CredID: " + credentialId)
            // get the public key object
            const publicKeyBytes = authData.slice(  //publicKeyBytes into db for users (Server has public key associated to user now -> User can authorize with his private key)
                55 + credentialIdLength);

            // the publicKeyBytes are encoded again as CBOR
            const publicKeyObject = CBOR.decode(
                publicKeyBytes.buffer);
            // console.log("publicKeyObject CBOR decoded: " + publicKeyObject)

            const response = await fetch("/create-user", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    username: "<?= $_POST['username'] ?? '' ?>",
                    password: "<?= $_POST['password'] ?? '' ?>",
                    credentialId: credentialId,
                    publicKeyBytes: publicKeyBytes,
                })
            })
            const data = await response.json()
            if (data.success) {
                window.location.href = '/login'
            }
        }

        <?php

        $message = '';
        $message_class = '';
        $username = $_POST['username'] ?? '' ;
        $password = $_POST['password'] ?? '' ;
        $confirm_password = $_POST['confirm_password'];

        // Regex patterns
        $username_pattern = '/^[\w!@#$%^&*()\-+=]{3,16}$/'; // Alphanumeric, underscores, and special characters, 3-16 characters
        $password_pattern = '/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d!@#$%^&*()\-+=]{8,}$/'; // Minimum 8 characters, at least one letter, one number, and special characters
        
        // Validate username
        if (!preg_match($username_pattern, $username)) {
            $message = 'Der Benutzername muss 3-16 Zeichen lang sein und darf nur Buchstaben, Zahlen, Unterstriche und Sonderzeichen enthalten.';
            $message_class = 'text-danger';
        }
        // Validate password
        elseif (!preg_match($password_pattern, $password)) {
            $message = 'Das Passwort muss mindestens 8 Zeichen lang sein und mindestens einen Buchstaben, eine Zahl und ein Sonderzeichen enthalten.';
            $message_class = 'text-danger';
        }
        // Check if passwords match
        elseif ($password !== $confirm_password) {
            $message = 'Die Passwörter stimmen nicht überein.';
            $message_class = 'text-danger';
        } else {
            // Check if username already exists
            $stmt = db()->prepare('SELECT * FROM users WHERE username = :username');
            $stmt->bindValue(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $message = 'Der Benutzername ist bereits vergeben.';
                $message_class = 'text-danger';
            } else {
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    ?>
                    main()
                    <?php
                }
            }
        }
        ?>

    </script>

</body>

</html>