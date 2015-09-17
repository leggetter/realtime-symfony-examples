// Log all Pusher JS info to console
// Pusher.log = function(msg) {
//   console.log(msg);
// };

var storage = window.localStorage || { setItem: function(){}, getItem: function() {}};

// Store Twitter ID entered by User.
var twitterUsername = storage.getItem('username');

function init() {
  if(twitterUsername) {
    setUpUser(twitterUsername);
  }
  
  // Twitter username button click and Enter press handler setup
  $('#try-it-out').click(setUpUser);
  $('#input-name').keyup(function(e) {
    if(e.keyCode === 13) {
      var username = $('#input-name').val();
      setUpUser(username);
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
  $.get('/chat-api/messages').success(function(messages) {
    messages.forEach(addMessage);
  });
}

/**
 * Handle user entered events. Ensure there's a value
 * and store for use later.
 *
 * Also hide Twitter username input and show messages.
 */
function setUpUser(username) {
  if (!username) {
    return;
  }

  twitterUsername = username;
  storage.setItem('username', twitterUsername);

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
  $.post('/chat-api/message', data).success(sendMessageSuccess);

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
