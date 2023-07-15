<?php 
require_once 'templates/header.php'; 
authentication();
?>

<div class="container">
    <h3 class="">Bulk Import</h3>

    <form id="upload-form" enctype="multipart/form-data" method="post">
        <div class="form-group">
            <label for="resumeUpload">
                <i class="fas fa-file-alt"></i> Upload Resumes
            </label>
            <input type="file" class="form-control-file" id="resumeUpload" name="resume_files[]" multiple required>
            <div class="invalid-feedback">Please upload one or more PDF files.</div>
        </div>

        <div class="form-group">
            <label for="roleSelect">
                Select Role
            </label>
            <select class="form-control" id="roleSelect" name="role">
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

        <button type="submit" id="upload_files_button" class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload Files
        </button>
        <div class="spinner-border text-primary loader" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </form>

    <div class="loader loading-content">
        <div class="spinner-grow text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <div class="spinner-grow text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
    </div>

	<div id="resume-list " class="table-responsive" >
		<table id="resume_queue_table" class="table table-sm" >
			<thead>
				<tr>
					<th>Id</th>
					<th>Status</th>
					<th>Role</th>
					<th>Username</th>
					<th>Created Date</th>
					<th>File Count</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<script src="assets/js/bulk-import.js"></script>


<?php require_once 'templates/footer.php'; ?>