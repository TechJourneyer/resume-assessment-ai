<?php
require_once 'constants.php';
require_once 'functions.php';
require_once 'MonoLogger.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedRole = $_POST['role'];

    // Fetch job description, experience, and skills from the job_requirements table for the selected role
    $query = "SELECT description, experience_years, skills FROM job_requirements WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $selectedRole);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $description, $experience, $skills);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Prepare the response as a JSON object
    $response = array(
        'description' => $description,
        'experience' => $experience,
        'skills' => $skills
    );

    header('Content-Type: application/json');
    echo json_encode($response);
}
