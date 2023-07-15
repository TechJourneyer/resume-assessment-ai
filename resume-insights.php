<?php
require_once 'constants.php'; 
require_once 'functions.php'; 
require_once 'vendor/autoload.php';
$hide_navbar = true;
require_once 'templates/header.php';
authentication();

// Retrieve the record ID from the query string
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $sql = "SELECT id,queue_id,candidate_name,experience,score,insights,status FROM resume_insights WHERE id = $id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $insights = $row['insights'];
        $status = $row['status'];

        if(empty($insights) ){
            die('No insights');
        }
        if(strtolower($status) != 'completed'){
            die('Still not completed');
        }
        $insights_array = json_decode($insights , true);
        $html = show_resume_report($insights_array);
        ?>
        <div class="container">
            <?php echo $html;?>
        </div>
        <?php 
    
    } else {
        echo "PDF file not found.";
    }
}
else{
    echo "Invalid request";
}

