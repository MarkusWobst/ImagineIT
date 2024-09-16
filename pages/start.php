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
            border-radius: 10px;
            margin-bottom: 20px;
            perspective: 1000px;
            height: 100%;
            visibility: ;
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
        .flip-card-front, .flip-card-back {
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
            max-width: 100%;
            border-radius: 10px;
        }
        .flip-card-back {
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            transform: rotateY(180deg);
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
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
    <a href="login.php" class="btn btn-light">Anmelden</a>
    <a href="register.php" class="btn btn-light">Registrieren</a>
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
                        <img src="../pictures/freakbob%20ritter.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                    </div>
                    <div class="flip-card-back">
                        <h2>Ritter Freakbob</h2>
                        <p>Ritter Freakbob und das Geheimnis des Verzauberten Waldes

                            Es war einmal ein ungewöhnlicher Ritter namens Freakbob. Er war nicht wie die anderen Ritter, denn er trug eine Rüstung aus bunten Federn und hatte einen Helm, der wie ein riesiger Fisch aussah. Trotz seines seltsamen Aussehens war Freakbob ein mutiger und edler Ritter, der immer bereit war, den Schwachen zu helfen.

                            Eines Tages hörte Freakbob von einem verzauberten Wald, in dem ein mächtiger Drache lebte. Der Drache hatte das Dorf in der Nähe des Waldes terrorisiert und die Dorfbewohner lebten in ständiger Angst. Ritter Freakbob beschloss, dem Drachen entgegenzutreten und das Dorf zu retten.

                            Mit seinem treuen Pferd, das er liebevoll “Blitz” nannte, ritt Freakbob in den Wald. Die Bäume flüsterten geheimnisvolle Worte und die Luft war erfüllt von Magie. Plötzlich stand der Drache vor ihm, seine Schuppen glänzten in allen Farben des Regenbogens.

                            “Wer wagt es, meinen Wald zu betreten?” brüllte der Drache.

                            “Ich bin Ritter Freakbob, und ich werde dich besiegen, um das Dorf zu retten!” rief Freakbob mutig.

                            Der Drache lachte. “Du bist mutig, kleiner Ritter. Aber Mut allein wird nicht ausreichen.”

                            Freakbob zog sein Schwert, das ebenfalls in bunten Farben schimmerte, und stürzte sich auf den Drachen. Der Kampf war lang und hart, aber Freakbob gab nicht auf. Schließlich gelang es ihm, den Drachen mit einem geschickten Hieb zu besiegen.

                            Der Drache verwandelte sich in eine wunderschöne Fee. “Danke, tapferer Ritter,” sagte sie. “Ich war einst eine Fee, die von einem bösen Zauberer in einen Drachen verwandelt wurde. Durch deinen Mut hast du mich befreit.”

                            Die Fee belohnte Freakbob mit einem magischen Amulett, das ihm in zukünftigen Abenteuern helfen würde. Das Dorf war gerettet, und Ritter Freakbob wurde als Held gefeiert.

                            Und so lebte Ritter Freakbob glücklich und bereit für neue Abenteuer.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <img src="../pictures/king%20bob.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                    </div>
                    <div class="flip-card-back">
                        <h2>King Freakbob XIV</h2>
                        <p>King Freakbob XIV war der weiseste und mächtigste König des Landes...</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card">
                <div class="flip-card-inner">
                    <div class="flip-card-front">
                        <img src="../pictures/freakbob3.jfif" alt="Ein beschreibendes Bild für Tolle Geschichte">
                    </div>
                    <div class="flip-card-back">
                        <h2>Super Bob</h2>
                        <p>Super Bob rettet die Welt mit seinen unglaublichen Kräften...</p>
                    </div>
                </div>
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