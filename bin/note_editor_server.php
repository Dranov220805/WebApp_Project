<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

require dirname(__DIR__) . '/vendor/autoload.php';

class NoteEditor implements MessageComponentInterface
{
    protected $clients;
    private $noteSubscribers = [];  // [noteId => [connId => [conn, userEmail], ...], ...]
    private $userConnections = [];  // [userEmail => [connId => conn, ...], ...]
    private ?mysqli $conn;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage;

        // DB connection settings - adjust as needed
        $host = 'mysql';
        $username = 'user';
        $password = 'userpass';
        $database = 'note_manager';

        try {
            $this->conn = new mysqli($host, $username, $password, $database);
            if ($this->conn->connect_error) {
                throw new Exception("Database connection failed: " . $this->conn->connect_error);
            }
            echo "Database connected successfully.\n";
        } catch (Exception $e) {
            echo "Database connection error: " . $e->getMessage() . "\n";
            $this->conn = null;
        }

        echo "WebSocket Server Started on port 8082...\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!isset($data['type'])) {
            echo "Message missing 'type', ignoring: " . $msg . "\n";
            return;
        }

        $connId = $from->resourceId;

        switch ($data['type']) {
            case 'subscribe':
                $this->handleSubscribe($from, $data);
                break;

            case 'unsubscribe':
                $this->handleUnsubscribe($from, $data);
                break;

            case 'edit':
                $this->handleEdit($from, $data);
                break;

            case 'typing_indicator':
                $this->handleTypingIndicator($from, $data);
                break;

            default:
                echo "Unknown message type: {$data['type']}\n";
        }
    }

    private function handleSubscribe(ConnectionInterface $conn, array $data)
    {
        $noteId = $data['noteId'] ?? null;
        $userEmail = $data['userEmail'] ?? 'anonymous';
        $connId = $conn->resourceId;

        if (!$noteId) {
            echo "Subscribe missing noteId, ignoring\n";
            return;
        }

        // Initialize arrays if needed
        if (!isset($this->noteSubscribers[$noteId])) {
            $this->noteSubscribers[$noteId] = [];
        }

        if (!isset($this->userConnections[$userEmail])) {
            $this->userConnections[$userEmail] = [];
        }

        // Store connection
        $this->noteSubscribers[$noteId][$connId] = [
            'conn' => $conn,
            'userEmail' => $userEmail
        ];

        $this->userConnections[$userEmail][$connId] = $conn;

        echo "Connection {$connId} ({$userEmail}) subscribed to note {$noteId}\n";

        // Notify others that a new user joined
        foreach ($this->noteSubscribers[$noteId] as $clientId => $clientData) {
            if ($clientId !== $connId) {
                $clientData['conn']->send(json_encode([
                    'type' => 'user_joined',
                    'noteId' => $noteId,
                    'userEmail' => $userEmail
                ]));
            }
        }
    }

    private function handleUnsubscribe(ConnectionInterface $conn, array $data)
    {
        $noteId = $data['noteId'] ?? null;
        $userEmail = $data['userEmail'] ?? 'anonymous';
        $connId = $conn->resourceId;

        if (!$noteId) {
            echo "Unsubscribe missing noteId, ignoring\n";
            return;
        }

        if (isset($this->noteSubscribers[$noteId][$connId])) {
            // Remove connection
            unset($this->noteSubscribers[$noteId][$connId]);

            // Notify others that user left
            foreach ($this->noteSubscribers[$noteId] as $clientData) {
                $clientData['conn']->send(json_encode([
                    'type' => 'user_left',
                    'noteId' => $noteId,
                    'userEmail' => $userEmail
                ]));
            }

            // Clean up empty arrays
            if (empty($this->noteSubscribers[$noteId])) {
                unset($this->noteSubscribers[$noteId]);
            }

            echo "Connection {$connId} ({$userEmail}) unsubscribed from note {$noteId}\n";
        }

        // Remove from user connections
        if (isset($this->userConnections[$userEmail][$connId])) {
            unset($this->userConnections[$userEmail][$connId]);
            if (empty($this->userConnections[$userEmail])) {
                unset($this->userConnections[$userEmail]);
            }
        }
    }

    private function handleEdit(ConnectionInterface $from, array $data)
    {
        $noteId = $data['noteId'] ?? null;
        $title = $data['title'] ?? '';
        $content = $data['content'] ?? '';
        $editorEmail = $data['editorEmail'] ?? 'anonymous';
        $connId = $from->resourceId;

        if (!$noteId) {
            echo "Edit missing noteId, ignoring\n";
            return;
        }

        echo "Received edit for note {$noteId} from {$editorEmail}\n";

        // Save to database (debounced on server side)
        $this->debounceSaveToDatabase($noteId, $title, $content);

        // Broadcast to all other subscribers
        if (isset($this->noteSubscribers[$noteId])) {
            foreach ($this->noteSubscribers[$noteId] as $clientId => $clientData) {
                if ($clientId !== $connId) {
                    $clientData['conn']->send(json_encode([
                        'type' => 'update',
                        'noteId' => $noteId,
                        'title' => $title,
                        'content' => $content,
                        'editorEmail' => $editorEmail,
                        'timestamp' => time()
                    ]));
                }
            }

            echo "Broadcasted edit to " . (count($this->noteSubscribers[$noteId]) - 1) . " subscribers\n";
        }
    }

    private function handleTypingIndicator(ConnectionInterface $from, array $data)
    {
        $noteId = $data['noteId'] ?? null;
        $userEmail = $data['userEmail'] ?? 'anonymous';
        $isTyping = $data['isTyping'] ?? false;
        $connId = $from->resourceId;

        if (!$noteId) {
            echo "Typing indicator missing noteId, ignoring\n";
            return;
        }

        // Broadcast typing indicator to other subscribers
        if (isset($this->noteSubscribers[$noteId])) {
            foreach ($this->noteSubscribers[$noteId] as $clientId => $clientData) {
                if ($clientId !== $connId) {
                    $clientData['conn']->send(json_encode([
                        'type' => 'typing_indicator',
                        'noteId' => $noteId,
                        'userEmail' => $userEmail,
                        'isTyping' => $isTyping
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        $connId = $conn->resourceId;

        // Find and remove from all note subscriptions
        foreach ($this->noteSubscribers as $noteId => $subscribers) {
            if (isset($subscribers[$connId])) {
                $userEmail = $subscribers[$connId]['userEmail'];

                // Notify others that user left
                foreach ($subscribers as $clientId => $clientData) {
                    if ($clientId !== $connId) {
                        $clientData['conn']->send(json_encode([
                            'type' => 'user_left',
                            'noteId' => $noteId,
                            'userEmail' => $userEmail
                        ]));
                    }
                }

                // Remove subscription
                unset($this->noteSubscribers[$noteId][$connId]);

                // Clean up empty arrays
                if (empty($this->noteSubscribers[$noteId])) {
                    unset($this->noteSubscribers[$noteId]);
                }

                // Remove from user connections
                if (isset($this->userConnections[$userEmail][$connId])) {
                    unset($this->userConnections[$userEmail][$connId]);
                    if (empty($this->userConnections[$userEmail])) {
                        unset($this->userConnections[$userEmail]);
                    }
                }

                echo "Connection {$connId} ({$userEmail}) unsubscribed from note {$noteId} due to disconnect\n";
            }
        }

        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    // Save changes to database with debouncing
    private $saveQueue = [];
    private $saveTimers = [];

    private function debounceSaveToDatabase($noteId, $title, $content)
    {
        // Store the latest content
        $this->saveQueue[$noteId] = [
            'title' => $title,
            'content' => $content,
            'time' => time()
        ];

        // Clear existing timer
        if (isset($this->saveTimers[$noteId])) {
            clearTimeout($this->saveTimers[$noteId]);
        }

        // Set timer to save after delay
        $this->saveTimers[$noteId] = setTimeout(function () use ($noteId) {
            if (isset($this->saveQueue[$noteId])) {
                $data = $this->saveQueue[$noteId];
                $this->saveToDatabase($noteId, $data['title'], $data['content']);
                unset($this->saveQueue[$noteId]);
            }
        }, 2000); // 2 second debounce
    }

    private function saveToDatabase($noteId, $title, $content)
    {
        if (!$this->conn) {
            echo "Cannot save to database: No connection\n";
            return;
        }

        try {
            $stmt = $this->conn->prepare("UPDATE Note SET title = ?, content = ? WHERE noteId = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            $stmt->bind_param("sss", $title, $content, $noteId);
            $result = $stmt->execute();

            if ($result) {
                echo "Note {$noteId} saved to database\n";
            } else {
                echo "Failed to save note {$noteId}: " . $stmt->error . "\n";
            }

            $stmt->close();
        } catch (Exception $e) {
            echo "Database error: " . $e->getMessage() . "\n";
        }
    }
}

// Create timeout function for PHP
function setTimeout($callback, $milliseconds)
{
    $timerid = uniqid();
    global $timers;
    $timers[$timerid] = [
        'callback' => $callback,
        'ms' => $milliseconds,
        'start' => microtime(true)
    ];
    return $timerid;
}

function clearTimeout($timerid)
{
    global $timers;
    if (isset($timers[$timerid])) {
        unset($timers[$timerid]);
        return true;
    }
    return false;
}

// Global timers array
$timers = [];

// Start the WebSocket server
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new NoteEditor()
        )
    ),
    8082 // Use consistent port 8082
);

// Process timers
$server->loop->addPeriodicTimer(0.1, function () use (&$timers) {
    $now = microtime(true);
    foreach ($timers as $id => $timer) {
        if (($now - $timer['start']) * 1000 >= $timer['ms']) {
            // Timer expired, execute callback and remove
            call_user_func($timer['callback']);
            unset($timers[$id]);
        }
    }
});

echo "Server running on port 8082...\n";
$server->run();