<?php

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use ChatApp\Chat;

require dirname(__DIR__).'/vendor/autoload.php';

$chat = new Chat();
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            $chat
        )
    ),
    8080
);

$redis = new Predis\Async\Client('tcp://127.0.0.1:6379', $server->loop);
$redis->connect(function($redis) use($chat) {
  $chat->init($redis);
});

$server->run();
