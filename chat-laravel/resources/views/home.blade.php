<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Application</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}"> <!-- Link to your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        header {
            background: #007bff;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        .content {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .info {
            flex: 1;
            margin-right: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
            max-height: 400px; /* Set a max height for scrolling */
        }
        .actions {
            flex: 0 0 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .btn {
            background: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-align: center;
            margin-bottom: 10px;
            text-decoration: none;
        }
        .btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to the Chat Application</h1>
        <p>Connect with friends and family in real-time!</p>
    </header>
    <div class="container">
        <div class="content">
            <div class="info">
                <h2>About Our Chat</h2>
                <p>Our chat application allows you to communicate with others seamlessly. Enjoy features like:</p>
                <ul>
                    <li>Real-time messaging</li>
                    <li>Group chats</li>
                    <li>File sharing</li>
                    <li>Custom emojis and reactions</li>
                    <li>Secure and private conversations</li>
                </ul>
                <p>Join us today and start chatting!</p>
            </div>
            <div class="actions">
                <a href="{{ route('login') }}" class="btn">Login</a>
                <a href="{{ route('register') }}" class="btn">Register</a>
            </div>
        </div>
    </div>
</body>
</html>
