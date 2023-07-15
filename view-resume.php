<?php
require_once 'constants.php'; 
require_once 'functions.php'; 
require_once 'vendor/autoload.php';

// Retrieve the record ID from the query string
$id = $_GET['id'];


$sql = "SELECT original_file_path FROM resume_insights WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $pdfFilePath = $row['original_file_path'];
    // Set the appropriate headers for rendering PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="document.pdf"');

    // Output the PDF file content
    readfile($pdfFilePath);

} else {
    echo "PDF file not found.";
}
