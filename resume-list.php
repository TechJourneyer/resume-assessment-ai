<?php
require_once 'templates/header.php';
$queue_id = isset($_GET['queue_id']) ? $_GET['queue_id'] : "";

?>
<?php authentication(); ?>
<div class="container-fluid" style="max-width: 1000px;">
    <input type="hidden" name="queue_id" value="<?php echo $queue_id; ?>">
    <h3>Resume Files</h3>
    <div id="resume-list">
        <div class="filters">
            <div class="form-group">
                <label for="filter-job-role">Job Role:</label>
                <select class="form-control" id="filter-job-role" name="role">
                    <option value="">Select a role</option>
                    <?php
                    // Fetch roles from the job_requirements table and populate the select options
                    $query = "SELECT  id ,role_name FROM job_requirements";
                    $result = mysqli_query($conn, $query);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<option value="' . $row['id'] . '">' . $row['role_name'] . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="jobDescription">Job Description</label>
                <textarea class="form-control" id="jobDescription" name="jobDescription" rows="3" readonly></textarea>
            </div>
            <div class="form-group">
                <label for="experience">Experience</label>
                <input type="text" class="form-control" id="experience" name="experience" readonly>
            </div>
            <div class="form-group">
                <label for="skills">Skills</label>
                <input type="text" class="form-control" id="skills" name="skills" readonly>
            </div>
            <div class="form-group">
                <label for="filter-status">Status:</label>
                <select id="filter-status" name='status' class="form-control">
                    <option value="">All</option>
                    <option value="Processing">Processing</option>
                    <option value="Completed">Completed</option>
                    <option value="Failed">Failed</option>
                    <option value="Regenerating">Regenerating</option>
                </select>
            </div>
            <div class="form-group">
                <button id="searchButton" class="btn btn-primary">Search</button>
                <button id="clearButton" class="btn btn-secondary">Clear</button>
            </div>
        </div>
        <div class="table-responsive">
            <table id="fileTable" class="table table-sm" style="width:100%">
                <thead>
                    <tr>
                        <th>Queue ID</th>
                        <th>Original File</th>
                        <th>Role</th>
                        <th>Candidate's Name</th>
                        <th>Exp</th>
                        <th>Score</th>
                        <th>Status</th>
                        <th>Report</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

    </div>
</div>
<script src="assets/js/resume-list.js"></script>
<script>
    // Function to clear all the filters
   
</script>
<?php require_once 'templates/footer.php'; ?>