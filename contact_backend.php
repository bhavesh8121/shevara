<?php
header('Content-Type: application/json');

// Database config
$servername = "localhost";
$username = "root";       // your DB username
$password = "";           // your DB password
$dbname = "if0_39923297_fgbgf"; // your DB name
$port = 3307;             // your MySQL port (you mentioned this is custom)

// Connect to database
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "error" => "DB connection failed"]);
    exit;
}

// Handle POST requests: store a message
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $sender = trim($_POST['sender'] ?? 'anonymous');

    if ($message === '') {
        echo json_encode(["success" => false, "error" => "Empty message"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO chat_messages (sender, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $sender, $message);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }

    $stmt->close();
    $conn->close();
    exit;
}

// Handle GET requests: return all messages
$result = $conn->query("SELECT sender, message, created_at FROM chat_messages ORDER BY id ASC");
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

echo json_encode($messages);
$conn->close();
?>
