<?php
require_once 'constants.php';
require_once 'functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resume_id = $_POST['resume_id'];

    // Update the status in resume_insights table
    $update_insights_query = "UPDATE resume_insights SET status = 'Regenerating' WHERE id = ?";
    $stmt_insights = mysqli_prepare($conn, $update_insights_query);
    mysqli_stmt_bind_param($stmt_insights, "i", $resume_id);
    mysqli_stmt_execute($stmt_insights);
    mysqli_stmt_close($stmt_insights);

    // Update the status in resume_processing_queue table
    $update_queue_query = "UPDATE resume_processing_queue SET status = 'Regenerating' WHERE id = ?";
    $stmt_queue = mysqli_prepare($conn, $update_queue_query);
    mysqli_stmt_bind_param($stmt_queue, "i", $resume_id);
    mysqli_stmt_execute($stmt_queue);
    mysqli_stmt_close($stmt_queue);

    // Prepare the response as a JSON object
    $response = array(
        'message' => 'Status updated successfully.'
    );

    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
