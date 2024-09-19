<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Willkommen bei ImagineIT</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(to right, #2c3e50 0%, #4ca1af 100%);
            color: #f0f0f0;
        }

        .container {
            margin-top: 50px;
            flex: 1;
        }

        footer {
            text-align: center;
            font-size: 14px;
            background-color: #2c3e50;
            color: #f0f0f0;
            padding: 10px;
            width: 100%; /* Full width */
        }

        .hero {
            text-align: center;
            margin-bottom: 50px;
        }

        .info-card {
            border-radius: 10px;
            margin-bottom: 20px;
            perspective: 1000px;
            height: 300px; /* Set a fixed height for the card */
        }

        .flip-card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            text-align: center;
            transition: transform 0.8s;
            transform-style: preserve-3d;
        }

        .info-card:hover .flip-card-inner {
            transform: rotateY(180deg);
        }

        .flip-card-front,
        .flip-card-back {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 10px;
        }

        .flip-card-front {
            background-color: transparent;
            color: black;
        }

        .flip-card-front img {
            max-width: 90%; /* Adjust image size */
            height: auto; /* Auto height */
            border-radius: 10px;
            margin: auto; /* Center the image */
            display: block; /* Block element */
        }

        .flip-card-back {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            transform: rotateY(180deg);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center; /* Vertical centering */
            align-items: center; /* Horizontal centering */
            text-align: center; /* Center text */
            height: 100%; /* Ensure height is 100% */
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
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="/freakbobritter.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                        </div>
                        <div class="flip-card-back">
                            <h2>Ritter Freakbob</h2>
                            <p>Ritter Freakbob, bekannt für seine stärke, hörte von einem Drachen, der ein Dorf terrorisierte. Mutig ritt er mit seinem Pferd Blitz in den verzauberten Wald. Nach einem langen Kampf besiegte er den Drachen, der sich in eine Fee verwandelte. Sie belohnte ihn mit einem magischen Amulett, und das Dorf feierte ihn als Helden.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="/kingbob.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                        </div>
                        <div class="flip-card-back">
                            <h2>King Freakbob XIV</h2>
                            <p>King Freakbob XIV war der weiseste König des Landes. Als ein dunkler Schatten das Königreich bedrohte, versammelte er mutige Ritter und Magier. Gemeinsam fanden sie die Quelle des Schattens und besiegten sie, wodurch das Licht ins Königreich zurückkehrte. King Freakbob wurde als Held gefeiert und lehrte sein Volk, dass wahre Stärke in der Gemeinschaft liegt.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card">
                    <div class="flip-card-inner">
                        <div class="flip-card-front">
                            <img src="/freakbob3.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                        </div>
                        <div class="flip-card-back">
                            <h2>Super Bob</h2>
                            <p>Super Bob, einst ein normaler Mensch, erhielt übernatürliche Kräfte durch ein geheimnisvolles Artefakt. Er kämpfte gegen den bösen Dr. Schatten und rettete die Welt vor Chaos. Mit Mut und Entschlossenheit half er den Menschen und wurde als Held gefeiert, bereit für neue Abenteuer.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <div>
            &copy; 2024 ImagineIT. Powered by LLAVA-PHI3.
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>