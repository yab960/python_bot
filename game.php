<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require __DIR__ . '/vendor/autoload.php';

class RandomNumberServer implements MessageComponentInterface {
    protected $clients;
    protected $loop;
    $this->bingoCards = $this->generateBingoCards(50);

    public function __construct($loop) {
        $this->clients = new \SplObjectStorage;
        $this->loop = $loop;
        echo "WebSocket server started.\n";

         $conn->send(json_encode($this->bingoCards));
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
            $random = rand(1, 75);
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
startGenerator
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

private function generateBingoCards($numCards = 50) {
    $cards = [];
    
    // Define the number ranges for each column
    $bingoRanges = [
        'B' => range(1, 15),
        'I' => range(16, 30),
        'N' => range(31, 45),
        'G' => range(46, 60),
        'O' => range(61, 75)
    ];

    // Generate each Bingo card
    for ($i = 0; $i < $numCards; $i++) {
        $card = generateSingleCard($bingoRanges);
        $cards[] = $card;
    }

    return $cards;
}

private function generateSingleCard($bingoRanges) {
    $card = [];
    
    // Generate the 5 columns for the Bingo card (B, I, N, G, O)
    foreach ($bingoRanges as $column => $range) {
        $numbers = array_splice($range, 0, 5); // Get 5 unique numbers from the range
        shuffle($numbers);  // Shuffle the numbers for randomness
        $card[$column] = $numbers;
    }

    // Set the middle space to "Free" (N column, 3rd row)
    $card['N'][2] = 'Free';
    
    return $card;
}

