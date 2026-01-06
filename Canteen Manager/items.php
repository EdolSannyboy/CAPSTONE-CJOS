<?php
  require_once '../php/db_config.php';
  require_once '../php/classes.php';
  $db = new db_class();

  if(!isset($_SESSION['user_ID'])) {
    header("Location: ../index.php");
    exit();
  }

  if($_SESSION['user_type'] !== "Canteen Staff" && $_SESSION['user_type'] !== "Canteen Manager") {
    header("Location: ../index.php");
    exit();
  }

  $id = $_SESSION['user_ID'];
  $login_time = $_SESSION['login_time'] ?? null;
  $user_type = $_SESSION['user_type'];

  $row = [
    'firstname' => $_SESSION['fullname'] ?? 'User',
    'lastname'  => '',
    'image'     => 'avatar.png',
  ];

  switch ($user_type) {
    case "Administrator":
      $badgeClass = 'badge-primary';
      $userTypeName = 'Administrator';
      break;
    case "Staff":
      $badgeClass = 'badge-info';
      $userTypeName = 'Staff';
      break;
    default:
      $badgeClass = 'badge-dark';
      $userTypeName = 'Unknown';
      break;
  }

  require_once 'header.php';
  require_once 'sidebar.php';
  $current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Items</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="row">
      <div class="col-md-12 col-sm-12">
        <div class="card-box bg-white table-responsive pb-2">
          <button data-toggle="modal" data-target="#AddItem" class="btn btn-sm btn-primary mb-3 ml-3 mt-1"><i class="fa fa-plus"></i> Add</button>
          <button id="delete-selected" class="btn btn-sm btn-danger mb-3 mt-1"><i class="fa fa-trash"></i> Delete</button>
          <button data-toggle="modal" data-target="#ViewAdjustments" class="btn btn-sm btn-info mb-3 mt-1"><i class="fa fa-history"></i> View Adjustments</button>
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th><input type="checkbox" id="select-all" class="d-block m-auto"></th>
                <th>ITEM NAME</th>
                <th>UNIT PRICE</th>
                <th>STOCK</th>
                <th>LOW STOCK THRESHOLD</th>
                <th>ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $items = $db->getItems();
              if ($items) {
                while ($row2 = $items->fetch_assoc()) {
                  $stockQty = isset($row2['stock_qty']) ? (int)$row2['stock_qty'] : 0;
                  $threshold = isset($row2['low_stock_threshold']) ? (int)$row2['low_stock_threshold'] : 0;
                  $isLowStock = $threshold > 0 && $stockQty <= $threshold;
              ?>
                  <tr<?= $isLowStock ? ' class="table-warning"' : '' ?>>
                    <td><input type="checkbox" class="select-record d-block m-auto" value="<?= $row2['item_id'] ?>"></td>
                    <td><?= htmlspecialchars($row2['item_name']); ?></td>
                    <td><?= number_format($row2['item_unit_price'], 2); ?></td>
                    <td>
                      <?= $stockQty; ?>
                      <?php if ($isLowStock): ?>
                        <span class="badge badge-danger ml-1">Low stock</span>
                      <?php endif; ?>
                    </td>
                    <td><?= $threshold; ?></td>
                    <td>
                      <button type="button" class="btn btn-success btn-sm edit-item"
                        data-item-id="<?= $row2['item_id']; ?>"
                        data-item-name="<?= htmlspecialchars($row2['item_name']); ?>"
                        data-item-unit-price="<?= $row2['item_unit_price']; ?>"
                        data-item-stock-qty="<?= $stockQty; ?>"
                        data-item-low-stock-threshold="<?= $threshold; ?>">
                        <i class="fa fa-edit"></i>
                      </button>
                      <button type="button" class="btn btn-warning btn-sm adjust-stock"
                        data-item-id="<?= $row2['item_id']; ?>"
                        data-current-stock="<?= $stockQty; ?>">
                        <i class="fa fa-exchange"></i>
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

<!-- Add Modal -->
<div class="modal fade" id="AddItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Add Item</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="addItemForm" method="POST" autocomplete="off">
        <div class="modal-body">
          <div class="form-group">
            <label for="item_name">Item Name</label>
            <input type="text" class="form-control" id="item_name" name="item_name" required>
          </div>
          <div class="form-group">
            <label for="item_unit_price">Unit Price</label>
            <input type="number" step="0.01" class="form-control" id="item_unit_price" name="item_unit_price" required>
          </div>
          <div class="form-group">
            <label for="stock_qty">Stock Quantity</label>
            <input type="number" min="0" class="form-control" id="stock_qty" name="stock_qty" value="0" required>
          </div>
          <div class="form-group">
            <label for="low_stock_threshold">Low Stock Threshold</label>
            <input type="number" min="0" class="form-control" id="low_stock_threshold" name="low_stock_threshold" value="10" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="AddItem">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Update Modal -->
<div class="modal fade" id="UpdateItem" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Update Item</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="updateItemForm" method="POST" autocomplete="off">
        <div class="modal-body">
          <input type="hidden" id="update_item_id" name="item_id">
          <div class="form-group">
            <label for="update_item_name">Item Name</label>
            <input type="text" class="form-control" id="update_item_name" name="item_name" required>
          </div>
          <div class="form-group">
            <label for="update_item_unit_price">Unit Price</label>
            <input type="number" step="0.01" class="form-control" id="update_item_unit_price" name="item_unit_price" required>
          </div>
          <div class="form-group">
            <label for="update_low_stock_threshold">Low Stock Threshold</label>
            <input type="number" min="0" class="form-control" id="update_low_stock_threshold" name="low_stock_threshold" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" name="UpdateItem">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="AdjustStock" tabindex="-1" role="dialog" aria-labelledby="AdjustStockLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="AdjustStockLabel">Adjust Stock</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="adjustStockForm" method="POST" autocomplete="off">
        <div class="modal-body">
          <input type="hidden" id="adjust_item_id" name="item_id">
          <div class="form-group">
            <label for="current_stock">Current Stock</label>
            <input type="number" class="form-control" id="current_stock" readonly>
          </div>
          <div class="form-group">
            <label for="change_qty">Change Quantity (use negative for deduction)</label>
            <input type="number" class="form-control" id="change_qty" name="change_qty" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Apply</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Adjustments Modal -->
<div class="modal fade" id="ViewAdjustments" tabindex="-1" role="dialog" aria-labelledby="ViewAdjustmentsLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="ViewAdjustmentsLabel">Item Adjustment History</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table id="adjustmentsTable" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th>Item Name</th>
              <th>Change Quantity</th>
              <th>Previous Stock</th>
              <th>New Stock</th>
              <th>Date & Time</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $adjustments_result = $db->getStockAdjustments();
            if ($adjustments_result && $adjustments_result->num_rows > 0) {
              while ($row = $adjustments_result->fetch_assoc()) {
                $change_class = $row['change_qty'] >= 0 ? 'text-success' : 'text-danger';
                $change_symbol = $row['change_qty'] >= 0 ? '+' : '';
                $adjusted_by = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
                $item_name = htmlspecialchars($row['item_name'] ?? 'Unknown Item');
                $created_at = date('M d, Y h:i A', strtotime($row['created_at']));
            ?>
            <tr>
              <td><?php echo $item_name; ?></td>
              <td class="<?php echo $change_class; ?>">
                <?php echo $change_symbol . $row['change_qty']; ?>
              </td>
              <td><?php echo $row['previous_stock']; ?></td>
              <td><?php echo $row['new_stock']; ?></td>
              <td><?php echo $created_at; ?></td>
            </tr>
            <?php
              }
            } else {
            ?>
            <tr>
              <td colspan="5" class="text-center">No adjustment records found</td>
            </tr>
            <?php
            }
            ?>
          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?php require_once 'footer.php'; ?>

<script>
$(document).ready(function() {
  var table = $('#datatable').DataTable();

  var initialFilter = <?php echo json_encode($_GET['filter'] ?? ''); ?>;
  if (initialFilter === 'low_stock') {
    table.search('Low stock').draw();
  }

  // Add Item
  $('#addItemForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: '../php/processes.php',
      type: 'POST',
      data: $(this).serialize() + '&action=AddItem',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Close modal, then show success and refresh data
          $('#AddItem').modal('hide');
          Swal.fire({
            title: 'Success!',
            text: response.message,
            icon: 'success'
          }).then(function() {
            location.reload();
          });
        } else {
          Swal.fire({
            title: 'Error!',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Error!',
          text: 'An unexpected error occurred.',
          icon: 'error'
        });
      }
    });
  });

  // Edit Item
  $('.edit-item').on('click', function() {
    var itemId = $(this).data('item-id');
    var itemName = $(this).data('item-name');
    var itemUnitPrice = $(this).data('item-unit-price');
    var lowStockThreshold = $(this).data('item-low-stock-threshold');

    $('#update_item_id').val(itemId);
    $('#update_item_name').val(itemName);
    $('#update_item_unit_price').val(itemUnitPrice);
    $('#update_low_stock_threshold').val(lowStockThreshold);

    $('#UpdateItem').modal('show');
  });

  // Update Item
  $('#updateItemForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: '../php/processes.php',
      type: 'POST',
      data: $(this).serialize() + '&action=UpdateItem',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          // Close modal, then show success and refresh data
          $('#UpdateItem').modal('hide');
          Swal.fire({
            title: 'Success!',
            text: response.message,
            icon: 'success'
          }).then(function() {
            location.reload();
          });
        } else {
          Swal.fire({
            title: 'Error!',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Error!',
          text: 'An unexpected error occurred.',
          icon: 'error'
        });
      }
    });
  });

  // Adjust Stock - open modal
  $('.adjust-stock').on('click', function() {
    var itemId = $(this).data('item-id');
    var currentStock = $(this).data('current-stock');

    $('#adjust_item_id').val(itemId);
    $('#current_stock').val(currentStock);
    $('#change_qty').val('');

    $('#AdjustStock').modal('show');
  });

  // Adjust Stock - submit
  $('#adjustStockForm').on('submit', function(e) {
    e.preventDefault();
    $.ajax({
      url: '../php/processes.php',
      type: 'POST',
      data: $(this).serialize() + '&action=AdjustStock',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          $('#AdjustStock').modal('hide');
          Swal.fire({
            title: 'Success!',
            text: response.message,
            icon: 'success'
          }).then(function() {
            location.reload();
          });
        } else {
          Swal.fire({
            title: 'Error!',
            text: response.message,
            icon: 'error'
          });
        }
      },
      error: function() {
        Swal.fire({
          title: 'Error!',
          text: 'An unexpected error occurred.',
          icon: 'error'
        });
      }
    });
  });

  // Delete selected
  $('#delete-selected').on('click', function() {
    var selected = [];
    $('.select-record:checked').each(function() {
      selected.push($(this).val());
    });

    if (selected.length === 0) {
      Swal.fire({
        title: 'Warning!',
        text: 'Please select at least one record to delete.',
        icon: 'warning'
      });
      return;
    }

    Swal.fire({
      title: 'Are you sure?',
      text: 'You are about to delete ' + selected.length + ' record(s). This action cannot be undone.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete!',
      cancelButtonText: 'Cancel'
    }).then(function(result) {
      if (result.isConfirmed) {
        $.ajax({
          url: '../php/processes.php',
          type: 'POST',
          data: { action: 'DeleteItems', ids: selected },
          dataType: 'json',
          success: function(response) {
            if (response.success) {
              Swal.fire({
                title: 'Success!',
                text: response.message,
                icon: 'success'
              }).then(function() {
                location.reload();
              });
            } else {
              Swal.fire({
                title: 'Error!',
                text: response.message,
                icon: 'error'
              });
            }
          },
          error: function() {
            Swal.fire({
              title: 'Error!',
              text: 'An unexpected error occurred.',
              icon: 'error'
            });
          }
        });
      }
    });
  });

  // Select all
  $('#select-all').change(function() {
    $('.select-record').prop('checked', $(this).prop('checked'));
  });

  // Initialize adjustments table when modal opens
  $('#ViewAdjustments').on('shown.bs.modal', function () {
    if (!$.fn.DataTable.isDataTable('#adjustmentsTable')) {
      $('#adjustmentsTable').DataTable({
        "order": [[ 4, "desc" ]], // Sort by date column descending
        "pageLength": 10
      });
    }
  });
});
</script>
