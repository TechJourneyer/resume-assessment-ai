<?php

require_once 'constants.php'; 
require_once 'api.php'; 
require_once 'functions.php'; 
// error_reporting(0);

try {
    if(isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
        $skills = isset($_POST['skills']) && !empty($_POST['skills']) ? implode(",", $_POST['skills']) : "";
        $job_description = $_POST['job_description'];
        $required_experience = $_POST['experience'];
    
        $pdf_file = $_FILES['pdf_file']['name'];
        $pdf_tmp = $_FILES['pdf_file']['tmp_name'];
        $pdf_size = $_FILES['pdf_file']['size'];
        $pdf_ext = pathinfo($pdf_file, PATHINFO_EXTENSION);
        $pdf_name = pathinfo($pdf_file, PATHINFO_FILENAME);
        $allowed_exts = array("pdf");
    
        // Check if the file type is allowed
        if(in_array($pdf_ext, $allowed_exts)) {
            // Convert the pdf to text format
            $pdf_text = pdfToText($pdf_tmp);
    
            if(!isset($pdf_text) || empty($pdf_text)){
                show_response(false ,  [],"Failed to parse PDF file");
            }

            $screening_report = generate_screening_report1($pdf_text , $skills , $job_description ,  $required_experience);
           
            if(isset($screening_report['success']) &&  $screening_report['success'] == true){
                $formatted_text =  $screening_report['text'];
                $array = json_decode($formatted_text, true);
                $screening_report['formatted_response'] = $array;
                // ob_start();
                // echo "<pre>";
                // print_r($array);
                // $html = ob_get_clean();
                $html = show_resume_report1($array);
                show_response(true ,  $html ,"",$screening_report);
            }
            else{
                show_response(false ,  [],"Failed to fetch response from Chatgpt API");
            }
            
            
        } else {
            show_response(false ,  [],"Invalid file type. Only PDF files are allowed!");
        }
    }
} catch (\Throwable $th) {
    show_response(false ,  [],"FAILED : ".$th->getMessage());
}

?>
