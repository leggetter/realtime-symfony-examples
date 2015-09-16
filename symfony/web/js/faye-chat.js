$LAB.script("http://localhost:8080/faye/client.js").wait(fayeInit);

function fayeInit() {
  // Faye
  var client = new Faye.Client('http://localhost:8080/');
  client.subscribe('/chat', function(payload) {
    if(payload.event === 'new-message') {
      addMessage(payload.data);
    }
  });

  var Logger = {
    incoming: function(message, callback) {
      console.log('incoming', message);
      callback(message);
    },
    outgoing: function(message, callback) {
      console.log('outgoing', message);
      callback(message);
    }
  };

  client.addExtension(Logger);
}
