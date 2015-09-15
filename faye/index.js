var http = require('http'),
    faye = require('faye'); 

var Redis = require('ioredis');

var server = http.createServer(),
    bayeux = new faye.NodeAdapter({mount: '/', timeout: 45});
    
var fayeClient = bayeux.getClient();
    
var redis = new Redis();
redis.subscribe('chat', function (err, count) {
  if(err) {
    console.error(err);
  }
});

redis.on('message', function (channel, message) {
  console.log('Receive message %s from channel %s', message, channel);
  
  fayeClient.publish('/' + channel, JSON.parse(message));
  
});

bayeux.attach(server);
server.listen(8080);
