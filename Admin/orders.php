<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Order Records</title>

<?php
  // Load pending customer reminders for display
  if (!isset($db) || !($db instanceof db_class)) {
      $db = new db_class();
  }
  $pendingReminders = $db->getPendingReminders();
?>

<div class="right_col" role="main">
  <div class="">

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>Customer Reminders</h2>
        <div class="clearfix"></div>
      </div>
      <div class="x_content" style="max-height: 260px; overflow-y: auto;">
        <?php if (!empty($pendingReminders)): ?>
          <?php foreach ($pendingReminders as $rem): ?>
            <div class="alert alert-warning d-flex justify-content-between align-items-center" style="padding: 8px 12px;">
              <div>
                <strong>Order:</strong> <?= htmlspecialchars($rem['job_order_no']) ?><br>
                <strong>Office Email:</strong> <?= htmlspecialchars($rem['office_email']) ?><br>
                <small class="text-muted">Sent: <?= htmlspecialchars($rem['created_at']) ?></small>
              </div>
              <button type="button"
                      class="btn btn-success btn-xs ack-reminder-btn"
                      data-reminder-id="<?= (int)$rem['reminder_id']; ?>">
                Acknowledge
              </button>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted mb-0">No pending customer reminders.</p>
        <?php endif; ?>
      </div>
    </div>

    <div class="x_panel">
      <div class="x_title">
        <h2>View Order Records</h2>

        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="card-box bg-white table-responsive pb-2">
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Job Order #</th>
                <th>Requester Name</th>
                <th>Office Name</th>
                <th>Needed Date/Time</th>
                <th>Status</th>
                <th>Reminder</th>
                <th>Total Amount</th>
                <th>Ordered At</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $orders = $db->getAllOrderRecords();

              // Build map of order_id => [pending_count, total_count]
              $orderReminderDisplayMap = [];
              $conn = $db->getConnection();
              $remResult = $conn->query("SELECT order_id, SUM(CASE WHEN is_acknowledged = 0 THEN 1 ELSE 0 END) AS pending_count, COUNT(*) AS total_count FROM order_reminders GROUP BY order_id ORDER BY order_id DESC");
              if ($remResult) {
                  while ($r = $remResult->fetch_assoc()) {
                      $oid = (int)$r['order_id'];
                      $orderReminderDisplayMap[$oid] = [
                          'pending_count' => (int)$r['pending_count'],
                          'total_count' => (int)$r['total_count']
                      ];
                  }
              }

              $seen_job_orders = [];
              $ordersData = [];
              while ($row2 = $orders->fetch_assoc()) {
                if (isset($seen_job_orders[$row2['job_order_no']])) {
                  continue;
                }
                $seen_job_orders[$row2['job_order_no']] = true;
                $ordersData[] = $row2;
              }

              usort($ordersData, function ($a, $b) {
                $statusRank = function ($status) {
                  switch ($status) {
                    case 'Pending':
                      return 1;
                    case 'On-going':
                      return 2;
                    case 'Completed':
                      return 3;
                    default:
                      return 99;
                  }
                };

                $aRank = $statusRank($a['status'] ?? '');
                $bRank = $statusRank($b['status'] ?? '');

                if ($aRank !== $bRank) {
                  return $aRank <=> $bRank;
                }

                $aTime = strtotime($a['created_at'] ?? '');
                $bTime = strtotime($b['created_at'] ?? '');

                if ($aTime === $bTime) {
                  return 0;
                }

                return $aTime > $bTime ? -1 : 1;
              });

              foreach ($ordersData as $row2) {
                ?>
                <?php
                  $baseOfficeName = !empty($row2['office_name']) ? $row2['office_name'] : '';
                  $subOfficeName  = $row2['office_under_name'] ?? '';
                  if ($baseOfficeName !== '' && $subOfficeName !== '') {
                    $officeLabel = ucwords($baseOfficeName . ' - ' . $subOfficeName);
                  } elseif ($baseOfficeName !== '') {
                    $officeLabel = ucwords($baseOfficeName);
                  } else {
                    $officeLabel = 'N/A';
                  }
                ?>
                <tr>
                  <td><?= htmlspecialchars($row2['job_order_no']); ?></td>
                  <td><?= htmlspecialchars($row2['job_requester_name'] ?? ''); ?></td>
                  <td><?= htmlspecialchars($officeLabel); ?></td>
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
                      $statusText = $row2['status'] ?? 'N/A';
                      $statusClass = match ($statusText) {
                        'Pending' => 'badge bg-info text-light',
                        'On-going' => 'badge bg-warning text-light',
                        'Ready for Pickup' => 'badge bg-warning text-dark',
                        'Completed' => 'badge bg-success text-light',
                        'Cancelled' => 'badge bg-danger text-light',
                        default => 'badge bg-secondary text-light',
                      };
                    ?>
                    <span class="<?= $statusClass; ?>"><?= htmlspecialchars($statusText); ?></span>
                  </td>
                  <td class="text-center">
                    <?php
                      $oid = (int)$row2['order_id'];
                      $display = $orderReminderDisplayMap[$oid] ?? null;
                      $pendingCount = $display['pending_count'] ?? 0;
                      $totalCount = $display['total_count'] ?? 0;
                      if ($totalCount > 0):
                        if ($pendingCount > 0):
                    ?>
                          <span class="badge bg-danger" title="Customer sent <?= $totalCount; ?> reminder(s); <?= $pendingCount; ?> pending">
                            <i class="fa fa-bell"></i> Follow-up
                          </span>
                    <?php else: ?>
                          <span class="badge bg-secondary" title="Customer sent <?= $totalCount; ?> reminder(s); all acknowledged">
                            <i class="fa fa-bell"></i> Follow-up (Done)
                          </span>
                    <?php endif; else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?= ($row2['total_amount'] > 0)
                      ? '₱' . number_format($row2['total_amount'], 2)
                      : '<span class="text-muted">N/A</span>'; ?>
                  </td>
                  <td><?= date("F d, Y h:i A", strtotime($row2['created_at'])); ?></td>

                  <td class="text-center">
                    <button type="button" class="btn btn-info btn-sm view-order" title="View Details"
                        data-order-id="<?= $row2['order_id']; ?>"
                        data-requester-name="<?= htmlspecialchars($row2['job_requester_name'] ?? ''); ?>"
                        data-email="<?= $row2['email']; ?>"
                        data-office="<?= htmlspecialchars($officeLabel); ?>"
                        data-job-order-no="<?= htmlspecialchars($row2['job_order_no']); ?>"
                        data-needed-datetime="<?= htmlspecialchars(implode(', ', $formatted_slots)); ?>"
                        data-status="<?= htmlspecialchars($row2['status'] ?? 'N/A'); ?>"
                        data-created-at="<?= htmlspecialchars($row2['created_at']); ?>"
                        data-total-amount="<?= htmlspecialchars($row2['total_amount']); ?>">
                        <i class="fa fa-eye"></i>
                    </button>

                    <?php if (($row2['status'] ?? '') === 'Pending'): ?>
                      <button type="button" class="btn btn-success btn-sm approve-order" title="Approve Order"
                          data-order-id="<?= $row2['order_id']; ?>">
                          <i class="fa fa-check"></i> Approve
                      </button>

                      <button type="button" class="btn btn-danger btn-sm cancel-order" title="Cancel Order"
                          data-order-id="<?= $row2['order_id']; ?>">
                          <i class="fa fa-times"></i> Cancel
                      </button>
                    <?php elseif (($row2['status'] ?? '') === 'On-going'): ?>
                      <button type="button" class="btn btn-warning btn-sm ready-pickup-order" title="Ready for Pickup"
                          data-order-id="<?= $row2['order_id']; ?>">
                          <i class="fa fa-box"></i> Ready for Pickup
                      </button>
                    <?php elseif (($row2['status'] ?? '') === 'Ready for Pickup'): ?>
                      <button type="button" class="btn btn-primary btn-sm complete-order" title="Mark as Completed"
                          data-order-id="<?= $row2['order_id']; ?>">
                          <i class="fa fa-check-circle"></i>
                      </button>
                    <?php else: 
                      $disabled_attr = 'disabled style="opacity:0.6;cursor:not-allowed;"';
                    ?>
                      <button type="button" class="btn btn-danger btn-sm" title="Order is already <?= htmlspecialchars($row2['status'] ?? 'N/A'); ?>"
                          <?= $disabled_attr ?>>
                          <i class="fa fa-times-circle"></i>
                      </button>

                      <button type="button" class="btn btn-primary btn-sm" title="Order is already <?= htmlspecialchars($row2['status'] ?? 'N/A'); ?>"
                          <?= $disabled_attr ?>>
                          <i class="fa fa-check-circle"></i>
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php
              } ?>
            </tbody>
          </table>

          <div class="modal fade" id="viewOrderModal" tabindex="-1" aria-labelledby="viewOrderModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="viewOrderModalLabel">Order Details</h5>
                  <i data-dismiss="modal" aria-label="Close">X</i>
                </div>

                <div class="modal-body">
                  <div class="mb-3">
                    <strong>Requester Name:</strong> <span id="modalRequesterName"></span><br>
                    <strong>Office:</strong> <span id="modalOffice"></span><br>
                    <strong>Job Order No:</strong> <span id="modalJobOrderNo"></span><br>
                    <strong>Needed Date/Time:</strong> <span id="modalNeededDatetime"></span><br>
                    <strong>Status:</strong> <span id="modalStatus" class="badge"></span><br>
                    <strong>Total Amount:</strong> <span id="modalTotalAmount"></span>
                  </div>

                  <table class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Remarks</th>
                      </tr>
                    </thead>
                    <tbody id="orderItemsBody">
                      <tr>
                        <td colspan="5" class="text-center text-muted">Loading...</td>
                      </tr>
                    </tbody>
                  </table>

                  <hr>
                  <h6>Status History</h6>
                  <table class="table table-sm table-bordered">
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
                  <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php require_once 'footer.php'; ?>
<script>
  $(document).on('click', '.view-order', function () {
    const orderId = $(this).data('order-id');
    const requesterName = $(this).data('requester-name');
    const jobOrderNo = $(this).data('job-order-no');
    const office = $(this).data('office');
    const neededDatetime = $(this).data('needed-datetime');
    const status = $(this).data('status');
    const totalAmount = $(this).data('total-amount');

    $('#modalRequesterName').text(requesterName || 'N/A');
    $('#modalOffice').text(office || 'N/A');
    $('#modalJobOrderNo').text(jobOrderNo);
    $('#modalNeededDatetime').html(neededDatetime);
    $('#modalTotalAmount').text(totalAmount > 0 ? `₱${parseFloat(totalAmount).toFixed(2)}` : 'N/A');

    const badge = $('#modalStatus');
    badge.removeClass().addClass('badge');
    if (status === 'On-going') badge.addClass('bg-warning text-light');
    else if (status === 'Ready for Pickup') badge.addClass('bg-warning text-dark');
    else if (status === 'Completed') badge.addClass('bg-success text-light');
    else if (status === 'Cancelled') badge.addClass('bg-danger text-light');
    else if (status === 'Pending') badge.addClass('bg-info text-light');
    else badge.addClass('bg-secondary text-light');
    badge.text(status || 'N/A');

    $('#viewOrderModal').modal('show');

    $('#orderItemsBody').html('<tr><td colspan="5" class="text-center text-muted">Loading...</td></tr>');
    $('#statusHistoryBody').html('<tr><td colspan="2" class="text-center text-muted">Loading...</td></tr>');
    
    $.ajax({
      url: '../php/processes.php',
      type: 'POST',
      data: { action: 'fetchOrderItems', order_id: orderId },
      dataType: 'json',
      success: function (response) {
        const tbody = $('#orderItemsBody');
        tbody.empty();
        
        const items = response.items || []; 
        const history = response.history || [];

        if (response.success && items.length > 0) {
          items.forEach(item => {
            
            const priceDisplay = item.price > 0 ? `₱${parseFloat(item.price).toFixed(2)}` : 'N/A';
            const subTotalDisplay = item.total > 0 ? `₱${parseFloat(item.total).toFixed(2)}` : 'N/A';
            
            tbody.append(`
              <tr>
                <td>${item.description}</td>
                <td>${priceDisplay}</td>
                <td>${item.quantity}</td>
                <td>${subTotalDisplay}</td>
                <td>${item.remarks || ''}</td>
              </tr>
            `);
          });
        } else {
          tbody.html('<tr><td colspan="5" class="text-center text-muted">No items found.</td></tr>');
        }

        const hbody = $('#statusHistoryBody');
        hbody.empty();
        if (history.length > 0) {
          history.forEach(entry => {
            const statusName = entry.status_name || 'N/A';
            const updatedAtRaw = entry.updated_at ? entry.updated_at : '';
            const updatedAt = formatDateTime12(updatedAtRaw);
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
      },
      error: function (xhr, status, error) {
        console.error(xhr.responseText);
        $('#orderItemsBody').html('<tr><td colspan="5" class="text-center text-danger">Failed to load items.</td></tr>');
      }
    });
  });

  function formatDateTime12(datetimeString) {
      if (!datetimeString) return '';
      const date = new Date(datetimeString.replace(' ', 'T'));
      if (isNaN(date.getTime())) return datetimeString;
      const options = { year: 'numeric', month: 'long', day: '2-digit', hour: 'numeric', minute: '2-digit', hour12: true };
      return date.toLocaleString('en-US', options);
  }

  $(document).on('click', '.approve-order', function () {
    const orderId = $(this).data('order-id');
    
    Swal.fire({
      title: 'Approve Order',
      text: 'Are you sure you want to approve this order?',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, approve',
      cancelButtonText: 'No',
      confirmButtonColor: '#28a745'
    }).then((result) => {
      if (result.isConfirmed) {
        updateOrderStatus(orderId, 'On-going');
      }
    });
  });

  $(document).on('click', '.ready-pickup-order', function () {
    const orderId = $(this).data('order-id');
    
    Swal.fire({
      title: 'Mark as Ready for Pickup?',
      html: `
        <p>Are you sure you want to mark this order as <strong>Ready for Pickup</strong>?</p>
        <p>An email notification will be sent to the customer informing them that their order is ready for pickup.</p>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Yes, Mark as Ready',
      cancelButtonText: 'No',
      confirmButtonColor: '#ffc107'
    }).then((result) => {
      if (result.isConfirmed) {
        updateOrderStatus(orderId, 'Ready for Pickup');
      }
    });
  });

  $(document).on('click', '.complete-order', function () {
    const orderId = $(this).data('order-id');
    
    Swal.fire({
      title: 'Confirm Order Completion?',
      html: `
        <p>Are you sure you want to mark this order as <strong>Completed</strong>?</p>
        <p><strong>Important:</strong> Once an order is marked as completed, its status cannot be changed or reversed.</p>
        <p>Please ensure all items have been delivered and the order is fully processed before proceeding.</p>
      `,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, Mark as Completed',
      cancelButtonText: 'Close',
      confirmButtonColor: '#28a745'
    }).then((result) => {
      if (result.isConfirmed) {
        updateOrderStatus(orderId, 'Completed');
      }
    });
  });

  $(document).on('click', '.cancel-order', function () {
    const orderId = $(this).data('order-id');
    
    Swal.fire({
      title: 'Cancel Order',
      text: 'Are you sure you want to cancel this order?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, cancel',
      cancelButtonText: 'No',
      confirmButtonColor: '#d33'
    }).then((result) => {
      if (result.isConfirmed) {
        updateOrderStatus(orderId, 'Cancelled');
      }
    });
  });

  function updateOrderStatus(orderId, status, reason = null) {
      const postData = {
          action: 'updateOrderStatus',
          order_id: orderId, 
          status: status 
      };
      
      if (reason) {
          postData.cancellation_reason = reason;
      }
      
      $.ajax({
          type: 'POST',
          url: '../php/processes.php',
          data: postData,
          dataType: 'json',
          success: function (response) {
              if (response.success) {
                  Swal.fire({
                      title: 'Success!',
                      text: response.message,
                      icon: 'success',
                      confirmButtonText: 'OK'
                  }).then(() => window.location.href = 'orders.php');
              } else {
                  Swal.fire('Error', response.message, 'error');
              }
          },
          error: function (xhr) {
              console.error(xhr.responseText);
              Swal.fire('Error', 'Something went wrong while updating the order status.', 'error');
          }
      });
  }

  // Acknowledge customer reminder
  $(document).on('click', '.ack-reminder-btn', function () {
    const btn = $(this);
    const reminderId = btn.data('reminder-id');

    if (!reminderId) return;

    if (!confirm('Mark this follow-up as acknowledged?')) return;

    btn.prop('disabled', true).text('Saving...');

    $.ajax({
      url: '../php/processes.php',
      method: 'POST',
      dataType: 'json',
      data: {
        action: 'acknowledgeReminder',
        reminder_id: reminderId
      },
      success: function (res) {
        if (res.success) {
          location.reload();
        } else {
          alert(res.message || 'Failed to acknowledge reminder.');
          btn.prop('disabled', false).text('Acknowledge');
        }
      },
      error: function () {
        alert('Error while acknowledging reminder.');
        btn.prop('disabled', false).text('Acknowledge');
      }
    });
  });
</script>