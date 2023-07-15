<?php 
require_once 'constants.php'; 
require_once 'functions.php'; 
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
if(isset($_GET['id'])){
    $resume_id = $_GET['id'];
    renderPDF("resume-insights.php");
}
else{
    echo "Invalid request";
}

require_once 'dompdf/autoload.inc.php'; // Include Dompdf autoload file


function renderPDF($phpFilePath) {
    global $conn;
    // Create a new Dompdf instance
    $dompdf = new Dompdf();

    // Load the PHP file
    ob_start();
    include $phpFilePath;
    $html = ob_get_clean();

    // Load HTML content into Dompdf
    $dompdf->loadHtml($html);

    // Set paper size and orientation
    $dompdf->setPaper('A4', 'portrait');

    // Render the PDF
    $dompdf->render();

    // Output the PDF to the browser
    $dompdf->stream('output.pdf', ['Attachment' => false]);
}


