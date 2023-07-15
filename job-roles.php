<?php require_once 'templates/header.php'; ?>
<?php authentication(); ?>

<div class="container">

    <h3>Add New Role</h3>
    <form id="addRoleForm" method="post" action="add_new_role.php">
        <div class="form-group">
            <label for="roleName">Role Name</label>
            <input type="text" class="form-control" id="roleName" name="roleName" required>
        </div>
        <div class="form-group">
            <label for="qualification">Qualification</label>
            <input type="text" class="form-control" id="qualification" name="qualification" required>
        </div>
        <div class="form-group">
            <label for="experienceYears">Experience (in years)</label>
            <input type="number" class="form-control" id="experienceYears" name="experienceYears" required>
        </div>
        <div class="form-group">
            <label for="skills">Skills</label>
            <input type="text" class="form-control" id="skills" name="skills" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary" id="addRoleBtn">Add Role</button>
    </form>

    <h3>Existing Roles</h3>
    <hr>
    <div class="table-responsive">
        <table class="table table-sm" id="rolesTable">
            <thead>
                <tr>
                    <th>Role Name</th>
                    <th>Qualification</th>
                    <th>Experience (years)</th>
                    <th>Skills</th>
                    <th>Description</th>
                    <th>Added By</th>
                    <th>Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        var dataTable = $('#rolesTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "job-roles-dt.php",
                type: "POST",
                data: function(d) {
                    
                }
            },
            "columns": [
                { "data": "role_name" },
                { "data": "qualification" },
                { "data": "experience_years" },
                { "data": "skills" },
                { "data": "description" },
                { "data": "created_by" },
                { "data": "created_at" },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        // Render edit, delete, and view description buttons
                        return '<button class="editRoleBtn btn btn-primary" data-id="' + row.id + '">Edit</button>' ;
                    }
                }
            ],
            "order": [[6, "desc"]],
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
        // Submit the add role form
        $('#addRoleForm').submit(function(event) {
            event.preventDefault();

            var form = $(this);
            var url = form.attr('action');
            var data = form.serialize();

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response) {
                    // Clear the form inputs
                    form[0].reset();

                    // Fetch the updated list of roles
                    fetchRoles();
                }
            });
        });

        // Edit role button click event
        $(document).on('click', '.editRoleBtn', function() {
            var roleId = $(this).data('id');
            window.location.href = 'edit_role.php?id=' + roleId;
        });

        // Delete role button click event
        $(document).on('click', '.deleteRoleBtn', function() {
            var roleId = $(this).data('id');
        });

        // View description button click event
        $(document).on('click', '.viewDescriptionBtn', function() {
            var description = $(this).data('description');
            showDescriptionPopup(description);
        });


    });

    // Function to display the popup with complete description
    function showDescriptionPopup(description) {
        // Set the description content in the modal
        $('#descriptionModal .modal-body').text(description);
        // Show the modal
        $('#descriptionModal').modal('show');
    }

</script>
<!-- Modal -->
<div class="modal fade" id="descriptionModal" tabindex="-1" role="dialog" aria-labelledby="descriptionModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="descriptionModalLabel">Complete Description</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <!-- Description content will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once 'templates/footer.php'; ?>
