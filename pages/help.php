<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Funny Memes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }
        .meme-container {
            margin: 20px 0;
        }
        img {
            max-width: 100%;
            height: auto;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="meme-container">
    <img id="meme" src="" alt="Funny Meme">
</div>
<button onclick="playSound()">Play Sound</button>
<audio id="sound" src=""></audio>

<script>
    const memes = <?php
        $memes = [
            ["src" => "../pictures/Freakbob.png", "sound" => "../audio/Furz geräusch ton _ furzsound.mp3"],
            ["src" => "../pictures/dc3.jpg", "sound" => "../audio/Hog rider sound.mp3"],
            // Add more memes and sounds here
        ];
        echo json_encode($memes);
        ?>;

    function getRandomMeme() {
        return memes[Math.floor(Math.random() * memes.length)];
    }

    function playSound() {
        const meme = getRandomMeme();
        const img = document.getElementById('meme');
        const audio = document.getElementById('sound');

        img.src = meme.src;
        audio.src = meme.sound;
        audio.play();
    }

    // Initialize with a random meme
    window.onload = playSound;
</script>
</body>
</html>