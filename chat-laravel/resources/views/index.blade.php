<!DOCTYPE html>
<html lang="en">
<head>
    <title>Chat Laravel | {{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- JavaScript -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <!-- End JavaScript -->

    <!-- CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        body {
            overflow: hidden;
        }

        .chat {
            max-width: 1200px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            position: fixed;
            top: 80px;
            left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 40px);
            height: calc(100vh - 180px);
        }

        .top {
            background: #4F46E5;
            color: white;
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1;
        }

        .top img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .messages {
            padding: 1rem;
            height: calc(100% - 140px);
            margin-top: 70px;
            overflow-y: auto;
            background: #F3F4F6;
        }

        .message {
            margin-bottom: 1rem;
            max-width: 80%;
            clear: both;
        }

        .message.sent {
            float: right;
        }

        .message.received {
            float: left;
        }

        .message-content {
            padding: 0.75rem 1rem;
            border-radius: 15px;
            position: relative;
            display: inline-block;
        }

        .sent .message-content {
            background: #4F46E5;
            color: white;
            border-bottom-right-radius: 5px;
        }

        .received .message-content {
            background: white;
            color: #1F2937;
            border-bottom-left-radius: 5px;
        }

        .message-meta {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            color: #6B7280;
        }

        .sent .message-meta {
            text-align: right;
        }

        .bottom {
            padding: 1rem;
            background: white;
            border-top: 1px solid #E5E7EB;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }

        .bottom form {
            display: flex;
            gap: 1rem;
        }

        .bottom input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #E5E7EB;
            border-radius: 9999px;
            outline: none;
            transition: border-color 0.2s;
        }

        .bottom input:focus {
            border-color: #4F46E5;
        }

        .bottom button {
            background: #4F46E5;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .bottom button:hover {
            background: #4338CA;
        }

        .user-avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 8px;
        }
    </style>
</head>

<body class="font-sans antialiased">
    <x-banner />

    <div class="min-h-screen bg-gray-100">
        @livewire('navigation-menu')

        <div class="chat">
            <!-- Header -->
            <div class="top">
                <img src="{{ Auth::user()->profile_photo_url }}" alt="Avatar">
                <div>
                    <p class="font-semibold">{{ Auth::user()->name }}</p>
                    <small>Online</small>
                </div>
            </div>
            <!-- End Header -->

            <!-- Chat -->
            <div class="messages">
                @if(isset($messages) && $messages->count() > 0)
                    @foreach($messages as $message)
                        @if($message->user_id === Auth::id())
                            <div class="message sent">
                                <div class="message-content">
                                    {{ $message->message }}
                                </div>
                                <div class="message-meta">
                                    {{ $message->user->name }} • {{ $message->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @else
                            <div class="message received">
                                <div class="message-content">
                                    <img src="{{ $message->user->profile_photo_url }}" alt="Avatar" class="user-avatar">
                                    {{ $message->message }}
                                </div>
                                <div class="message-meta">
                                    {{ $message->user->name }} • {{ $message->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="message received">
                        <div class="message-content">
                            Welcome to the chat! 👋
                        </div>
                    </div>
                @endif
            </div>
            <!-- End Chat -->

            <!-- Footer -->
            <div class="bottom">
                <form>
                    <input type="text" id="message" name="message" placeholder="Type your message..." autocomplete="off">
                    <button type="submit">Send</button>
                </form>
            </div>
            <!-- End Footer -->
        </div>
    </div>

    @stack('modals')
    @livewireScripts

    <script>
        const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
            cluster: 'eu'
        });
        const channel = pusher.subscribe('public');

        // Scroll to bottom on load
        $(document).ready(function() {
            scrollToBottom();
        });

        // Scroll to bottom function
        function scrollToBottom() {
            const messages = $('.messages');
            messages.scrollTop(messages[0].scrollHeight);
        }

        // Receive messages
        channel.bind('chat', function(data) {
            $.post("/receive", {
                _token: '{{csrf_token()}}',
                message: data.message,
                user: data.user
            })
            .done(function(res) {
                $(".messages").append(res);
                scrollToBottom();
            });
        });

        // Broadcast messages
        $("form").submit(function(event) {
            event.preventDefault();

            const messageInput = $("form #message");
            const message = messageInput.val();

            if (!message) return;

            $.ajax({
                url: "/broadcast",
                method: 'POST',
                headers: {
                    'X-Socket-Id': pusher.connection.socket_id
                },
                data: {
                    _token: '{{csrf_token()}}',
                    message: message
                }
            }).done(function(res) {
                $(".messages").append(res);
                messageInput.val('');
                scrollToBottom();
            });
        });
    </script>
</body>
</html>
