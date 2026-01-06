<?php
require_once 'header.php';
?>
<title>Order Records | <?= $system_info['system_name'] ?></title>
<style>
  #officeFilter:focus {
    outline: none !important;
    box-shadow: none !important;
    border-color: #ced4da;
  }

  table.dataTable thead .sorting:after,
  table.dataTable thead .sorting_asc:after,
  table.dataTable thead .sorting_desc:after {
    opacity: 0 !important;
    visibility: hidden !important;
  }
</style>
<section id="contact" class="contact section bg-light">
  <div class="container section-title" data-aos="fade-up">
    <div><span class="description-title">Order Records</span></div>
    <p>View submitted food orders below</p>
  </div>
  <div class="container" data-aos="fade-up" style="max-width: 1200px;">
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white p-3">
        <a href="#" id="createOrderLink" class="buy-btn" onclick="checkTime(event)"><i
            class="bi bi-plus-circle me-1"></i> Create Order</a>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-4">
            <div class="form-group">
              <label class="form-label">Filter by Office</label>
              <select id="officeFilter" name="office_name" class="form-control form-select"
                style="color: #000; background-color: #fff;">
                <option value="">Show All Offices</option>
                <optgroup label="Colleges">
                  <option value="CITC">CITC</option>
                  <option value="CEA">CEA</option>
                  <option value="COT">COT</option>
                  <option value="CSM">CSM</option>
                  <option value="CSTE">CSTE</option>
                </optgroup>
                <optgroup label="Administration">
                  <option value="Office of the President">Office of the President</option>
                  <option value="Finance / Office of the Vice Chancellor for Finance and Administration">
                    Finance / Office of the Vice Chancellor for Finance and Administration
                  </option>
                  <option value="USG / University Student Government">USG / University Student Government</option>
                  <option value="Office of the Chancellor / Chancellor Office">Office of the Chancellor / Chancellor
                    Office</option>
                </optgroup>
                <optgroup label="Support Offices">
                  <option value="Admission Office / Admission and Scholarship Office (ASO)">
                    Admission Office / Admission and Scholarship Office (ASO)
                  </option>
                  <option value="Registrar Office">Registrar Office</option>
                  <option value="Office of Student Affairs (OSA)">Office of Student Affairs (OSA)</option>
                  <option value="Federation of Accredited Extra-curricular Student Organizations (FAESO)">
                    Federation of Accredited Extra-curricular Student Organizations (FAESO)
                  </option>
                </optgroup>
              </select>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <hr>
          <table id="datatable" class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th scope="col">JOB ORDER #</th>
                <th scope="col">OFFICE NAME</th>
                <th scope="col">PICKUP DATETIME</th>
                <th scope="col">TOTAL</th>
                <th scope="col">STATUS</th>
                <th scope="col">ORDERED AT</th>
                <th scope="col" class="text-center">ACTIONS</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $orders = $db->getAllOrderRecords();
              if ($orders->num_rows > 0) {
                while ($row = $orders->fetch_assoc()) {
                  ?>
                  <tr>
                    <td><?= htmlspecialchars($row['job_order_no']); ?></td>
                    <td><?= ucwords($row['office_name']); ?></td>
                    <td>
                      <?php
                      $datetime_string = $row['needed_datetime'];

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
                      <?= ($row['total_amount'] > 0)
                        ? '₱' . number_format($row['total_amount'], 2)
                        : '<span class="text-muted">N/A</span>'; ?>
                    </td>
                    <td>
                      <?php
                      $statusClass = match ($row['status']) {
                        'On-going' => 'badge bg-warning text-dark',
                        'Completed' => 'badge bg-success',
                        default => 'badge bg-secondary',
                      };
                      ?>
                      <span class="<?= $statusClass; ?>"><?= htmlspecialchars($row['status']); ?></span>
                    </td>
                    <td><?= date("F d, Y h:i A", strtotime($row['created_at'])); ?></td>
                    <td class="text-center">
                      <button type="button" class="buy-btn view-order" title="View Details"
                        data-order-id="<?= $row['order_id']; ?>"
                        data-job-order-no="<?= htmlspecialchars($row['job_order_no']); ?>"
                        data-needed-datetime="<?= htmlspecialchars(implode(', ', $formatted_slots)); ?>"
                        data-status="<?= htmlspecialchars($row['status']); ?>"
                        data-created-at="<?= htmlspecialchars($row['created_at']); ?>"
                        data-total-amount="<?= htmlspecialchars($row['total_amount']); ?>">
                        <i class="fa fa-eye"></i>
                      </button>
                    </td>
                  </tr>
                  <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    No records found.
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>

        <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Order #<span id="orderJobNo"></span></h5>
              </div>
              <div class="modal-body">

                <div class="order-details-summary mb-3 p-3 border rounded bg-light">
                  <p class="mb-1"><strong>Ordered By Office Name:</strong> <span id="modalOfficeName"></span></p>
                  <p class="mb-1"><strong>Office Email:</strong> <span id="modalOfficeEmail"></span></p>
                  <p class="mb-1"><strong>Needed By:</strong> <span id="modalNeededTime"></span></p>
                  <p class="mb-1"><strong>Event/Purpose:</strong> <span id="modalEvent"></span></p>
                  <p class="mb-1"><strong>Date Placed:</strong> <span id="modalDatePlaced"></span></p>
                  <p class="mb-1"><strong>Status:</strong> <span id="modalStatus" class="badge bg-secondary"></span></p>
                  <p class="mb-0"><strong>Total Amount:</strong> <span id="modalTotalAmount"
                      class="text-danger font-weight-bold"></span></p>
                </div>
                <table class="table table-bordered table-hover table-sm mb-0">
                  <thead>
                    <tr>
                      <th>DESCRIPTION</th>
                      <th class="text-center">QTY</th>
                      <th class="text-center">UNIT PRICE</th>
                      <th class="text-center">SUBTOTAL</th>
                    </tr>
                  </thead>
                  <tbody id="orderItems">
                    <tr>
                      <td colspan="4" class="text-center">Loading...</td>
                    </tr>
                  </tbody>
                </table>
                <hr>
                <h6>Status History</h6>
                <table class="table table-sm table-bordered mb-0">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th>Updated At</th>
                    </tr>
                  </thead>
                  <tbody id="statusHistoryBody">
                    <tr>
                      <td colspan="2" class="text-center text-muted">Loading...</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
<script>
  function formatSingleDateTime(datetimeString) {
    const cleanString = datetimeString.trim();
    if (!cleanString) return 'N/A';

    const date = new Date(cleanString);

    if (isNaN(date.getTime())) {
      return cleanString; 
    }

    return date.toLocaleString();
  }

  function formatNeededDateTimeSlots(datetimeString) {
    if (!datetimeString) return 'N/A';

    const slots = datetimeString.split(' | ');

    const formattedSlots = slots.map(slot => formatSingleDateTime(slot));

    return formattedSlots.join('<br>');
  }
  function checkTime(event) {
    event.preventDefault();

    const now = new Date();
    const currentHour = now.getHours();

    const startTime = 1;
    const endTime = 24;

    if (currentHour >= startTime && currentHour < endTime) {
      const targetUrl = 'create_orders.php';
      window.location.href = targetUrl;
    } else {
      showSweetAlert("information", "You can only create an order between 7 AM and 5 PM.", "info");
    }
  }

  let dataTable;

  $(document).ready(function () {
    dataTable = $('#datatable').DataTable({
      "paging": true,
      "info": true,
      "searching": true,
    });

    $('#officeFilter').on('change', function () {
      const officeName = $(this).val();
      dataTable.column(1).search(officeName).draw();
    });
  });

  $(document).on('click', '.view-order', function () {
    const orderId = $(this).data('order-id');
    const jobOrderNo = $(this).data('job-order-no');
    const status = $(this).data('status');

    $('#orderJobNo').text(jobOrderNo);

    $('#orderModal').modal('show');

    $('#orderItems').html('<tr><td colspan="4" class="text-center text-muted">Loading...</td></tr>');
    $('#statusHistoryBody').html('<tr><td colspan="2" class="text-center text-muted">Loading...</td></tr>');

    $.ajax({
      url: 'php/processes.php',
      type: 'POST',
      data: { action: 'fetchOrderItems', order_id: orderId },
      dataType: 'json',
      success: function (response) {

        if (response.success && response.details) {
          const details = response.details;

          const neededTimeDisplay = formatNeededDateTimeSlots(details.needed_datetime);
          const datePlaced = new Date(details.order_placed_at).toLocaleDateString();
          const totalAmountDisplay = details.total_amount > 0 ?
            `₱${parseFloat(details.total_amount).toFixed(2)}` :
            'N/A';

          $('#modalOfficeName').text(details.office_name || 'N/A');
          $('#modalOfficeEmail').text(details.email || 'N/A');
          $('#modalNeededTime').html(neededTimeDisplay);
          $('#modalEvent').text(details.event || 'N/A');
          $('#modalDatePlaced').text(datePlaced);
          $('#modalTotalAmount').text(totalAmountDisplay);

          const statusText = details.status || status || 'N/A';
          const badge = $('#modalStatus');
          badge.removeClass().addClass('badge');
          if (statusText === 'On-going') badge.addClass('bg-warning text-dark');
          else if (statusText === 'Completed') badge.addClass('bg-success');
          else if (statusText === 'Cancelled') badge.addClass('bg-danger');
          else badge.addClass('bg-secondary');
          badge.text(statusText);

          const tbody = $('#orderItems');
          tbody.empty();

          if (response.items && response.items.length > 0) {
            response.items.forEach(item => {
              const unitPrice = parseFloat(item.price);
              const subTotal = parseFloat(item.total);

              const unitPriceDisplay = unitPrice > 0 ? `₱${unitPrice.toFixed(2)}` : 'N/A';
              const subTotalDisplay = subTotal > 0 ? `₱${subTotal.toFixed(2)}` : 'N/A';

              tbody.append(`
                            <tr>
                                <td>
                                    ${item.description}
                                </td>
                                <td class="text-center">${item.quantity}</td>
                                <td class="text-center">${unitPriceDisplay}</td>
                                <td class="text-center">${subTotalDisplay}</td>
                            </tr>
                        `);
            });
          } else {
            tbody.html('<tr><td colspan="4" class="text-center text-muted">No items found for this order.</td></tr>');
          }

          const history = response.history || [];
          const hbody = $('#statusHistoryBody');
          hbody.empty();
          if (history.length > 0) {
            history.forEach(entry => {
              const statusName = entry.status_name || 'N/A';
              const updatedAtRaw = entry.updated_at ? entry.updated_at : '';
              const updatedAt = formatSingleDateTime(updatedAtRaw);
              hbody.append(`
                <tr>
                  <td>${statusName}</td>
                  <td>${updatedAt}</td>
                </tr>
              `);
            });
          } else {
            hbody.html('<tr><td colspan="2" class="text-center text-muted">No status history found.</td></tr>');
          }

        } else {
          $('#orderItems').html('<tr><td colspan="4" class="text-center text-danger">Order details not found.</td></tr>');
        }
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        $('#orderItems').html('<tr><td colspan="4" class="text-center text-danger">Failed to load order data. Please try again.</td></tr>');
      }
    });
  });

</script>