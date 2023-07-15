<?php
require_once 'constants.php';
require_once 'functions.php';
require_once 'MonoLogger.php';

// Fetch the list of roles from the database
$query = "SELECT 
    jr.id,
    jr.role_name,
    jr.qualification,
    jr.experience_years,
    jr.skills,
    jr.description,
    jr.created_by,
    jr.created_at,
    U.username
    FROM job_requirements jr
    INNER JOIN users U ON jr.created_by = U.id
 ";

// Get the total records count without any filtering
$totalRecordsQuery = "SELECT COUNT(*) as count FROM job_requirements";
$totalRecordsResult = mysqli_query($conn, $totalRecordsQuery);
$totalRecords = mysqli_fetch_assoc($totalRecordsResult)['count'];

// Apply filtering if necessary
if (!empty($_POST['role'])) {
    $roleFilter = $_POST['role'];
    $query .= " WHERE jr.role_name = '$roleFilter'";
}

// Apply pagination and ordering
$start = $_POST['start'];
$length = $_POST['length'];
$orderByColumnIndex = $_POST['order'][0]['column'];
$orderByColumnName = $_POST['columns'][$orderByColumnIndex]['data'];
$orderByDirection = $_POST['order'][0]['dir'];
$query .= " ORDER BY $orderByColumnName $orderByDirection LIMIT $start, $length";
$result = mysqli_query($conn, $query);

$roles = array();

// Iterate over the result set and store the roles in an array
while ($row = mysqli_fetch_assoc($result)) {
    // Limit the characters for the Description field
    $strlen = strlen($row['description']);
    $limitedDescription = substr($row['description'], 0, 50); // Change 50 to your desired limit

    // Add a "View" button to trigger the popup with complete description
    $descriptionWithButton = $limitedDescription;
    if ($strlen > 50) {
        $descriptionWithButton .= '... <a class="viewDescriptionBtn" data-description="' . $row['description'] . '">View more</a>';
    }

    $role = array(
        'id' => $row['id'],
        'role_name' => $row['role_name'],
        'qualification' => $row['qualification'],
        'experience_years' => $row['experience_years'],
        'skills' => $row['skills'],
        'description' => $descriptionWithButton,
        'created_by' => $row['username'],
        'created_at' => $row['created_at']
    );
    // <button class="btn btn-sm btn-primary editRoleBtn" data-id="' + role.id + '">Edit</button>
    $roles[] = $role;
}

// Prepare the response data for DataTables
$response = array(
    'draw' => intval($_POST['draw']),
    'recordsTotal' => $totalRecords,
    'recordsFiltered' => count($roles),
    'data' => $roles,
);

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);
