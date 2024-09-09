<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Geschichten Erzähler KI</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
            position: relative;
        }
        nav {
            margin: 20px 0;
            text-align: center;
        }
        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #333;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        footer {
            text-align: center;
            padding: 10px 0;
            background-color: #333;
            color: #fff;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .auth-buttons {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .auth-buttons a {
            margin-left: 10px;
            padding: 5px 10px;
            background-color: #fff;
            color: #333;
            text-decoration: none;
            border: 1px solid #333;
            border-radius: 5px;
        }
        .auth-buttons a:hover {
            background-color: #333;
            color: #fff;
        }
    </style>
</head>
<body>
<header>
    <h1>Willkommen zur Geschichten Erzähler KI</h1>
    <div class="auth-buttons">
        <a href="login.php">Login</a>
        <a href="register.php">Anmelden</a>
    </div>
</header>
<nav>
    <a href="#">Startseite</a>
    <a href="#">Über uns</a>
    <a href="#">Kontakt</a>
</nav>
<div class="container">
    <h2>Projektbeschreibung</h2>
    <p>
        <?php
        // Hier könnt ihr eine kurze Beschreibung eures Projekts einfügen
        echo "Unsere KI generiert Bilder, die perfekt zu den erzählten Geschichten passen. Tauchen Sie ein in eine Welt voller visueller Erzählungen.";
        ?>
    </p>
    <h2>Aktuelle Nachrichten</h2>
    <ul>
        <?php
        // Beispiel für dynamische Inhalte
        $news = [
            "Nachricht 1: Unsere KI hat eine neue Funktion zur verbesserten Bildgenerierung erhalten.",
            "Nachricht 2: Wir haben eine neue Partnerschaft mit einem führenden Verlag geschlossen.",
            "Nachricht 3: Unser Team wächst weiter und wir suchen neue Talente im Bereich KI-Entwicklung."
        ];

        foreach ($news as $item) {
            echo "<li>$item</li>";
        }
        ?>
    </ul>
</div>
<footer>
    &copy; <?php echo date("Y"); ?> ImagienIT. Alle Rechte vorbehalten.
</footer>
</body>
</html>