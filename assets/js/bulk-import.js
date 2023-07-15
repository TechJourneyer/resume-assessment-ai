$(document).ready(function() {

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
        $('#upload_files_button').hide();

        $.ajax({
            url: 'bulk-import-submit.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
        })
        .done(function(data) {
            let jsondata = JSON.parse(data);
            console.log(jsondata); // handle the server response
            alert(jsondata.message);
            $('.loader').hide();
            location.reload();
        })
        .fail(function(error) {
            $('.loader').hide();
            $('#upload_files_button').show();
            console.error(error);
        });
    });

    // AJAX call on role select change
    $('#roleSelect').change(function() {
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


    var dataTable = $('#resume_queue_table').DataTable({
        // "responsive": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            url: "bulk-import-dt.php",
            type: "POST"
        },
        "columns": [
            { "data": "id",
              "render": function (data, type, row, meta) {
                  return '<a href="resume-list.php?queue_id=' + row.id + '">' + data + '</a>';
              }
            },
            { "data": "status" },
            { "data": "role" },
            { "data": "username" },
            { "data": "created_date" },
            { "data": "file_count" }
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
});