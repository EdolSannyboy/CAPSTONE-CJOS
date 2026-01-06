<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Order Reports</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Order Reports</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Reports</h2>

        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <!-- FILTER/GENERATE REPORT -->
        <div class="filters pl-3 pr-3">
          <form id="export_record" method="POST" action="../php/export_records.php" target="_blank">
            <div class="row align-items-end">
              <div class="col-md-3">
                <div class="form-group">
                  <label for="order_date_from" class="mr-2">Order Date From</label>
                  <input type="date" class="form-control" name="order_date_from" id="order_date_from"
                    max="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="order_date_to" class="mr-2">Order Date To</label>
                  <input type="date" class="form-control" name="order_date_to" id="order_date_to"
                    max="<?= date('Y-m-d') ?>">
                </div>
              </div>
              <div class="col-md-3">
                <div class="form-group">
                  <label for="status" class="mr-2">Status</label>
                  <select name="status" id="status" class="form-control">
                    <option value=""> Select status</option>
                    <option value="On-going">On-going</option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                  </select>
                </div>
              </div>
              <div class="col-md-2">
                <div class="form-group">
                  <label>Export Options</label>
                  <div class="d-flex gap-2">
                    <button type="submit" id="applyFilters" class="btn btn-danger btn-sm"
                      name="export_orders_record_pdf">
                      <i class="fa fa-file"></i> PDF
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="card-box bg-white table-responsive pb-2">
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Job Order No.</th>
                <th>Office Name</th>
                <th>Needed Date/Time</th>
                <th>Status</th>
                <th>Total Amount</th>
                <th>Ordered At</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $orders = $db->getAllOrderRecords();
              while ($row2 = $orders->fetch_assoc()) {
                ?>
                <tr>
                  <td><?= htmlspecialchars($row2['job_order_no']); ?></td>
                  <td><?= !empty($row2['office_name']) ? ucwords($row2['office_name']) : 'N/A'; ?></td>
                  <td>
                    <?php
                        $datetime_string = $row2['needed_datetime'];
                        
                        $datetime_slots = explode(' | ', $datetime_string);
                        
                        $formatted_slots = [];
                        
                        foreach ($datetime_slots as $slot) {
                            $timestamp = strtotime($slot);
                            
                            $formatted_slots[] = date("F d, Y h:i A", $timestamp);
                        }
                        
                        echo implode('<br>', $formatted_slots);
                    ?>
                  </td>
                  <td>
                    <?php
                    $statusClass = match ($row2['status']) {
                      'On-going' => 'badge bg-warning text-light',
                      'Completed' => 'badge bg-success text-light',
                      'Cancelled' => 'badge bg-danger text-light',
                      default => 'badge bg-secondary text-light',
                    };
                    ?>
                    <span class="<?= $statusClass; ?>"><?= htmlspecialchars($row2['status']); ?></span>
                  </td>
                  <td>
                    <?= ($row2['total_amount'] > 0)
                      ? '₱' . number_format($row2['total_amount'], 2)
                      : '<span class="text-muted">N/A</span>'; ?>
                  </td>
                  <td><?= date("F d, Y h:i A", strtotime($row2['created_at'])); ?></td>
                </tr>
                <?php
              } ?>
            </tbody>
          </table>

          
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php require_once 'footer.php'; ?>

