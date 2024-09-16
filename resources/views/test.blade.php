<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Pusher</title>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@7"></script> <!-- Charger Pusher JS -->
</head>
<body>
    <h1>Pusher Test</h1>
    <p>Check your browser's console to see if the event was received.</p>

    <script>
        // Exemple avec Pusher JS client
        const pusher = new Pusher('PUSHER_APP_KEY', {
            cluster: 'eu',
            encrypted: true
        });

        const channel = pusher.subscribe('test-channel');
        channel.bind('test-event', function(data) {
            console.log('Received event:', data.message);
        });
    </script>
</body>
</html>
