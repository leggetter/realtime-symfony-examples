<?php

namespace ChatApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

use React\EventLoop\StreamSelectLoop;
use Predis\Async\Client;

class Chat implements MessageComponentInterface
{
    private $clients;
    
    private $streamLoop = null;
    private $redisClient = null;
    
    public function __construct()
    {
        $this->wsclients = new \SplObjectStorage();
    
        $this->streamLoop = new StreamSelectLoop();
        $this->redisClient = new Client('tcp://127.0.0.1:6379', $this->streamLoop);
        
        $this->redisClient->connect(function ($client) {
          echo "Connected to Redis, now listening for incoming messages...\n";

          $client->pubSubLoop('nrk:channel', function ($event) {
            echo "Received message `{$event->payload}` from {$event->channel}.\n";
          });
          
        });

        echo 'Ratchet Chat server running';
        
        $this->streamLoop->run();
    }
    
    public function onOpen(ConnectionInterface $conn)
    {
        // Store the new connection to send messages to later
        $this->wsclients->attach($conn);

        echo "New connection! ({$conn->resourceId})\n";
    }
    
    public function onMessage(ConnectionInterface $from, $msg)
    {
        // we don't want to do anything with incoming messages
    }
    
    public function onClose(ConnectionInterface $conn)
    {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->wsclients->detach($conn);

        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        trigger_error("An error has occurred: {$e->getMessage()}\n", E_USER_WARNING);
        $conn->close();
    }
}
