<?php
require_once 'templates/header.php'; 
authentication();
// Assuming you have a database connection established and stored in the $conn variable

// Check if the role ID is provided as a parameter in the URL
if (isset($_GET['id'])) {
    $roleId = $_GET['id'];

    // Fetch the role details from the database
    $query = "SELECT * FROM job_requirements WHERE id = $roleId";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);

    // Check if the role exists
    if ($row) {
        $roleName = $row['role_name'];
        $qualification = $row['qualification'];
        $experienceYears = $row['experience_years'];
        $skills = $row['skills'];
        $description = $row['description'];

        // Handle the form submission to update the role
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updatedQualification = $_POST['qualification'];
            $updatedExperienceYears = $_POST['experienceYears'];
            $updatedSkills = $_POST['skills'];
            $updatedDescription = $_POST['description'];

            // Update the role in the database
            $updateQuery = "UPDATE job_requirements SET qualification = '$updatedQualification', 
                            experience_years = '$updatedExperienceYears', skills = '$updatedSkills', 
                            description = '$updatedDescription' WHERE id = $roleId";
            mysqli_query($conn, $updateQuery);

            // Redirect back to the UI page after the role is updated
            header('Location: job_roles.php');
            exit();
        }
    } else {
        echo "Role not found.";
        exit();
    }
} else {
    echo "Invalid role ID.";
    exit();
}
?>

<div class="container">
<!-- HTML form for editing the role -->
<h2>Edit Role</h2>
<form id="editRoleForm" method="post">
    <div class="form-group">
        <label for="roleName">Role Name</label>
        <input type="text" class="form-control" id="roleName" name="roleName" value="<?php echo $roleName; ?>" disabled>
    </div>
    <div class="form-group">
        <label for="qualification">Qualification</label>
        <input type="text" class="form-control" id="qualification" name="qualification" value="<?php echo $qualification; ?>" required>
    </div>
    <div class="form-group">
        <label for="experienceYears">Experience (in years)</label>
        <input type="number" class="form-control" id="experienceYears" name="experienceYears" value="<?php echo $experienceYears; ?>" required>
    </div>
    <div class="form-group">
        <label for="skills">Skills</label>
        <input type="text" class="form-control" id="skills" name="skills" value="<?php echo $skills; ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $description; ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Update Role</button>
</form>

<div>

<?php require_once 'templates/footer.php'; ?>
