// A Node.js server
var server = require('http').Server();

// Requiring the ioredis package
var Redis = require('ioredis');
// A redis client
var redis = new Redis();
// Store people in chatroom
var chatters = [];

// Store messages in chatroom
var chat_messages = [];

 
// Subscribe to all channels which name complies with the '*' pattern
// '*' means we'll subscribe to ALL possible channels
redis.psubscribe('*');

// Listen for new messages
redis.on('pmessage', function (pattern, channel, message) {
    message = JSON.parse(message);

    // Just to check that things really work
    console.log(message);
});

// Start the server at http://localhost:3000
server.listen(4000);

// Just to be sure it's working
console.log('Server started');
