<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/vendor/autoload.php';

class RandomNumberServer implements MessageComponentInterface {
    protected $clients;
    protected $loop;

    public function __construct($loop) {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        echo "WebSocket server started.\n";

        $this->startGenerator();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Optional: handle messages from clients
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} closed.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    private function startGenerator() {
        $this->loop->addPeriodicTimer(1, function () {
            $random = rand(1, 1000);
            $msg = "[" . date('H:i:s') . "] Random: $random\n";
            echo $msg;

            foreach ($this->clients as $client) {
                $client->send($msg);
            }
        });
    }
}

// Use ReactPHP event loop
$loop = React\EventLoop\Factory::create();

// Get port from environment or default 8080
$port = getenv('PORT') ?: 8080;

$webSock = new React\Socket\SocketServer("0.0.0.0:$port", [], $loop);

$server = new Ratchet\Server\IoServer(
    new Ratchet\Http\HttpServer(
        new Ratchet\WebSocket\WsServer(
            new RandomNumberServer($loop)
        )
    ),
    $webSock,
    $loop
);

$server->run();
