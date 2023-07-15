<?php

require_once 'constants.php';
require_once 'functions.php';
require_once 'MonoLogger.php';

require_once 'api.php';

ini_set('max_execution_time',1000);

// Initialize Monologger for logging
$MonoLogger = new MonoLogger();

// Auto-delete log files older than the specified number of days
$delete_before_days = 7;
$MonoLogger->auto_delete_log(4);
$MonoLogger->display_logs();
echo "<pre>";

// Get the URL to access the log files
echo PHP_EOL . $MonoLogger->get_log_url();

// Log the start of the cron job
$MonoLogger->info("Cron starts");

// Fetch pending queues from resume_processing_queue
$sqlQueues = "SELECT rq.id,rq.role, rq.status , job.role_name , job.experience_years ,job.skills , job.description
    FROM resume_processing_queue rq
    INNER JOIN job_requirements job on job.id = rq.role
    WHERE rq.status = 'Processing' or rq.status = 'Regenerating'";
$resultQueues = $conn->query($sqlQueues);
$processed_count =0 ;
if ($resultQueues->num_rows > 0) {
    while ($rowQueue = $resultQueues->fetch_assoc()) {
        $queueId = $rowQueue['id'];
        $queueStatus = $rowQueue['status'];
        $role = $rowQueue['role_name'];
        $experience = $rowQueue['experience_years'];
        $skills = $rowQueue['skills'];
        $description = $rowQueue['description'];

        // Log the start of processing for the queue
        $MonoLogger->info("Processing queue with ID: $queueId");

        // Fetch pending resumes for the current queue from resume_insights
        $sqlResumes = "SELECT ri.id, ri.original_file_path,rc.resume_text , ri.status
                       FROM resume_insights ri
                       LEFT JOIN resume_content rc on ri.id = rc.resume_id
                       WHERE ri.queue_id = $queueId AND ri.status <> 'Completed'";
                       
        $resultResumes = $conn->query($sqlResumes);

        if ($resultResumes->num_rows > 0) {
            while ($rowResume = $resultResumes->fetch_assoc()) {
                $resumeId = $rowResume['id'];
                $resumePath = $rowResume['original_file_path'];
                $resumeStatus = $rowResume['status'];
                $resumeText = $rowResume['resume_text'];
                // Log the start of processing for the resume
                $MonoLogger->info("Processing resume with ID: $resumeId");
                if(empty($resumeText)){
                    $MonoLogger->info("Resume text is empty");
                    continue;
                }
                $screening_report = generate_screening_report($resumeText , $skills , $description ,  $experience);
                $processed_count++;
                if($processed_count > 30){
                    $MonoLogger->info("Api calls limit Per cron is exceeded");
                    continue;
                }
                $token_usage = null;
                if(isset($screening_report['success']) &&  $screening_report['success'] == true){
                    $token_usage = $screening_report['token_usage'];
                    $formatted_text =  $screening_report['text'];
                    $array = json_decode($formatted_text, true);
                    $screening_report['formatted_response'] = $array;
                    $resume_alignment_score = isset($array['resume_alignment_score']) ? $array['resume_alignment_score'] : null ;
                    $matched_skills = isset($array['matched_skills']) ? $array['matched_skills'] : null ;
                    $skills = isset($array['skill']) ? $array['skill'] : null ;

                    if(is_array($matched_skills)){
                        $matched_skills = implode(",",$matched_skills);
                    }
                    if(is_array($skills)){
                        $skills = implode(",",$skills);
                    }
                    $resume_tagline = isset($array['resume_tagline']) ? $array['resume_tagline'] : null;
                    $candidate_name = isset($array['candidate_name']) ? $array['candidate_name'] : null;
                    $work_experience = isset($array['work_experience_in_years']) ? $array['work_experience_in_years'] : null;
                    $resume_alignment_score = intval($resume_alignment_score);
                    $work_experience = intval($work_experience);
                    
                    $MonoLogger->info("Resume details fetched using chatgpt api");
                    $status = 'Completed'; 
                }
                else{
                    $status = 'Failed'; 
                    $MonoLogger->info("Failed to extract details from resume ");
                }
                $sqlUpdateResume = "UPDATE resume_insights 
                    SET status = ? , 
                        insights = ? ,
                        token_usage = ? ,
                        candidate_name = ? ,
                        resume_tagline = ? ,
                        experience = ? ,
                        skills = ? ,
                        matched_skills = ? , 
                        score = ? 
                    WHERE id = ?";
                $stmt = $conn->prepare($sqlUpdateResume);
                $stmt->bind_param("ssississii", 
                    $status , 
                    $formatted_text ,
                    $token_usage ,
                    $candidate_name , 
                    $resume_tagline ,
                    $work_experience ,
                    $skills ,
                    $matched_skills  ,
                    $resume_alignment_score , 
                    $resumeId
                );
                if ($stmt->execute()) {
                    // Insertion successful
                    $MonoLogger->info("Resume insights successfully updated in the system");
                } else {
                    $error = $stmt->error;
                    // Insertion failed
                    $MonoLogger->info("Failed to update in the system");
                    continue;
                }

                // Log the completion of processing for the resume
                $MonoLogger->info("Completed processing resume with ID: $resumeId");
            }
        }

        // Check if all resumes under the current queue are processed
        $sqlCountResumes = "SELECT COUNT(*) AS count
                            FROM resume_insights
                            WHERE queue_id = $queueId AND status <> 'Completed'";
        $countResult = $conn->query($sqlCountResumes);
        $countRow = $countResult->fetch_assoc();
        $resumeCount = $countRow['count'];
        // If all resumes are processed, update the status of the queue
        if ($resumeCount == 0) {
            $sqlUpdateQueue = "UPDATE resume_processing_queue SET status = 'Completed' WHERE id = $queueId";
            $conn->query($sqlUpdateQueue);
        }

        // Log the completion of processing for the queue
        $MonoLogger->info("Completed processing queue with ID: $queueId");
    }
}

// Log the completion of the cron job
$MonoLogger->info("Cron job completed");