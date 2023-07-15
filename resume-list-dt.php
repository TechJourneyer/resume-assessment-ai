<?php
require_once 'constants.php'; 
require_once 'functions.php'; 

session_start();
// Get the queue_id , role and status parameters 
$queueId = $_POST['queue_id'] ? $_POST['queue_id'] : null;
$role = $_POST['role'] ? $_POST['role'] : null;
$status = $_POST['status'] ? $_POST['status'] : null;

// Prepare the WHERE clause based on the role and status
$whereClause = '';
$filters = [];
if (!empty($queueId)) {
    $filters[] = "  queue_id = $queueId ";
}
if (!empty($role)) {
    $filters[] = "  rq.role = $role ";
} 
if (!empty($status)) {
    $filters[] = "  ri.status = '$status' ";
}
if(!empty( $filters)){
    $whereClause = "WHERE ". implode(" AND ", $filters);
}

// Define columns
$columns = array(
    0 => 'ri.id',
    1 => 'ri.original_file_name',
    2 => 'ri.role_name',
    3 => 'ri.candidate_name',
    4 => 'ri.experience',
    5 => 'ri.score',
    6 => 'ri.status',
    7 => 'ri.insights',
    8 => 'ri.created_date'
);

// Get total number of records
$sql = "SELECT COUNT(*) as count FROM resume_insights ri
        JOIN resume_processing_queue rq ON ri.queue_id = rq.id
        $whereClause";
$countResult = $conn->query($sql);
$countRow = $countResult->fetch_assoc();
$totalRecords = $countRow['count'];

// Get filtered records
$sql = "SELECT ri.id,
            ri.queue_id,
            ri.original_file_path,
            ri.original_file_name,
            ri.experience,
            ri.candidate_name,
            ri.skills,
            ri.matched_skills, 
            ri.score, 
            ri.status, 
            ri.insights, 
            ri.created_date, 
            rq.role , jr.role_name FROM resume_insights ri
        JOIN resume_processing_queue rq ON ri.queue_id = rq.id
        JOIN job_requirements jr ON rq.role = jr.id
        $whereClause";
$filteredResult = $conn->query($sql);
$totalFiltered = $filteredResult->num_rows;

// Get records for pagination
$start = $_POST['start'];
$length = $_POST['length'];
$sql .= " ORDER BY " . $columns[$_POST['order'][0]['column']] . " " . $_POST['order'][0]['dir'] . " LIMIT $start, $length";
$result = $conn->query($sql);
// Format data for DataTables
$data = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $original_file_name = $row['original_file_name'];
        $status = $row['status'];
        $subdata = array();
        $subdata['id'] = $row['id'];
        $subdata['queue_id'] = $row['queue_id'];
        $subdata['original_file_path'] = '<a target="_blank" href="view-resume.php?id=' . $row['id'] . '">'.$original_file_name.'</a>';
        $subdata['experience'] = $row['experience'];
        $subdata['candidate_name'] = $row['candidate_name'];
        $subdata['skills'] = $row['skills'];
        $subdata['matched_skills'] = $row['matched_skills'];
        $subdata['score'] = $row['score'];
        $regenerate = "";
        if($_SESSION['user_group'] == 'admin'){
            if(strtolower($status) == 'completed' || strtolower($status) == 'failed'){
                $regenerate = '<button title="Regenerate" class="btn btn-primary btn-sm regenerate-button" data-id="' . $row['id'] . '"><i class="fas fa-redo-alt"></i> </button>';
            }
        }
        $subdata['status'] = $status . $regenerate;
        $subdata['role_name'] = $row['role_name'];
        if(strtolower($row['status']) == 'completed'){
            $subdata['insights'] = '<a target="_blank" href="resume-insights.php?id=' . $row['id'] . '">View Report</a>';
        }
        else{
            $subdata['insights'] = '<p class="text-muted">N/A</p>';
        }
        $subdata['created_date'] = date("d M Y",strtotime($row['created_date']));
        $subdata['role'] = $row['role'];
        $data[] = $subdata;
    }
}

// Return response
$response = array(
    "draw" => intval($_POST['draw']),
    "recordsTotal" => $totalRecords,
    "recordsFiltered" => $totalFiltered,
    "data" => $data
);
echo json_encode($response);
exit();
