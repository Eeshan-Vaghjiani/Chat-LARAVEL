<!-- Chat Container -->
<div class="chat">
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

<!-- Styles -->
<style>
    .chat {
        margin: 0 auto;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .messages {
        padding: 1rem;
        height: calc(100vh - 400px);
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

<!-- Scripts -->
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
<script>
    const pusher = new Pusher('{{config('broadcasting.connections.pusher.key')}}', {
        cluster: 'eu'
    });
    const channel = pusher.subscribe('public');

    //Receive messages
    channel.bind('chat', function(data) {
        $.post('/receive', {
            _token: '{{csrf_token()}}',
            message: data.message,
            user: data.user
        })
         .done(function (res) {
            $('.messages > .message').last().after(res);
            scrollToBottom();
        });
    });

    //Broadcast messages
    $('form').submit(function(event) {
        event.preventDefault();

        $.post('/broadcast', {
            _token: '{{csrf_token()}}',
            message: $('form #message').val()
        })
         .done(function(res) {
            $('.messages > .message').last().after(res);
            $('form #message').val('');
            scrollToBottom();
        });
    });

    // Scroll to bottom on page load
    function scrollToBottom() {
        $('.messages').scrollTop($('.messages')[0].scrollHeight);
    }

    $(document).ready(function() {
        scrollToBottom();
    });
</script>
