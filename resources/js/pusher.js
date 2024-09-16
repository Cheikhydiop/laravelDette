// public/js/pusher.js
import Pusher from 'pusher-js';

const pusher = new Pusher('PUSHER_APP_KEY', {
    cluster: 'eu',
    encrypted: true
});

const channel = pusher.subscribe('test-channel');
channel.bind('test-event', function(data) {
    console.log('Received event:', data.message);
});
