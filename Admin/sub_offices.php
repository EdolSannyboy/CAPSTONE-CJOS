<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Sub Offices</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Departments</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Sub Offices / Departments</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <?php
        $conn = $db->getConnection();

        // Fetch offices for dropdown
        $offices_result = $conn->query("SELECT office_id, office_name FROM tbloffice ORDER BY office_name ASC");

        // Fetch existing departments with office name and email
        $departments_result = $conn->query("SELECT u.office_under_id, u.office_id, u.office_under_name, u.office_under_email, o.office_name
                                            FROM tbl_office_under u
                                            INNER JOIN tbloffice o ON u.office_id = o.office_id
                                            ORDER BY o.office_name ASC, u.office_under_name ASC");
        ?>

        <!-- ADD MODAL -->
        <div class="modal fade" id="AddSubOffice" tabindex="-1" aria-labelledby="AddSubOfficeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="SaveSubOfficeForm" autocomplete="off">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="AddSubOfficeLabel">New Sub Offices / Departments</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="office_id_select">Office name</label>
                    <select class="form-control" name="office_id" id="office_id_select" required>
                      <option value="" disabled selected>Select office</option>
                      <?php if ($offices_result && $offices_result->num_rows > 0): ?>
                        <?php while ($o = $offices_result->fetch_assoc()): ?>
                          <option value="<?= $o['office_id']; ?>"><?= htmlspecialchars($o['office_name']); ?></option>
                        <?php endwhile; ?>
                      <?php endif; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Departments</label>
                    <div id="add_departments_container">
                      <div class="department-input mb-2">
                        <div class="input-group mb-1">
                          <input type="text" class="form-control office_under_name" placeholder="e.g. Department of IT">
                          <div class="input-group-append">
                            <button type="button" class="btn btn-success btn-sm add-department"><i class="fa fa-plus"></i></button>
                          </div>
                        </div>
                        <input type="email" class="form-control office_under_email" placeholder="e.g. dept@ustp.edu.ph" pattern="[^@\s]+@ustp\.edu\.ph">
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm" id="submit_button"><i class="fa fa-save"></i> Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- UPDATE MODAL -->
        <div class="modal fade" id="UpdateSubOffice" tabindex="-1" aria-labelledby="UpdateSubOfficeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="UpdateSubOfficeForm" autocomplete="off">
              <input type="hidden" name="office_under_id" id="edit_office_under_id">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="UpdateSubOfficeLabel">Update Sub Office / Department</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="edit_office_id_select">Office name</label>
                    <select class="form-control" name="office_id" id="edit_office_id_select" required>
                      <option value="" disabled>Select office</option>
                      <?php
                      // Re-run offices query for edit dropdown
                      $offices_result_edit = $conn->query("SELECT office_id, office_name FROM tbloffice ORDER BY office_name ASC");
                      if ($offices_result_edit && $offices_result_edit->num_rows > 0):
                        while ($o2 = $offices_result_edit->fetch_assoc()): ?>
                          <option value="<?= $o2['office_id']; ?>"><?= htmlspecialchars($o2['office_name']); ?></option>
                        <?php endwhile;
                      endif; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="edit_office_under_name">Department name</label>
                    <input type="text" class="form-control" name="office_under_name" id="edit_office_under_name" required>
                  </div>
                  <div class="form-group">
                    <label for="edit_office_under_email">Department email</label>
                    <input type="email" class="form-control" name="office_under_email" id="edit_office_under_email" required pattern="[^@\s]+@ustp\.edu\.ph">
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm" id="edit_submit_button"><i class="fa fa-save"></i> Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <div class="card-box bg-white table-responsive pb-2">
          <button data-toggle="modal" data-target="#AddSubOffice" class="btn btn-sm btn-primary mb-3 ml-3 mt-1"><i class="fa fa-plus"></i> Add</button>
          <button id="delete-selected" class="btn btn-sm btn-danger mb-3 mt-1"><i class="fa fa-trash"></i> Delete</button>
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all" class="d-block m-auto"></th>
                <th>OFFICE NAME</th>
                <th>DEPARTMENT NAME</th>
                <th>DEPARTMENT EMAIL</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($departments_result && $departments_result->num_rows > 0): ?>
                <?php while ($row = $departments_result->fetch_assoc()): ?>
                  <tr>
                    <td><input type="checkbox" class="select-record d-block m-auto" value="<?= $row['office_under_id'] ?>"></td>
                    <td><?= htmlspecialchars($row['office_name']); ?></td>
                    <td><?= htmlspecialchars($row['office_under_name']); ?></td>
                    <td><?= htmlspecialchars($row['office_under_email'] ?? ''); ?></td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm edit-sub-office"
                        data-office-under-id="<?= $row['office_under_id']; ?>"
                        data-office-id="<?= $row['office_id']; ?>"
                        data-office-under-name="<?= htmlspecialchars($row['office_under_name']); ?>"
                        data-office-under-email="<?= htmlspecialchars($row['office_under_email'] ?? ''); ?>">
                        <i class="fa fa-edit"></i>
                      </button>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php require_once 'footer.php'; ?>
<script>
  $(document).ready(function () {

    // Handle dynamic add/remove of department inputs in Add modal
    $(document).on('click', '.add-department', function (e) {
      e.preventDefault();
      var container = $('#add_departments_container');
      var newInput = $('<div class="department-input mb-2">' +
        '<div class="input-group mb-1">' +
        '<input type="text" class="form-control office_under_name" placeholder="e.g. Department of IT">' +
        '<div class="input-group-append">' +
        '<button type="button" class="btn btn-danger btn-sm remove-department"><i class="fa fa-trash"></i></button>' +
        '</div>' +
        '</div>' +
        '<input type="email" class="form-control office_under_email" placeholder="e.g. dept@ustp.edu.ph" pattern="[^@\\s]+@ustp\\.edu\\.ph">' +
        '</div>');
      container.append(newInput);
    });

    $(document).on('click', '.remove-department', function (e) {
      e.preventDefault();
      $(this).closest('.department-input').remove();
    });

    // Save multiple departments
    $('#SaveSubOfficeForm').submit(function (e) {
      e.preventDefault();

      var officeId = $('#office_id_select').val();
      if (!officeId) {
        showSweetAlert("Validation", "Please select an office.", "warning");
        return;
      }

      var deptData = [];
      var invalid = false;
      var ustpPattern = /@ustp\.edu\.ph$/i;

      $('#add_departments_container .department-input').each(function () {
        var name = $(this).find('.office_under_name').val().trim();
        var email = $(this).find('.office_under_email').val().trim();

        if (name === '' && email === '') {
          return; // skip completely empty rows
        }

        if (name === '' || email === '') {
          invalid = true;
          showSweetAlert("Validation", "Each department must have both a name and an email.", "warning");
          return false; // break each()
        }

        if (!ustpPattern.test(email)) {
          invalid = true;
          showSweetAlert("Invalid Email", "Department email must be a valid USTP address ending with @ustp.edu.ph.", "warning");
          return false;
        }

        deptData.push({
          office_under_name: name,
          office_under_email: email
        });
      });

      if (invalid) {
        return;
      }

      if (deptData.length === 0) {
        showSweetAlert("Validation", "Please add at least one department.", "warning");
        return;
      }

      var formData = new FormData($(this)[0]);
      formData.append('action', 'AddSubOffices');
      formData.append('office_under_names', JSON.stringify(deptData));

      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
          try {
            if (typeof response === 'string') {
              response = JSON.parse(response);
            }
          } catch (e) {
            showSweetAlert("Error", "Unexpected response from server while saving departments.", "error");
            return;
          }

          if (response && response.success) {
            showSweetAlert("Success!", response.message, "success", "sub_offices.php");
          } else if (response && response.message) {
            showSweetAlert("Error", response.message, "error");
          } else {
            showSweetAlert("Error", "Failed to save departments. Please try again.", "error");
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText || error);
          showSweetAlert("Error", "A server error occurred while saving departments.", "error");
        }
      });
    });

    // Open edit modal
    $('#datatable').on('click', '.edit-sub-office', function () {
      const id = $(this).data('office-under-id');
      const officeId = $(this).data('office-id');
      const name = $(this).data('office-under-name');
      const email = $(this).data('office-under-email');

      $('#edit_office_under_id').val(id);
      $('#edit_office_id_select').val(officeId);
      $('#edit_office_under_name').val(name);
      $('#edit_office_under_email').val(email);

      $('#UpdateSubOffice').modal('show');
    });

    // Update single department
    $('#UpdateSubOfficeForm').submit(function (e) {
      e.preventDefault();

      var formData = new FormData($(this)[0]);
      formData.append('action', 'UpdateSubOffice');

      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (response) {
          try {
            if (typeof response === 'string') {
              response = JSON.parse(response);
            }
          } catch (e) {
            showSweetAlert("Error", "Unexpected response from server while updating the department.", "error");
            return;
          }

          if (response && response.success) {
            showSweetAlert("Success!", response.message, "success", "sub_offices.php");
          } else if (response && response.message) {
            showSweetAlert("Error", response.message, "error");
          } else {
            showSweetAlert("Error", "Failed to update department. Please try again.", "error");
          }
        },
        error: function (xhr, status, error) {
          console.error(xhr.responseText || error);
          showSweetAlert("Error", "A server error occurred while updating the department.", "error");
        }
      });
    });

    // Multi-select handling
    $('#select-all').on('click', function () {
      var isChecked = $(this).is(':checked');
      $('.select-record').prop('checked', isChecked);
    });

    $('#datatable').on('change', '.select-record', function () {
      if (!$(this).is(':checked')) {
        $('#select-all').prop('checked', false);
      } else {
        var allChecked = $('.select-record').length === $('.select-record:checked').length;
        $('#select-all').prop('checked', allChecked);
      }
    });

    // Multi delete (uses generic delete_Record in processes.php)
    $('#delete-selected').on('click', function () {
      var selectedIDs = [];
      $('.select-record:checked').each(function () {
        selectedIDs.push($(this).val());
      });

      if (selectedIDs.length === 0) {
        Swal.fire("No selection", "Please select at least one record to delete.", "info");
        return;
      }

      confirmMultipleDeletion(selectedIDs.length, function () {
        deleteMultipleRecords("tbl_office_under", "office_under_id", selectedIDs, "sub_offices.php");
      });
    });

  });
</script>
