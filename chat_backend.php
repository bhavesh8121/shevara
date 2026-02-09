<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
// $port = 3307;

$conn = new mysqli("localhost", "root", "", "if0_39923297_fgbgf", 3307);
if ($conn->connect_error) {
    die(json_encode(["error" => $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = trim($_POST['message'] ?? '');
    $sender  = trim($_POST['sender'] ?? 'anonymous');

    if ($message === '') {
        echo json_encode(["error" => "Empty message"]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO chat (message, sender) VALUES (?, ?)");
    $stmt->bind_param("ss", $message, $sender);
    $stmt->execute();
    $stmt->close();

    echo json_encode(["success" => true]);
    exit();
}

// GET request: fetch messages
$result = $conn->query("SELECT message, sender, created_at FROM chat ORDER BY id ASC");
$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}
echo json_encode($messages);
$conn->close();
?>
