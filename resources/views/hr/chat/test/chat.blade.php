<!DOCTYPE html>
<html>
<head>
    <title>Laravel Pusher Chat</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
</head>
<body>
<div style="max-width:600px;margin:20px auto;">
    <h2>Simple Chat (No npm)</h2>

    <div id="messages" style="border:1px solid #ccc; padding:10px; height:300px; overflow-y:scroll;"></div>

    <input type="text" id="username" placeholder="Name" style="width:100%; margin-top:10px;">
    <input type="text" id="message" placeholder="Message" style="width:100%; margin-top:5px;">
    <button id="send" style="margin-top:5px;">Send</button>
</div>

<script>
    // اتصال به Pusher
    Pusher.logToConsole = true;
    var pusher = new Pusher("{{ env('PUSHER_APP_KEY') }}", {
        cluster: "{{ env('PUSHER_APP_CLUSTER') }}",
        forceTLS: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        }
    });

    var channel = pusher.subscribe('private-test_chat');
    channel.bind('App\\Events\\TestMessageSent', function(data) {alert(data.username)
        let messages = document.getElementById('messages');
        messages.innerHTML += `<p><strong>${data.username}:</strong> ${data.message}</p>`;
        messages.scrollTop = messages.scrollHeight;
    });

    // ارسال پیام با fetch
    document.getElementById('send').addEventListener('click', function () {
        fetch('/aie/slja/ie/ij/h/als/jk/hr/test_chat/send-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                username: document.getElementById('username').value,
                message: document.getElementById('message').value
            })
        });
    });
</script>
</body>
</html>
