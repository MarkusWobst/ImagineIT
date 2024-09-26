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

        .about-section {
            margin-top: 150px; /* Adjust the margin to push the section down */
            padding-top: 50px; /* Add some padding at the top */
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
            <div class="col-12 text-center mb-4">
                <p>Beispiele für AI-generierte Geschichten:</p>
            </div>
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

        <div class="row about-section">
            <div class="col-12">
                <h2>Über ImagineIT</h2>
                <p>ImagineIT ist eine innovative Plattform, die künstliche Intelligenz nutzt, um Bilder in faszinierende Geschichten zu verwandeln. Unsere Mission ist es, Ihre Kreativität zu fördern und Ihnen zu helfen, Ihre Ideen zum Leben zu erwecken. Mit ImagineIT können Sie:</p>
                <ul>
                    <li>AI-generierte Bilder in spannende Geschichten umwandeln</li>
                    <li>Eigene Geschichten mit passenden Bildern erstellen</li>
                    <li>Ihre Kreationen mit der Community teilen</li>
                    <li>Inspiration aus den Geschichten anderer Nutzer schöpfen</li>
                </ul>
                <p>Unsere Plattform ist benutzerfreundlich und bietet eine Vielzahl von Tools, um Ihre kreativen Projekte zu unterstützen. Egal, ob Sie ein erfahrener Geschichtenerzähler oder ein Anfänger sind, ImagineIT bietet Ihnen die Ressourcen, die Sie benötigen, um Ihre Visionen zu verwirklichen.</p>
                <p>ImagineIT bietet eine einzigartige Möglichkeit, Ihre kreativen Fähigkeiten zu erweitern und Ihre Geschichten auf eine neue, visuell ansprechende Weise zu präsentieren. Unsere AI-Technologie analysiert Ihre Bilder und generiert automatisch spannende und passende Geschichten, die Ihre Bilder zum Leben erwecken. Sie können auch Ihre eigenen Geschichten schreiben und passende Bilder aus unserer umfangreichen Bibliothek auswählen.</p>
                <p>Unsere Community ist ein integraler Bestandteil von ImagineIT. Teilen Sie Ihre Kreationen mit anderen Nutzern, erhalten Sie Feedback und lassen Sie sich von den Geschichten und Bildern anderer inspirieren. Unsere Plattform fördert den Austausch von Ideen und die Zusammenarbeit, um gemeinsam großartige Geschichten zu schaffen.</p>
                <p>Registrieren Sie sich noch heute und beginnen Sie Ihre Reise mit ImagineIT! Entdecken Sie die unendlichen Möglichkeiten, die unsere Plattform bietet, und lassen Sie Ihrer Kreativität freien Lauf. Wir freuen uns darauf, Ihre Geschichten zu sehen und gemeinsam mit Ihnen die Welt der Bilder und Geschichten zu erkunden.</p>
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