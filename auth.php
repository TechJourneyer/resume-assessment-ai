<?php
session_start();
require_once 'constants.php';
require_once 'functions.php';

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve username and password from the login form
$username = $_POST['username'];
$password = md5(trim($_POST['password']));

// Prepare and execute the query using prepared statements to prevent SQL injection
$stmt = $conn->prepare("SELECT id,user_group,username FROM users WHERE username = ? AND password = ?");
$stmt->bind_param("ss", $username, $password);
$stmt->execute();

$result = $stmt->get_result();
if ($result->num_rows === 1) {
    // Authentication successful
    $row = $result->fetch_assoc();
    $_SESSION['login'] = true;
    $_SESSION['userid'] = $row['id'];
    $_SESSION['user_group'] = $row['user_group'];
    $_SESSION['username'] = $username;
    header('Location: index.php'); // Redirect to the dashboard page
} else {
    // Authentication failed
    $errorMessage = "Invalid username or password.";
    $_SESSION['error_message'] = $errorMessage;
    // Authentication failed
    header('Location: login.php'); // Redirect back to the login page
}

// Close the database connection
$stmt->close();
$conn->close();
?>