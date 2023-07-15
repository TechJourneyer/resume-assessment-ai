// add event listener to the submit button
$(document).ready(function(){
    $('#expectedSkills').select2({
        placeholder: 'Select skills',
        tags: true,
        tokenSeparators: [',', ' ']
    });
    $('#upload-form button[type="submit"]').on('click', function(event) {
        event.preventDefault(); // prevent default form submission behavior
        
        var form = $('#upload-form');
        if (form[0].checkValidity() === false) {
            event.stopPropagation();
            form.addClass('was-validated');
            return;
        }
        var formData = new FormData(form[0]); // create FormData object from the form

        $('.loader').show();
        $('#resume-assessment-report').html('');
        $('#resume-assessment-report').hide();
        $('#analyze_resume_button').hide();

        $.ajax({
            url: 'resume_upload.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        })
        .done(function(data) {
            let jsondata = JSON.parse(data);
            console.log(jsondata); // handle the server response
            
            $('.loader').hide();
            $('#resume-assessment-report').show();
            $('#analyze_resume_button').show();
            if(jsondata.success){
                $('#resume-assessment-report').html(jsondata.html);
            }
        })
        .fail(function(error) {
            $('.loader').hide();
            $('#resume-assessment-report').show();
            $('#analyze_resume_button').show();
            console.error(error);
        });
    });
});