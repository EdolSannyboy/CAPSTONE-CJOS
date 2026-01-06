<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Office Types</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Office Types</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Manage Office Types</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content">

        <!-- ADD MODAL -->
        <div class="modal fade" id="AddOfficeType" tabindex="-1" aria-labelledby="AddOfficeTypeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="SaveOfficeTypeForm" autocomplete="off">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="AddOfficeTypeLabel">New Office Type</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="office_type_name">Office type name</label>
                    <input type="text" class="form-control" name="office_type_name" id="office_type_name" placeholder="e.g. College, Office, Institute" required>
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
        <div class="modal fade" id="UpdateOfficeType" tabindex="-1" aria-labelledby="UpdateOfficeTypeLabel" aria-hidden="true">
          <div class="modal-dialog">
            <form id="UpdateOfficeTypeForm" autocomplete="off">
              <input type="hidden" name="office_type_id" id="edit_office_type_id">
              <div class="modal-content">
                <div class="modal-header bg-light">
                  <h5 class="modal-title" id="UpdateOfficeTypeLabel">Update Office Type</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fa fa-times-circle"></i></span>
                  </button>
                </div>
                <div class="modal-body">
                  <div class="form-group">
                    <label for="edit_office_type_name">Office type name</label>
                    <input type="text" class="form-control" name="office_type_name" id="edit_office_type_name" required>
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
          <button data-toggle="modal" data-target="#AddOfficeType" class="btn btn-sm btn-primary mb-3 ml-3 mt-1"><i class="fa fa-plus"></i> Add</button>
          <button id="delete-selected" class="btn btn-sm btn-danger mb-3 mt-1"><i class="fa fa-trash"></i> Delete</button>
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all" class="d-block m-auto"></th>
                <th>OFFICE TYPE NAME</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $types = $db->getOfficeTypes();
              if ($types) {
                while ($row2 = $types->fetch_assoc()) {
              ?>
                  <tr>
                    <td><input type="checkbox" class="select-record d-block m-auto" value="<?= $row2['office_type_id'] ?>"></td>
                    <td><?= htmlspecialchars($row2['office_type_name']); ?></td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm edit-office-type"
                        data-office-type-id="<?= $row2['office_type_id']; ?>"
                        data-office-type-name="<?= htmlspecialchars($row2['office_type_name']); ?>">
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

    $('#SaveOfficeTypeForm').submit(function (e) {
      e.preventDefault();
      var formData = new FormData($(this)[0]);
      formData.append('action', 'AddOfficeType');
      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.success) {
            showSweetAlert("Success!", response.message, "success", "office_types.php");
          } else {
            showSweetAlert("Error", response.message, "error");
          }
        }, error: function (xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });

    $('#datatable').on('click', '.edit-office-type', function () {
      const id = $(this).data('office-type-id');
      const name = $(this).data('office-type-name');
      $('#edit_office_type_id').val(id);
      $('#edit_office_type_name').val(name);
      $('#UpdateOfficeType').modal('show');
    });

    $('#UpdateOfficeTypeForm').submit(function (e) {
      e.preventDefault();
      var formData = new FormData($(this)[0]);
      formData.append('action', 'UpdateOfficeType');
      $.ajax({
        type: 'POST',
        url: '../php/processes.php',
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.success) {
            showSweetAlert("Success!", response.message, "success", "office_types.php");
          } else {
            showSweetAlert("Error", response.message, "error");
          }
        }, error: function (xhr, status, error) {
          console.error(xhr.responseText);
        }
      });
    });

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
        deleteMultipleRecords("tblofficetype", "office_type_id", selectedIDs, "office_types.php");
      });
    });

  });
</script>
