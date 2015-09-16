$LAB.script("//js.pusher.com/3.0/pusher.min.js").wait(pusherInit);

function pusherInit() {
  Pusher.log = function(msg) {
    console.log(msg);
  };

  // Pusher
  var pusher = new Pusher('0cb24b6b414cc36a6ae6');

  var channel = pusher.subscribe('chat');
  channel.bind('new-message', addMessage);

}
