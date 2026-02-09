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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get action parameter
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    switch($action) {
        case 'add_contact':
            handleAddContact($conn);
            break;
        case 'mark_attendance':
            handleMarkAttendance($conn);
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Invalid action"
            ]);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Handle GET requests
    $action = isset($_GET['action']) ? $_GET['action'] : '';
    
    switch($action) {
        case 'get_contacts':
            getContacts($conn);
            break;
        case 'get_attendance':
            getAttendance($conn);
            break;
        default:
            header('Content-Type: application/json');
            echo json_encode([
                "status" => "error",
                "message" => "Invalid action"
            ]);
    }
} else {
    // If not POST or GET request
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}

$conn->close();

// Handle adding a contact
function handleAddContact($conn) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    
    // Validate input
    if (empty($name) || empty($phone)) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Name and phone are required"
        ]);
        return;
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO safety (type, name, phone, timestamp) VALUES (?, ?, ?, NOW())");
    $type = "contact";
    $stmt->bind_param("sss", $type, $name, $phone);
    
    // Execute and check result
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "message" => "Contact added successfully",
            "data" => [
                "name" => $name,
                "phone" => $phone
            ]
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Error adding contact: " . $conn->error
        ]);
    }
    
    $stmt->close();
}

// Handle marking attendance
function handleMarkAttendance($conn) {
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    
    // Validate input
    if (empty($status)) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Status is required"
        ]);
        return;
    }
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO safety (type, name, phone, timestamp) VALUES (?, ?, ?, NOW())");
    $type = "attendance";
    $name = $status;
    $phone = "";
    $stmt->bind_param("sss", $type, $name, $phone);
    
    // Execute and check result
    if ($stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "message" => "Attendance marked successfully"
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "error",
            "message" => "Error marking attendance: " . $conn->error
        ]);
    }
    
    $stmt->close();
}

// Get all contacts
function getContacts($conn) {
    $stmt = $conn->prepare("SELECT name, phone FROM safety WHERE type = 'contact' ORDER BY timestamp DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $contacts = [];
    while($row = $result->fetch_assoc()) {
        $contacts[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "data" => $contacts
    ]);
    
    $stmt->close();
}

// Get attendance records
function getAttendance($conn) {
    $stmt = $conn->prepare("SELECT name, timestamp FROM safety WHERE type = 'attendance' ORDER BY timestamp DESC LIMIT 10");
    $stmt->execute();
    $result = $stmt->get_result();
    
    $attendance = [];
    while($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        "status" => "success",
        "data" => $attendance
    ]);
    
    $stmt->close();
}
?>