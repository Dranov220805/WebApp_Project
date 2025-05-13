// filepath: bin/note_editor_server.php (or your chosen location)
<?php
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require dirname(__DIR__) . '/vendor/autoload.php';

class NoteEditor implements MessageComponentInterface {
    protected $clients;
    private $noteSubscribers; // [noteId => [connectionId => connection, ...], ...]

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->noteSubscribers = [];
        echo "WebSocket Server Started...\n";
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n",
            $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        $data = json_decode($msg, true);

        if (isset($data['type'])) {
            switch ($data['type']) {
                case 'subscribe':
                    $noteId = $data['noteId'];
                    if (!isset($this->noteSubscribers[$noteId])) {
                        $this->noteSubscribers[$noteId] = new \SplObjectStorage;
                    }
                    $this->noteSubscribers[$noteId]->attach($from);
                    echo "Connection {$from->resourceId} subscribed to note {$noteId}\n";
                    break;

                case 'edit':
                    $noteId = $data['noteId'];
                    $content = $data['content'];
                    $editorId = $from->resourceId; // Identify who made the edit

                    // Broadcast to other subscribers of the same note
                    if (isset($this->noteSubscribers[$noteId])) {
                        foreach ($this->noteSubscribers[$noteId] as $client) {
                            // Don't send back to the originator if you don't want to
                            // Or send with an 'isOriginator' flag if client needs to know
                            if ($from !== $client) {
                                $client->send(json_encode([
                                    'type' => 'update',
                                    'noteId' => $noteId,
                                    'content' => $content,
                                    'editorId' => $editorId // So client can know who edited
                                ]));
                            }
                        }
                    }
                    // Here you might also trigger a database save, perhaps debounced
                    // For simplicity, this example only broadcasts.
                    // $this->saveToDatabase($noteId, $content);
                    break;
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        // Remove from any note subscriptions
        foreach ($this->noteSubscribers as $noteId => $subscribers) {
            if ($subscribers->contains($conn)) {
                $subscribers->detach($conn);
                echo "Connection {$conn->resourceId} unsubscribed from note {$noteId}\n";
                if (count($subscribers) == 0) {
                    unset($this->noteSubscribers[$noteId]);
                }
            }
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    // Optional: Implement database saving logic
    // private function saveToDatabase($noteId, $content) {
    //     // Your database update logic here
    //     // This could be a direct DB call or an API call to your existing backend
    //     echo "Attempting to save note {$noteId} to DB.\n";
    // }
}

// Run the server application through the WebSocket protocol on port 8080
$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new NoteEditor()
        )
    ),
    8080 // Choose a port
);

$server->run();