<?php
// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "if0_39923297_fgbgf";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,3307);

// Check connection
if ($conn->connect_error) {
    // Return JSON error response
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Process form submissionhome
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and sanitize input data
    $issue = isset($_POST['problem']) ? trim($_POST['problem']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';
    
    // Validate input
    if (empty($issue) || empty($message)) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "All fields are required"
        ]);
        exit();
    }
    
    // Further validation
    $allowed_issues = ['period', 'health', 'unsafe', 'other'];
    if (!in_array($issue, $allowed_issues)) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Invalid issue type"
        ]);
        exit();
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO help_requests (issue, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $issue, $message);
    
    // Execute and check result
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "message" => "Your request has been submitted successfully"
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Error submitting request: " . $conn->error
        ]);
    }
    
    $stmt->close();
} else {
    // If not POST request
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}

$conn->close();
?>