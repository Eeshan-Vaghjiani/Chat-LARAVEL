<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div id="chat-messages" class="chat-messages">
                    @include('chat-content')
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function fetchMessages() {
            $.ajax({
                url: "{{ route('fetch.messages') }}",
                method: 'GET',
                success: function(data) {
                    $('#chat-messages').empty();

                    data.forEach(function(message) {
                        $('#chat-messages').append(`
                            <div class="message">
                                <p>${message.content}</p>
                                <span class="timestamp">${new Date(message.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                            </div>
                        `);
                    });
                },
                error: function(xhr) {
                    console.error('Error fetching messages:', xhr);
                }
            });
        }

        setInterval(fetchMessages, 5000);
    </script>
</x-app-layout>
