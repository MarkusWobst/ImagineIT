<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen bei ImagineIT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #f0f0f0;
        }
        .container {
            margin-top: 50px;
        }
        .hero {
            text-align: center;
            margin-bottom: 50px;
        }
        .info-card {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-card img {
            max-width: 100%;
            margin-bottom: 15px;
            border-radius: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
        }
        .header-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .header-buttons .btn {
            margin-left: 10px;
        }
        .btn-light {
            color: #2c3e50;
            background-color: #ffffff;
            border-color: #ffffff;
        }
    </style>
</head>
<body>
<div class="header-buttons">
    <a href="/login" class="btn btn-light">Anmelden</a>
    <a href="register" class="btn btn-light">Registrieren</a>
</div>
<div class="container">
    <div class="hero">
        <h1>Willkommen bei ImagineIT</h1>
        <p>Ihre intelligente Assistentin, die Bilder in Geschichten verwandelt und Geschichten mit Bildern erzählt</p>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="info-card">
                <h2>Ritter Freakbob</h2>
                <img src="../pictures/freakbob%20ritter.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                <p></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h2>King Freakbob XIV</h2>
                <img src="../pictures/king%20bob.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                <p></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <h2>Super Bob</h2>
                <img src="../pictures/freakbob3.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
            </div>
        </div>
    </div>
    <div class="footer">
        <p>&copy; 2024 ImagineIT. Powered by LAVA-PHI3.</p>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.com/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>