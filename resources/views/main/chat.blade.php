<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
</head>
<body>
    <div id="chat-box">
        @foreach($chatMessages as $message)
            <p>
                <strong>{{ $message->user->name }}:</strong> {{ $message->message }}
            </p>
        @endforeach
    </div>

    <form action="{{ route('chat.send') }}" method="post">
        @csrf
        <input type="text" name="message" placeholder="Ketik pesan...">
        <button type="submit">Kirim</button>
    </form>
</body>
</html>
