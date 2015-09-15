// Log all Pusher JS info to console
// Pusher.log = function(msg) {
//   console.log(msg);
// };

// Store Twitter ID entered by User.
var twitterUsername = null;

function init() {
  // Twitter username button click and Enter press handler setup
  $('#try-it-out').click(setUpUser);
  $('#input-name').keyup(function(e) {
    if(e.keyCode === 13) {
      setUpUser();
    }
  });

  // send button click and Enter keypress handling
  $('.send-message').click(sendMessage);
  $('.input-message').keypress(checkSend);
  
  fetchInitialMessages();
}

/**
 * Get all existing messages.
 */
function fetchInitialMessages() {
  $.get('/chat/messages').success(function(messages) {
    messages.forEach(addMessage);
  });
}

/**
 * Handle user entered events. Ensure there's a value
 * and store for use later.
 *
 * Also hide Twitter username input and show messages.
 */
function setUpUser() {
  var username = $('#input-name').val();
  if (!username) {
    return;
  }

  twitterUsername = username;

  $('.twitter-username-capture').slideUp(function() {
    $('.chat-app').fadeIn();
    scrollMessagesToBottom();
  });
}

/**
 * Check to see if the Enter key has been pressed to
 * send a message.
 */
function checkSend(e) {
  if (e.keyCode === 13) {
    return sendMessage();
  }
}

/**
 * Check to ensure a 3 char message has been input.
 * If so, send the message to the server.
 */
function sendMessage() {
  var messageText = $('.input-message').val();
  if (messageText.length < 3) {
    return false;
  }

  // Build POST data and make AJAX request
  var data = {
    username: twitterUsername,
    chat_text: messageText
  };
  $.post('/chat/message', data).success(sendMessageSuccess);

  // Ensure the normal browser event doesn't take place
  return false;
}

/**
 * Handle the message post success callback
 */
function sendMessageSuccess() {
  $('.input-message').val('')
  console.log('message sent successfully');
}

/**
 * Build the UI for a new message and add to the DOM
 */
function addMessage(data) {
  // Create element from template and set values
  var el = createMessageEl();
  el.find('.message-body').html(data.text);
  el.find('.author').text(data.username);
  el.find('.avatar img').attr('src', 'https://twitter.com/' + data.username + '/profile_image?size=original')

  // Utility to build nicely formatted time
  el.find('.timestamp').text(strftime('%H:%M:%S %P', new Date(data.timestamp.date)));

  var messages = $('#messages');
  messages.append(el)

  scrollMessagesToBottom();
}

/**
 * Make sure the incoming message is shown.
 */
function scrollMessagesToBottom() {
  var messages = $('#messages');
  messages.scrollTop(messages[0].scrollHeight);
}

/**
 * Creates a chat message element from the template
 */
function createMessageEl() {
  var text = $('#chat_message_template').text();
  var el = $(text);
  return el;
}

$(init);

/***********************************************/

// Pusher
// var pusher = new Pusher('0cb24b6b414cc36a6ae6');
// 
// var channel = pusher.subscribe('chat');
// channel.bind('new-message', addMessage);

// Ratchet
// var conn = new WebSocket('ws://localhost:8080');
// conn.onopen = function(e) {
//     console.log("Connection established!");
// };
// 
// conn.onmessage = function(e) {
//   var payload = JSON.parse(e.data);
//   console.log(payload);
//   
//   if(payload.event === 'new-message') {
//     addMessage(payload.data)
//   }
// };

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
