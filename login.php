<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "if0_39923297_fgbgf";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,3307);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare and execute SQL
    $stmt = $conn->prepare("SELECT password FROM registered WHERE full_name = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['username'] = $username;
            echo "<div style='color: #00ff99; text-align: center; margin-top: 20px;'>Login successful! Welcome, " . htmlspecialchars($username) . ".</div>";
            echo "<script>setTimeout(() => window.location.href = 'index.html', 2000);</script>";
            exit();
        } else {
            echo "<div class='error'>Incorrect password.</div>";
        }
    } else {
        echo "<div class='error'>Username not found.</div>";
    }
    $stmt->close();
} else {
    echo "<div class='error'>Invalid request method.</div>";
}

$conn->close();
?>