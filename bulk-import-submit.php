<?php
require_once 'constants.php';
require_once 'functions.php';
require_once 'MonoLogger.php';

$logger = new MonoLogger();
$logger->auto_delete_log(5);
// show_logfile_url($filename);
session_start();

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission

    // Directory to save uploaded files
    $uploadDirectory = RESUME_PDF_UPLOAD_DIR;

    // Create the directory if it doesn't exist
    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    // Insert data into the resume_processing_queue table
    $status = 'Processing'; // Set initial status
    $role = $_POST['role'];
    $createdBy = $_SESSION['userid']; // Assuming a user with ID 1 is creating the entry
    $lastUpdatedBy = $_SESSION['userid']; // Assuming a user with ID 1 is updating the entry


    // Handle file uploads
    if (isset($_FILES['resume_files'])) {
        $files = $_FILES['resume_files'];
        $insertQueueQuery = "INSERT INTO resume_processing_queue (status, role, created_by, last_updated_by) 
        VALUES ('$status', $role, $createdBy, $lastUpdatedBy)";
        mysqli_query($conn, $insertQueueQuery);
        $queueId = mysqli_insert_id($conn);
        // Iterate over each uploaded file
        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $fileTmpName = $files['tmp_name'][$i];
            $filePrefix = date('YmdHis') . "_{$queueId}_{$i}";
            $fileDestination = $uploadDirectory . $filePrefix . ".pdf";
            $pdfText = pdfToText($fileTmpName);
            // Move the uploaded file to the destination directory
            move_uploaded_file($fileTmpName, $fileDestination);

            // Insert data into the resume_insights table
            $originalFilePath = $fileDestination;
            $experience = null; // Update with actual experience value
            $skills = ''; // Update with actual skills value
            $matchedSkills = ''; // Update with actual matched skills value
            $score = ''; // Update with actual score value
            $insights = ''; // Update with actual insights value
            $sqlUpdateResume = "INSERT INTO resume_insights (queue_id, original_file_path,original_file_name,  status, insights) 
            VALUES( ? , ? , ? , ? , ?  )";
            $stmt = $conn->prepare($sqlUpdateResume);
            $stmt->bind_param("issss", 
                $queueId , 
                $originalFilePath ,
                $fileName ,
                $status , 
                $insights 
            );
            if($stmt->execute()){
                $resumeId = mysqli_insert_id($conn);
                $insertResumeContent = insertResumeContent($resumeId, $pdfText);
                if(!$insertResumeContent){
                    show_response(false ,  [],"Failed to update resume text");
                }
            }
            else{
                show_response(false ,  [],"Failed to add request");
            }
        }
    }
    else{
        show_response(false ,  [],"Resume files not found");
    }

    show_response(true ,  "" ,"Request added in the system","");
    exit();
}


function insertResumeContent($resumeId, $resumeText) {
    $resumeText = trim($resumeText);
    global $conn; // Assuming you have a database connection object
    $date = date('Y-m-d H:i:s');
    // Prepare the SQL statement with a parameter placeholder
    $sql = "INSERT INTO resume_content (resume_id, resume_text, created_date) 
            VALUES (?, ?, ?)";

    // Create a prepared statement
    $stmt = $conn->prepare($sql);

    // Bind the parameters with the values
    $stmt->bind_param("iss", $resumeId, $resumeText, $date);

    // Execute the statement
    if ($stmt->execute()) {
        // Insertion successful
        return true;
    } else {
        $error = $stmt->error;
        // Insertion failed
        return false;
    }
}

?>