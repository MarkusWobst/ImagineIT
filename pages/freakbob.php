<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein ChatGPT</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('./pictures/Freakbob.png') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 80%;
            max-width: 600px;
        }
        .container h1 {
            margin-bottom: 20px;
        }
        .new-chat-btn {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .new-chat-btn:hover {
            background-color: #0056b3;
        }
        .chat-container {
            display: none;
            flex-direction: column;
            align-items: center;
        }
        .chat-box {
            border: 1px solid #ddd;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
            margin-bottom: 10px;
            width: 100%;
        }
        .input-box {
            display: flex;
            width: 100%;
        }
        .input-box input[type="text"] {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
        }
        .input-box button {
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }
        .input-box button:hover {
            background-color: #0056b3;
        }
        .input-box input[type="file"] {
            display: none;
        }
        .input-box label {
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            margin-left: 5px;
        }
        .input-box label:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="pre-chat">
            <h1>Willkommen zu meinem ChatGPT</h1>
            <button class="new-chat-btn" onclick="startChat()">Neuer Chat</button>
        </div>
        <div class="chat-container" id="chat-container">
            <div class="chat-box" id="chat-box">
                <!-- Chat messages will appear here -->
            </div>
            <div class="input-box">
                <input type="text" id="user-input" placeholder="Schreibe eine Nachricht...">
                <button onclick="sendMessage()">Senden</button>
                <input type="file" id="file-input" accept="image/*" onchange="uploadImage(event)">
                <label for="file-input">Bild hochladen</label>
            </div>
        </div>
    </div>

    <script>
        function startChat() {
            document.querySelector('.pre-chat').style.display = 'none';
            document.getElementById('chat-container').style.display = 'flex';
        }

        function sendMessage() {
            var userInput = document.getElementById('user-input').value;
            var chatBox = document.getElementById('chat-box');

            if (userInput.trim() !== "") {
                var userMessage = document.createElement('div');
                userMessage.textContent = "Du: " + userInput;
                chatBox.appendChild(userMessage);

                // Clear the input field
                document.getElementById('user-input').value = "";

                // Scroll to the bottom of the chat box
                chatBox.scrollTop = chatBox.scrollHeight;

                // Here you would typically send the message to your backend or AI model
                // and append the response to the chat box
            }
        }

        function uploadImage(event) {
            var file = event.target.files[0];
            var chatBox = document.getElementById('chat-box');

            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100%';
                    img.style.marginTop = '10px';
                    chatBox.appendChild(img);

                    // Scroll to the bottom of the chat box
                    chatBox.scrollTop = chatBox.scrollHeight;

                    // Here you would typically send the image to your backend or AI model
                    // and append the response to the chat box
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>