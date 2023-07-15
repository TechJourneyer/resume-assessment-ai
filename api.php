<?php

require_once 'constants.php'; // Include the Guzzle library


function chatgpt_api($prompt,$model="gpt-3.5-turbo",$max_tokens = 1000 , $temperature = 0.5){
    
    $apiKey = OPENAI_API_KEY;
    $url = 'https://api.openai.com/v1/chat/completions';

    $headers = array(
        "Authorization: Bearer {$apiKey}",
        "Content-Type: application/json"
    );

    // Define messages
    $messages = array();
    $messages[] = array("role" => "user", "content" => $prompt);

    // Define data
    $data = array();
    $data["model"] = $model;
    $data["messages"] = $messages;
    $data["max_tokens"] = $max_tokens;

    // init curl
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    $result = curl_exec($curl);
    if (curl_errno($curl)) {
         $error_msg = 'Error:' . curl_error($curl);
         curl_close($curl);
         return [
            "success" => false,
            "text" => null,
            "response" => null,
            "error_message" =>$error_msg,
        ];
    } else {
        curl_close($curl);
        $response_array = json_decode($result,true);
        $token_usage = $response_array['usage']['total_tokens'];
        $completion = $response_array['choices'][0]['message']['content'];
        return [
            "success" => true,
            "text" => $completion,
            "response" => $response_array,
            "error_message" =>"",
            "token_usage" => $token_usage
        ];
    }
}
// assess the candidate's understanding of fundamental concepts
function resume_report_labels(){
    return [
        "resume_summary" => "Summary of the resume",
        "resume_tagline" => "Short Oneline Resume Tagline",
        "interview_questions" => "Please provide a arraay list of 10 specific technical, logical, or practical interview questions that focus on logical reasoning or candidate's technical skills. Avoid asking generic questions about experience. Instead, ask questions that delve into specific topics and assess the candidate's understanding and problem-solving abilities. 
        If user has experiance of mysql and javascript examples of questions you can ask like : 
        1. What is indexes in Mysql
        2. Can you explain how an AJAX call works and its purpose in web development?",
        "certifications" => "List of certification names",
        "work_experience_in_years" => "Years of work experience , return 0 in case of freshers",
        "timeline" => "Create timeline in descending order based on the candidate's work, education and certifications and it should be in following json array format having type,event,time in each row [{ 'type': 'certification', 'event': 'Certified Scrum Master', 'time': 'July 2021'}]",
        "skill" => "List of candidate's technical skills",
        "candidate_name" => "Candidate name"
    ];
}

function screening_report_labels(){
    $resume_report_labels = resume_report_labels();

    $screening_report_labels =  [
        "skills_matching_percentage" => "use this formula to calculate the percentage : (matched_skills/no_of_required_skills) * 100",
        "job_desc_matching_percentage" => "Matching percentage of job description",
        "matched_skills" => "List of candidates skills matched with the requirement",
        "expected_experience_matching_percentage" => "Matching percentage of expected experience",
        "resume_alignment_score" => "Average score (out of 100) based on all matching percentages",
        // "resume_alignment_score_explaination" => "Explaination on all scores and resume alignment score",
    ];
    return array_merge($resume_report_labels , $screening_report_labels );
}

function report_params($skills,$required_experience,$job_description){
    $expected_skills_array = explode( ",", $skills );
    $expected_skills_arr_format = [];
    foreach($expected_skills_array as $expected_sk){
        $expected_skills_arr_format[] = [
            "skills" => $expected_sk,
            "matched" => "{return true if candidate has this skill}",
        ];
    }
    $skills_fomrat_array = [
        "candidates_skills" => [
            [
                "skill_name" => "{skill_name}",
                "matched_with_requirement" => "{return true if this skill is relevant to job_role_expectations }",
            ]
        ] ,
        // "expected_skills" => $expected_skills_arr_format ,
        "overall_expected_skills_matching_score" => '{score out of 100}' ,
        "overall_candidates_skills_matching_score" => '{score out of 100}'
    ];

    $skills_format = json_encode($skills_fomrat_array);

    $timeline_array = [
        [
            'type' => 'certification',
            'event' => 'Certified Scrum Master',
            'time' => 'July 2021', 
        ]
    ];
    $timeline_format = json_encode($timeline_array);

    return [
        "resume_summary" => "{Summary of the resume}",
        "resume_tagline" => "{Short headline for the resume}",
        "candidate_name" => "{Candidate name}",
        "interview_questions" => "Please provide a arraay list of atleast 10 specific technical, logical interview questions that focus on logical reasoning or candidate's technical skills. Avoid asking generic questions about experience. Instead, ask questions that delve into specific topics and assess the candidate's understanding and problem-solving abilities. 
        If user has experiance of mysql and javascript examples of questions you can ask like : 
        1. Can you explain how an AJAX call works and its purpose in web development?",
        "certifications" => "List of certification names",
        "work_experience_in_years" => "Years of work experience, return 0 if experience does not match the role criteria (PHP Developer) or if the candidate is a fresher",
        "timeline" => "Create timeline in descending order based on the candidate's work, education and certifications and it should be in following json array format having type,event,time in each row : $timeline_format",
        "skill_assessment" => "Please extract the candidate's technical skills from the Resume and Provide the result in the following format : $skills_format",
        "minimum_experiance_criteria_met" => "Check if candidate has minimum experience of $required_experience years",
        "job_desc_matching_percentage" => "{check if candidate is aligned with job description from [Job Role Expectations]  , calculate the score in percentage}",
        "resume_alignment_score" => "Calculate the Average score (out of 100) based on all criterias"
    ];
}

function scan_resume($pdf_text){
    $labels = resume_report_labels();
    $prompt = "Here's what I found in the resume:\n```\n$pdf_text\n```\n\nPlease provide the following details:\n- " . implode("\n- ", array_values($labels)); 
    $response = chatgpt_api($prompt,"gpt-3.5-turbo",2500);
    if(!isset($response['success']) ||  $response['success'] == false){
        return false;
    }
    return $response['text'];
}


function generate_screening_report2($scan_result , $skills , $job_description ,  $required_experience){
    $expectation_array = [
        "skills" => $skills,
        "job_description" => $job_description,
        "minimum_experience_required" => $required_experience,
    ];
    $expectation = json_encode($expectation);
    $screening_report_labels = report_params($skills,$required_experience,$job_description);
    $prompt = "[Resume Details]\n"
    . "<<<\n"
    . $scan_result
    . "\n>>>\n\n"

    . "[Job Role Expections]\n"
    . "<<<\n"
    . $expectation
    . "\n>>>\n\n"

    . "[Question]\n Based on the provided resume details, please generate a report that compares it to the job role criteria. Please provide the report in the following JSON format:\n"
    . json_encode($screening_report_labels, JSON_PRETTY_PRINT);
    writeLog("generate_screening_report prompt : " . $prompt);
    $prompt_response = chatgpt_api($prompt,"gpt-3.5-turbo",1500);
    writeLog("generate_screening_report  response : " . json_encode($prompt_response));
    if(!isset($prompt_response['success']) ||  $prompt_response['success'] == false){
        return false;
    }
    return $prompt_response;
}

function generate_screening_report1($scan_result , $skills , $job_description ,  $required_experience){
    $expectation_array = [
        "skills" => $skills,
        "job_description" => $job_description,
        "minimum_experience_required" => $required_experience,
    ];
    $expectation = json_encode($expectation_array);
    // print_r($expectation);
    // exit;
    $screening_report_labels = report_params($skills,$required_experience,$job_description);
    $prompt_content_array = [
        "resume_details" => $scan_result,
        "job_role_expectations" => $expectation, 
    ];

    $prompt = "[Content]\n"
    . json_encode($prompt_content_array)
    . "\n"
    . "[Instruction]\n Based on the provided resume details, please generate a report that compares it to the job role criteria. Please provide the report in the following JSON format:\n"
    . json_encode($screening_report_labels, JSON_PRETTY_PRINT);
    writeLog("generate_screening_report prompt : " . $prompt);
    $prompt_response = chatgpt_api($prompt,"gpt-3.5-turbo",1500);
    writeLog("generate_screening_report  response : " . json_encode($prompt_response));
    if(!isset($prompt_response['success']) ||  $prompt_response['success'] == false){
        return false;
    }
    return $prompt_response;
}

function generate_screening_report($scan_result , $skills , $job_description ,  $required_experience){
    $screening_report_labels = screening_report_labels();
    $prompt = "Content : "
    . "Resume Details - \n"
    . "```\n"
    . $scan_result
    . "\n```\n\n"
    . "Job Requirements : \n"
    . "```\n"
    . "- Required Skills : $skills\n"
    . "- Job Description : $job_description\n"
    . "- Required Experience : $required_experience years\n\n"
    . "\n```\n\n"
    . "Question : Based on the provided resume details, please generate a report that compares it to the job requirements. Please provide the report in the following JSON format:\n"
    . json_encode($screening_report_labels, JSON_PRETTY_PRINT);
    writeLog("generate_screening_report prompt : " . $prompt);
    $prompt_response = chatgpt_api($prompt,"gpt-3.5-turbo",1500);
    writeLog("generate_screening_report  response : " . json_encode($prompt_response));
    if(!isset($prompt_response['success']) ||  $prompt_response['success'] == false){
        return false;
    }
    return $prompt_response;
}