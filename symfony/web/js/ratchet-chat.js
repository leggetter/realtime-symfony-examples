// Ratchet
var conn = new WebSocket('ws://localhost:8080');
conn.onopen = function(e) {
  console.log("Connection established!");
};

conn.onmessage = function(e) {
  var payload = JSON.parse(e.data);
  console.log(payload);
  
  if(payload.event === 'new-message') {
    addMessage(payload.data)
  }
};
