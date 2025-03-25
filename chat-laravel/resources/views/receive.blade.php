<div class="message received">
    <div class="message-content">
        <img src="{{ $user->profile_photo_url }}" alt="Avatar" class="user-avatar">
        {{ $message }}
    </div>
    <div class="message-meta">
        {{ $user->name }} • Just now
    </div>
</div>
