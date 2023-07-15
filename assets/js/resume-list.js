$(document).ready(function() {
    var dataTable = $('#fileTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "resume-list-dt.php",
            type: "POST",
            data: function(d) {
                d.queue_id = getParameterByName('queue_id');
                d.role = $('#filter-job-role').val();
                d.status = $('#filter-status').val();
            }
        },
        "columns": [
            { "data": "queue_id" },
            { "data": "original_file_path" },
            { "data": "role_name" },
            { "data": "candidate_name" },
            { "data": "experience" },
            { "data": "score" },
            { "data": "status" },
            { "data": "insights" },
            { "data": "created_date" }
        ],
        "order": [[0, "desc"]],
        "pageLength": 10,
        "lengthMenu": [10, 25, 50, 100],
        "pagingType": "full_numbers",
        "language": {
            "search": "",
            "searchPlaceholder": "Search...",
            "lengthMenu": "_MENU_ records per page",
            "zeroRecords": "No matching records found",
            "info": "Showing _START_ to _END_ of _TOTAL_ records",
            "infoEmpty": "Showing 0 to 0 of 0 records",
            "infoFiltered": "(filtered from _MAX_ total records)",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });

    $('#fileTable').on('click', '.regenerate-button', function() {
        var resumeId = $(this).data('id');
        var button = $(this);
        
        // Show loader icon
        button.html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Make AJAX call
        $.ajax({
            url: 'resume-report-regenerate.php', // Replace with the actual PHP file path
            method: 'POST',
            data: { resume_id: resumeId },
            success: function(response) {
                alert(response.message);
                dataTable.draw(); // Refresh the datatable
                // Handle success response
                // Update the button or perform any other necessary actions
            },
            error: function(xhr, status, error) {
                alert(error);
                // Handle error response
                // Display an error message or perform any other necessary actions
            }
        });
    });
    // AJAX call on role select change
    $('#filter-job-role').change(function() {
        var selectedRole = $(this).val();
        if(selectedRole == ''){
            $('#jobDescription').val('');
            $('#experience').val('');
            $('#skills').val('');
            return false;
        }

        // Make an AJAX request to fetch job description, experience, and skills for the selected role
        $.ajax({
            url: 'fetch_job_details.php',
            type: 'POST',
            data: {
                role: selectedRole
            },
            dataType: 'json',
            success: function(response) {
                // Auto-fill the job description, experience, and skills fields on the frontend page
                $('#jobDescription').val(response.description);
                $('#experience').val(response.experience);
                $('#skills').val(response.skills);
            }
        });
    });

    // Get the value of a query parameter by name
    function getParameterByName(name) {
        var url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    // Attach click event to the clear button
    $('#clearButton').click(function() {
        clearFilters();
    });

    $('#searchButton').click(function() {
        dataTable.draw(); // Refresh the datatable
    });
});

function clearFilters() {
    $('#filter-job-role').val('');
    $('#jobDescription').val('');
    $('#experience').val('');
    $('#skills').val('');
    $('#filter-status').val('');
}