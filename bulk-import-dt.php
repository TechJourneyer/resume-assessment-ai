<?php
require_once 'constants.php';
require_once 'functions.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define columns
$columns = array(
    0 => 'queue.id',
    1 => 'queue.status',
    2 => 'jr.role_name',
    3 => 'users.username',
    4 => 'queue.created_date',
    5 => 'COUNT(insights.queue_id)'
);

// Get total number of records
$sql = "SELECT COUNT(*) as count FROM resume_processing_queue AS queue";
$countResult = $conn->query($sql);
$countRow = $countResult->fetch_assoc();
$totalRecords = $countRow['count'];

// Get filtered records
$sql = "SELECT queue.id, queue.status, jr.role_name as role, users.username, queue.created_date, COUNT(insights.queue_id) as file_count
        FROM resume_processing_queue AS queue
        LEFT JOIN users ON queue.created_by = users.id
        LEFT JOIN job_requirements jr ON jr.id = queue.role

        LEFT JOIN resume_insights AS insights ON queue.id = insights.queue_id
        WHERE 1=1";
if (!empty($_POST['search']['value'])) {
    $searchValue = $_POST['search']['value'];
    $sql .= " AND (queue.status LIKE '%$searchValue%' OR queue.role LIKE '%$searchValue%' OR users.username LIKE '%$searchValue%')";
}
$sql .= " GROUP BY queue.id"; // Group by queue.id to get the count of files per queue

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
        $subdata = array();
        $subdata['id'] = $row['id'];
        $subdata['status'] = $row['status'];
        $subdata['role'] = $row['role'];
        $subdata['username'] = $row['username'];
        $subdata['created_date'] = $row['created_date'];
        $subdata['file_count'] = $row['file_count'];
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
?>
