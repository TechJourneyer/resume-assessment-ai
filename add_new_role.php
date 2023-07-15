<?php

require_once 'constants.php'; 
require_once 'functions.php'; 

session_start();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming you have established a database connection

    // Get the form data
    $roleName = $_POST['roleName'];
    $qualification = $_POST['qualification'];
    $experienceYears = $_POST['experienceYears'];
    $skills = $_POST['skills'];
    $description = $_POST['description'];
    $createdBy = $_SESSION['userid'];


    // Prepare the SQL statement
    $sql = "INSERT INTO job_requirements (role_name, qualification, experience_years, skills, description,created_by)
            VALUES (?, ?, ?, ?, ? , ?)";

    // Prepare and bind the parameters
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssissi", $roleName, $qualification, $experienceYears, $skills, $description,$createdBy);

    // Execute the query
    if ($stmt->execute()) {
        // Role added successfully
        echo "Role added successfully.";
    } else {
        // Error occurred while adding role
        echo "Error: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}
else{
    echo "Something went wrong";
}
?>

