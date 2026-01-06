<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Offices</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Offices</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Offices</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- ADD MODAL -->
        <div class="modal fade" id="AddOffice" tabindex="-1" aria-labelledby="AddOfficeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="SaveOfficeForm" autocomplete="off">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="AddOfficeLabel">New Office</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="office_type_id">Office type</label>
                    <select class="form-control" name="office_type_id" id="office_type_id" required>
                      <option value="" disabled selected>Select office type</option>
                      <?php
                      $types = $db->getOfficeTypes();
                      if ($types) {
                        while ($t = $types->fetch_assoc()) {
                      ?>
                          <option value="<?= $t['office_type_id']; ?>"><?= htmlspecialchars($t['office_type_name']); ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="office_name">Office name</label>
                    <input type="text" class="form-control" name="office_name" id="office_name" placeholder="e.g. College of Engineering" required>
                  </div>
                  <div class="form-group">
                    <label for="office_email">Office email</label>
                    <input type="email" class="form-control" name="office_email" id="office_email" placeholder="Email address" required pattern="[^@\s]+@ustp\.edu\.ph" title="Email must be a valid USTP address ending with @ustp.edu.ph">
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
        <div class="modal fade" id="UpdateOffice" tabindex="-1" aria-labelledby="UpdateOfficeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="UpdateOfficeForm" autocomplete="off">
              <input type="hidden" name="office_id" id="edit_office_id">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="UpdateOfficeLabel">Update Office</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="edit_office_type_id">Office type</label>
                    <select class="form-control" name="office_type_id" id="edit_office_type_id" required>
                      <option value="" disabled>Select office type</option>
                      <?php
                      $types2 = $db->getOfficeTypes();
                      if ($types2) {
                        while ($t2 = $types2->fetch_assoc()) {
                      ?>
                          <option value="<?= $t2['office_type_id']; ?>"><?= htmlspecialchars($t2['office_type_name']); ?></option>
                      <?php
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="edit_office_name">Office name</label>
                    <input type="text" class="form-control" name="office_name" id="edit_office_name" required>
                  </div>
                  <div class="form-group">
                    <label for="edit_office_email">Office email</label>
                    <input type="email" class="form-control" name="office_email" id="edit_office_email" required pattern="[^@\s]+@ustp\.edu\.ph" title="Email must be a valid USTP address ending with @ustp.edu.ph">
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
          <button data-toggle="modal" data-target="#AddOffice" class="btn btn-sm btn-primary mb-3 ml-3 mt-1"><i class="fa fa-plus"></i> Add</button>
          <button id="delete-selected" class="btn btn-sm btn-danger mb-3 mt-1"><i class="fa fa-trash"></i> Delete</button>
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all" class="d-block m-auto"></th>
                <th>OFFICE TYPE</th>
                <th>OFFICE NAME</th>
                <th>EMAIL</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $offices = $db->getOfficesWithType();
              if ($offices) {
                while ($row2 = $offices->fetch_assoc()) {
              ?>
                  <tr>
                    <td><input type="checkbox" class="select-record d-block m-auto" value="<?= $row2['office_id'] ?>"></td>
                    <td><?= htmlspecialchars($row2['office_type_name']); ?></td>
                    <td><?= htmlspecialchars($row2['office_name']); ?></td>
                    <td><?= htmlspecialchars($row2['office_email']); ?></td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm edit-office"
                        data-office-id="<?= $row2['office_id']; ?>"
                        data-office-type-id="<?= $row2['office_type_id']; ?>"
                        data-office-name="<?= htmlspecialchars($row2['office_name']); ?>"
                        data-office-email="<?= htmlspecialchars($row2['office_email']); ?>">
                        <i class="fa fa-edit"></i>
                      </button>
                    </td>
                  </tr>
              <?php
                }
              }
              ?>
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

    $('#SaveOfficeForm').submit(function (e) {
      e.preventDefault();

      var emailVal = ($('#office_email').val() || '').trim();
      var ustpPattern = /@ustp\.edu\.ph$/i;
      if (!ustpPattern.test(emailVal)) {
        showSweetAlert("Invalid Email", "Office email must be a valid USTP address ending with @ustp.edu.ph.", "warning");
        return;
      }

      var formData = new FormData($(this)[0]);
      formData.append('action', 'AddOffice');

      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.success) {
            showSweetAlert("Success!", response.message, "success", "offices.php");
          } else {
            showSweetAlert("Error", response.message, "error");
          }
        }, error: function (xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });

    $('#datatable').on('click', '.edit-office', function () {
      const id = $(this).data('office-id');
      const typeId = $(this).data('office-type-id');
      const name = $(this).data('office-name');
      const email = $(this).data('office-email');
      $('#edit_office_id').val(id);
      $('#edit_office_type_id').val(typeId);
      $('#edit_office_name').val(name);
      $('#edit_office_email').val(email);

      $('#UpdateOffice').modal('show');
    });

    $('#UpdateOfficeForm').submit(function (e) {
      e.preventDefault();

      var emailVal = ($('#edit_office_email').val() || '').trim();
      var ustpPattern = /@ustp\.edu\.ph$/i;
      if (!ustpPattern.test(emailVal)) {
        showSweetAlert("Invalid Email", "Office email must be a valid USTP address ending with @ustp.edu.ph.", "warning");
        return;
      }

      var formData = new FormData($(this)[0]);
      formData.append('action', 'UpdateOffice');

      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.success) {
            showSweetAlert("Success!", response.message, "success", "offices.php");
          } else {
            showSweetAlert("Error", response.message, "error");
          }
        }, error: function (xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });

    // Helper function to escape HTML
    function htmlEscape(text) {
      return $('<div>').text(text).html();
    }

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
        deleteMultipleRecords("tbloffice", "office_id", selectedIDs, "offices.php");
      });
    });

  });
</script>
