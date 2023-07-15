<?php 

function pdfToText($filename) {
    // Use pdftotext to convert the PDF to text
    $command = POPPLER_UTILS_BINARY . " -layout " . escapeshellarg($filename) . " -";
    $output = shell_exec($command); // execute the command and capture the output
    return $output;
}

function show_response($success ,  $response,$message ="",$details=[]){
    $result =  [
        "success" => $success,
        "html" => $response,
        "message" => $message,
        "details" => $details
    ];
    echo json_encode($result);
    exit;
}

function writeLog($message, $logFile = false){ 
    $date = date('Y-m-d');
    if($logFile === false){
        $logFile = BASE_URI . "logs/$date.txt";
    }
    
    // Create a timestamp for the log entry
    $timestamp = date('Y-m-d H:i:s');

    // Format the log message with the timestamp and any additional information
    $logEntry = "$timestamp| $message\n";

    // Write the log entry to the specified log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
}

function show_resume_report($response){
    ob_start();
	
	$resume_alignment_score = isset($response['resume_alignment_score']) ? $response['resume_alignment_score'] : false ;

	if($resume_alignment_score === false){
		return show_error_message("Something went wrong!");
	}

	$resume_alignment_score = str_replace('%', '', $resume_alignment_score) . '%' ;
	
    $interview_questions = isset($response['interview_questions']) ? $response['interview_questions'] : [];
    $certifications = isset($response['certifications']) ? $response['certifications'] : false;
    $matched_skills = isset($response['matched_skills']) ? $response['matched_skills'] : false ;
	$resume_summary = isset($response['resume_summary']) ? $response['resume_summary'] : false;
    $resume_tagline = isset($response['resume_tagline']) ? $response['resume_tagline'] : false;
	$skills = isset($response['skill']) ? $response['skill'] : false;

	$work_experience = isset($response['work_experience_in_years']) ? $response['work_experience_in_years'] : false;

    $jd_matching_percentage = isset($response['job_desc_matching_percentage']) ? $response['job_desc_matching_percentage'] : false;
	$jd_matching_percentage = ($jd_matching_percentage === false) ? false :  str_replace('%', '', $jd_matching_percentage)  ;
    
	$skills_matching_percentage = isset($response['skills_matching_percentage']) ? $response['skills_matching_percentage'] : false;
	$skills_matching_percentage = ($skills_matching_percentage === false) ? false :  str_replace('%', '', $skills_matching_percentage)  ;

	$expected_experiance_matching_score = isset($response['expected_experience_matching_percentage']) ? $response['expected_experience_matching_percentage'] : false;
	$expected_experiance_matching_score = ($expected_experiance_matching_score === false) ? false :  str_replace('%', '', $expected_experiance_matching_score) ;

	$resume_alignment_score = intval($resume_alignment_score);
	$work_experience = intval($work_experience);
	$jd_matching_percentage = intval($jd_matching_percentage);
	$skills_matching_percentage = intval($skills_matching_percentage);
	$expected_experiance_matching_score = intval($expected_experiance_matching_score);

	$timeline = isset($response['timeline']) ? $response['timeline'] : false;

	?>

	<div class="alignment-score-label">Resume Alignment Score</div>
	<div class="alignment-score">
		<div class="alignment-score-bar">
			<div class="alignment-score-progress" style="width: <?php echo $resume_alignment_score; ?>%;"></div>
		</div>
		<div class="alignment-score-percent"><?php echo $resume_alignment_score; ?>%</div>
	</div>
	<div class="resume-overview card">
		<div class="card-header">
			<i class="fas fa-chart-bar"></i> Resume Overview
		</div>
		<div class="card-body">
			<h3><?php echo $resume_tagline; ?></h3>
			<p><?php echo $resume_summary; ?></p>
			<?php if($work_experience!==false){ ?>
				<h5>Work Experiance : <?php echo $work_experience; ?></h5>
			<?php } ?>
			<?php if(!empty($skills)) { ?>
				<br>
				<h5>Skills</h5>
				<ul>
					<?php foreach($skills as $skill) { ?>
						<li>
							<?php echo show_skills($skill ,$matched_skills);?>
						</li>
					<?php } ?>
				</ul>
			<?php } ?>
			<br>
			<h5>Certifications</h5>
			<?php if(!empty($certifications) && is_array($certifications)) { ?>
				<ul>
					<?php 
					foreach($certifications as $certification) { 
						if(is_array($certification)){
							continue;
						}
						?>
						<li>
							<strong><?php echo $certification;?></strong>
						</li>
					<?php } ?>
				</ul>
			<?php } else { ?>
				<p class="text-muted">No certifications found</p>
			<?php } ?>
		</div>
	</div>
	<div class="resume-compatibility card">
		<div class="card-header">
			<i class="fas fa-chart-bar"></i> Resume Compatibility
		</div>
		<div class="card-body">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Matching Criteria</th>
						<th>Percentage</th>
					</tr>
				</thead>
				<tbody>
					<?php if($skills_matching_percentage !== false) { ?>
						<tr>
							<td>Skills Match Percentage</td>
							<td>
								<div class="progress">
									<div class="progress-bar <?php echo bg_class_by_score($skills_matching_percentage); ?>" role="progressbar" style="width: <?php echo $skills_matching_percentage; ?>%;" aria-valuenow="<?php echo $skills_matching_percentage; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $skills_matching_percentage; ?>%</div>
								</div>
							</td>
						</tr>
					<?php } ?>
					<?php if($jd_matching_percentage !== false) { ?>
						<tr>
							<td>Job Fit Percentage</td>
							<td>
								<div class="progress">
									<div class="progress-bar <?php echo bg_class_by_score($jd_matching_percentage); ?>" role="progressbar" style="width: <?php echo $jd_matching_percentage; ?>%;" aria-valuenow="<?php echo $jd_matching_percentage; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $jd_matching_percentage; ?>%</div>
								</div>
							</td>
						</tr>
					<?php } ?>

					<?php if($expected_experiance_matching_score !== false) { ?>
						<tr>
							<td>Experience Match Percentage</td>
							<td>
								<div class="progress">
									<div class="progress-bar <?php echo bg_class_by_score($expected_experiance_matching_score); ?>" role="progressbar" style="width: <?php echo $expected_experiance_matching_score; ?>%;" aria-valuenow="<?php echo $expected_experiance_matching_score; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $expected_experiance_matching_score; ?>%</div>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<i class="fas fa-file-alt"></i> Timeline
		</div>
		<div class="card-body">
			<?php if(!empty($timeline)) { ?>
				<div id="content">
					<ul class="timeline">
						<?php foreach ($timeline as $row) { ?>

						<li class="event" data-date="<?php echo $row['time']; ?>">
							<h3 style='display: flex;'> <i style='padding: 10px;' class="<?php echo timeline_event_icon($row['type']); ?>"></i> <?php echo $row['event']; ?></h3>
						</li>
						<?php } ?>
					</ul>
				</div>
			<?php } else {
				echo show_error_message("Failed to create timeline");
			}?>
		</div>
	</div>

	<div class="card">
		<div class="card-header">
			<i class="fas fa-file-alt"></i> Interview Questions
		</div>
		<div class="card-body">
			<?php if(!empty($interview_questions)) { ?>
				<?php if(is_array($interview_questions)){?>
				<ul class="interview-questions">
					<?php foreach ($interview_questions as $question) { ?>
						<li>
							<strong><?php echo $question;?></strong> 
						</li>
					<?php } ?>
				</ul>
				<?php } else { ?>
					<pre style='font-weight:bold'><?php echo trim($interview_questions);?></pre>
				<?php } ?>
			<?php } else {
				echo show_error_message("Failed to generate interview questions");
			}?>
		</div>
	</div>

	
	<?php
	$html_output = ob_get_clean();
    return $html_output;

}

function timeline_event_icon($event){
	$icons = [
		"education"=>"fas fa-graduation-cap fa-2xl",
		"certification"=>"fas fa-certificate fa-2xl",
		"work"=>"fas fa-briefcase fa-2xl"
	];
	return isset($icons[$event]) ? $icons[$event] : "";  
}
function bg_class_by_score($score){
	if($score>60){
		return 'bg-success';
	}
	elseif($score > 40){
		return 'bg-warning';
	}
	else{
		return 'bg-danger';
	}
}

function show_error_message($message){
	ob_start();
	?>
	<div class="alert alert-primary" role="alert">
		<?php echo $message; ?>
	</div>
	<?php
	$html_output = ob_get_clean();
    return $html_output;
}

function show_skills($skill ,$matched_skills){
	if(!empty($matched_skills)){
		$matched_skills = array_map('strtolower', $matched_skills);
		if(in_array(strtolower($skill),$matched_skills)){
			return "<span >$skill</span> <i title='Matched skill' style='color:#e45cff' class='fas fa-check-circle'></i>";
		}
	}
	return $skill;
}

function is_login(){
	if(isset($_SESSION['login']) && $_SESSION['login'] == true){
		return true;
	}
	return false;
}

function authentication(){
	if(!is_login()){
		header('Location: login.php');
	}
}

function write_text_to_file($content,$file){
	// Write the content to the file
	if (file_put_contents($file, $content) !== false) {
		return true;
	} else {
		return false;
	}
}


function show_resume_report1($response){
    ob_start();
	
	$resume_alignment_score = isset($response['resume_alignment_score']) ? $response['resume_alignment_score'] : false ;

	if($resume_alignment_score === false){
		return show_error_message("Something went wrong!");
	}

	$resume_alignment_score = str_replace('%', '', $resume_alignment_score) . '%' ;
	
    $interview_questions = isset($response['interview_questions']) ? $response['interview_questions'] : [];
    $certifications = isset($response['certifications']) ? $response['certifications'] : false;
    $matched_skills = isset($response['skill_assessment']['candidates_skills']) ? $response['skill_assessment']['candidates_skills'] : [];
	$resume_summary = isset($response['resume_summary']) ? $response['resume_summary'] : false;
    $resume_tagline = isset($response['resume_tagline']) ? $response['resume_tagline'] : false;
	$skills = isset($response['skill_assessment']['candidates_skills']) ? $response['skill_assessment']['candidates_skills'] : [];

	$work_experience = isset($response['work_experience_in_years']) ? $response['work_experience_in_years'] : false;

    $jd_matching_percentage = isset($response['job_desc_matching_percentage']) ? $response['job_desc_matching_percentage'] : false;
	$jd_matching_percentage = ($jd_matching_percentage === false) ? false :  str_replace('%', '', $jd_matching_percentage)  ;
    
	$skills_matching_percentage = isset($response['skill_assessment']['overall_candidates_skills_matching_score']) ? $response['skill_assessment']['overall_candidates_skills_matching_score'] : false;
	$skills_matching_percentage = ($skills_matching_percentage === false) ? false :  str_replace('%', '', $skills_matching_percentage)  ;

	$expected_experience_matching_score = isset($response['skill_assessment']['overall_expected_skills_matching_score']) ? $response['skill_assessment']['overall_expected_skills_matching_score'] : false;
	$expected_experience_matching_score = ($expected_experience_matching_score === false) ? false :  str_replace('%', '', $expected_experience_matching_score) ;

	$resume_alignment_score = intval($resume_alignment_score);
	$work_experience = intval($work_experience);
	$jd_matching_percentage = intval($jd_matching_percentage);
	$skills_matching_percentage = intval($skills_matching_percentage);
	$expected_experience_matching_score = intval($expected_experience_matching_score);

	$timeline = isset($response['timeline']) ? $response['timeline'] : false;

	?>

	<div class="alignment-score-label">Resume Alignment Score</div>
	<div class="alignment-score">
		<div class="alignment-score-bar">
			<div class="alignment-score-progress" style="width: <?php echo $resume_alignment_score; ?>;"></div>
		</div>
		<div class="alignment-score-percent"><?php echo $resume_alignment_score; ?></div>
	</div>
	<div class="resume-overview card">
		<div class="card-header">
			<i class="fas fa-chart-bar"></i> Resume Overview
		</div>
		<div class="card-body">
			<h3><?php echo $resume_tagline; ?></h3>
			<p><?php echo $resume_summary; ?></p>
			<?php if($work_experience !== false) { ?>
				<h5>Work Experience: <?php echo $work_experience; ?> years</h5>
			<?php } ?>
			<?php if(!empty($skills)) { ?>
				<h5>Skills:</h5>
				<ul>
					<?php foreach($skills as $skill) { ?>
						<li><?php echo $skill; ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
		</div>
	</div>

	<div class="matching-score card">
		<div class="card-header">
			<i class="fas fa-percent"></i> Matching Scores
		</div>
		<div class="card-body">
			<?php if($jd_matching_percentage !== false) { ?>
				<h5>Job Description Matching: <?php echo $jd_matching_percentage; ?>%</h5>
			<?php } ?>
			<?php if($skills_matching_percentage !== false) { ?>
				<h5>Skills Matching: <?php echo $skills_matching_percentage; ?>%</h5>
			<?php } ?>
			<?php if($expected_experience_matching_score !== false) { ?>
				<h5>Expected Experience Matching: <?php echo $expected_experience_matching_score; ?>%</h5>
			<?php } ?>
		</div>
	</div>

	<?php if(!empty($interview_questions)) { ?>
		<div class="interview-questions card">
			<div class="card-header">
				<i class="fas fa-question-circle"></i> Interview Questions
			</div>
			<div class="card-body">
				<ol>
					<?php foreach($interview_questions as $question) { ?>
						<li><?php echo $question; ?></li>
					<?php } ?>
				</ol>
			</div>
		</div>
	<?php } ?>

	<?php if($certifications !== false) { ?>
		<div class="certifications card">
			<div class="card-header">
				<i class="fas fa-certificate"></i> Certifications
			</div>
			<div class="card-body">
				<ul>
					<?php foreach($certifications as $certification) { ?>
						<li><?php echo $certification; ?></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	<?php } ?>

	<?php if($timeline !== false) { ?>
		<div class="timeline card">
			<div class="card-header">
				<i class="fas fa-calendar-alt"></i> Timeline
			</div>
			<div class="card-body">
				<ul>
					<?php foreach($timeline as $event) { ?>
						<li><?php echo $event; ?></li>
					<?php } ?>
				</ul>
			</div>
		</div>
	<?php } ?>

	<?php

	$html = ob_get_clean();
	return $html;
}


			

