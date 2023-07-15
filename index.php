<?php require_once 'templates/header.php'; ?>
<?php authentication(); 
if(!isset($_GET['testing'])){
	header('Location: bulk-import.php'); // Redirect to the dashboard page
}
?>
<div class="container">
	<h3 >Resume Analyzer </h3>

	<form id="upload-form">
		<div class="form-group">
			<label for="resumeUpload">
				<i class="fas fa-file-alt"></i> Upload Resume </label>
			<input type="file" class="form-control-file" id="resumeUpload" name='pdf_file' required>
			<div class="invalid-feedback">Please upload a PDF file.</div>
		</div>
		<div class="form-group">
			<label for="jobExpectation">
				<i class="fas fa-file-alt"></i> Job Expectation </label>
			<textarea class="form-control" id="jobExpectation" rows="5" name='job_description' required ></textarea>
			<div class="invalid-feedback">Please provide the job expectation.</div>
		</div>
		<div class="form-group">
			<label for="expectedSkills">
				<i class="fas fa-check-circle"></i> Expected Skills </label>
			<select multiple class="form-control" id="expectedSkills" name='skills[]'  required>
				<option value="">Select skills</option>
				<option value="Java">Java</option>
				<option value="Python">Python</option>
				<option value="C++">C++</option>
				<option value="JavaScript">JavaScript</option>
				<option value="PHP">PHP</option>
			</select>
			<div class="invalid-feedback">Please select at least one skill.</div>
		</div>
		<div class="form-group">
			<label for="expectedExperience">
				<i class="fas fa-clock"></i> Expected Experience (in years) </label>
			<input type="number" class="form-control" id="expectedExperience" name='experience' min="1" required>
			<div class="invalid-feedback">Please provide expected experience in years.</div>
		</div>
		<button type="submit" id="analyze_resume_button" class="btn btn-primary">
			<i class="fas fa-search"></i> Analyze Resume 
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

	<div id='resume-assessment-report'></div>
	
	<?php 
		if(isset($_GET['example']) && $_GET['example']==1){
			require_once 'functions.php';
			$jsonString = file_get_contents('test.json');
			$response = json_decode($jsonString, true);
			echo show_resume_report($response);
		}
	?>
</div>
<script src="assets/js/index.js"></script>

<?php require_once 'templates/footer.php'; ?>