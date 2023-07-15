<?php 

$host = 'localhost';
$username = 'your_username';
$password = 'your_password';
$database = 'your_database';

$customMysqli = new CustomMysqli($host, $username, $password, $database);

// Example usage
$query = 'SELECT * FROM your_table';
$result = $customMysqli->query($query);

// Fetching data from the result
while ($row = $result->fetch_assoc()) {
    // Process each row
}

// Get the number of rows
$rowCount = $customMysqli->getRowCount($result);

// Close the connection
$customMysqli->close();
