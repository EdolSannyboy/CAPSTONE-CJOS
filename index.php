<?php
require_once 'header.php';
$orders = $db->getOrdersToday();
if ($orders === false) {
    $orders = [];
}
$totalSales = count($orders) > 0 ? number_format(array_sum(array_column($orders, 'total_amount')), 2) : '0.00';
?>
<title>Today's Orders | <?= $system_info['system_name'] ?></title>
<style>
  .fc,
  .fc th,
  .fc td,
  .fc .fc-col-header-cell-cushion,
  .fc .fc-daygrid-day-number,
  .fc .fc-timegrid-slot-label,
  .fc .fc-list-day-text,
  .fc .fc-list-day-side-text,
  .fc .fc-list-event-time,
  .fc .fc-list-event-title {
    color: #000000 !important;
  }
</style>
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet">

<section id="gallery" class="gallery section">
  <div class="container section-title" data-aos="fade-up" style="margin-top: 80px;">
    <div><span>Today's Orders</span> <span class="description-title">Overview</span></div>
    <p>Quick overview and detailed list of all scheduled orders for today.</p>
  </div>

  <div class="container" data-aos="fade">

    <div class="row justify-content-center">
      <div class="col-lg-10">
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-body">
            <h4 class="mb-3">Track Order by Job Order No.</h4>
            <form id="jobOrderSearchForm" class="row g-2 align-items-center">
              <div class="col-sm-6 col-md-8">
                <input type="text" name="job_order_no" id="job_order_no" class="form-control" placeholder="Enter Job Order No." required>
              </div>
              <div class="col-sm-4 col-md-3">
                <button type="submit" class="btn btn-primary w-100">Search</button>
              </div>
              <div class="col-12 mt-2">
                <div id="jobOrderSearchMessage" class="text-danger small"></div>
              </div>
            </form>
          </div>
        </div>

        <div class="card shadow-sm border-0 mb-5">
          <div class="card-body">
            <h4 class="mb-3">Orders Calendar</h4>
            <div id="calendar"></div>
          </div>
        </div>

        <div class="card shadow-sm border-0">
          <div class="card-body">
            <h4 class="mb-3">Today's Order Lists <span class="text-primary">(<?= count($orders); ?>)</span></h4>
            <table id="datatable" class="table table-hover align-middle" style="width:100%">
              <thead class="table-light">
                <tr>
                  <th class="text-center">Job Order No</th>
                  <th class="text-center">Requester Name</th>
                  <th class="text-center">Office Name</th>
                  <th class="d-none text-center">Email</th>
                  <th class="d-none text-center">Total Amount</th>
                  <th class="text-center">Pickup DateTime</th>
                  <th class="text-center">Ordered At</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($orders as $order):
                  $baseOfficeName = !empty($order['office_name']) ? $order['office_name'] : '';
                  $subOfficeName  = $order['office_under_name'] ?? '';
                  if ($baseOfficeName !== '' && $subOfficeName !== '') {
                    $officeLabel = ucwords($baseOfficeName . ' - ' . $subOfficeName);
                  } elseif ($baseOfficeName !== '') {
                    $officeLabel = ucwords($baseOfficeName);
                  } else {
                    $officeLabel = 'N/A';
                  }
                  ?>
                  <tr>
                    <td class="text-center"><?= htmlspecialchars($order['job_order_no'] ?? '') ?></td>
                    <td class="text-center"><?= htmlspecialchars($order['job_requester_name'] ?? '') ?></td>
                    <td class="text-center"><?= htmlspecialchars($officeLabel) ?></td>
                    <td class="d-none text-center"><?= htmlspecialchars($order['email'] ?? '') ?></td>
                    <td class="d-none text-center">₱<?= number_format($order['total_amount'], 2) ?></td>
                    <td class="text-center">
                      <?php
                      $today_date_str = date('Y-m-d');
                      $needed_datetime = $order['needed_datetime'] ?? '';
                      $slots = !empty($needed_datetime) ? explode(' | ', $needed_datetime) : [];
                      $today_times = [];

                      foreach ($slots as $slot) {
                        if (strpos($slot, $today_date_str) !== false) {
                          $timestamp = strtotime($slot);
                          if ($timestamp !== false) {
                            $today_times[] = date("h:i A", $timestamp);
                          }
                        }
                      }

                      if (!empty($today_times)) {
                        echo implode('<br>', $today_times);
                      } else {
                        echo 'N/A Today';
                      }
                      ?>
                    </td>
                    <td class="text-center"><?= !empty($order['created_at']) ? date("F d, Y h:i A", strtotime($order['created_at'])) : 'N/A' ?></td>
                    <td>
                      <button class="btn btn-primary btn-sm view-items-btn" data-id="<?= $order['order_id'] ?>"
                        data-job="<?= htmlspecialchars($order['job_order_no'] ?? '') ?>">
                        Order details
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>

            <div class="modal fade" id="orderModal" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Order #<span id="orderJobNo"></span></h5>
                  </div>
                  <div class="modal-body">

                    <div class="order-details-summary mb-3 p-3 border rounded bg-light">
                      <p class="mb-1"><strong>Requester Name:</strong> <span id="modalRequesterName"></span></p>
                      <p class="mb-1"><strong>Ordered By Office Name:</strong> <span id="modalOfficeName"></span></p>
                      <!-- <p class="mb-1"><strong>Office Email:</strong> <span id="modalOfficeEmail"></span></p> -->
                      <p class="mb-1"><strong>Needed By:</strong> <span id="modalNeededTime"></span></p>
                      <p class="mb-1"><strong>Event/Purpose:</strong> <span id="modalEvent"></span></p>
                      <p class="mb-1"><strong>Date Placed:</strong> <span id="modalDatePlaced"></span></p>
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
                    <span id="reminderStatusText" class="me-auto small text-muted"></span>
                    <button type="button" class="btn btn-danger btn-sm" id="sendReminderBtn" onclick="sendReminderFromModal()">
                      <i class="fas fa-bell"></i> Send Reminder to Canteen
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</section>

<?php require_once 'footer.php'; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>

<script>
  // Global variables to store current order details
  let currentOrderId = null;
  let currentJobOrderNo = null;
  let currentOfficeEmail = null;
  let currentOrderStatus = '';

  function hideReminderButton() {
    const btn = document.getElementById('sendReminderBtn');
    if (!btn) return;
    btn.style.display = 'none';
  }

  function showReminderButton() {
    const btn = document.getElementById('sendReminderBtn');
    if (!btn) return;
    btn.style.display = '';
  }

  function resetReminderButtonState() {
    const btn = document.getElementById('sendReminderBtn');
    if (!btn) return;
    btn.disabled = false;
    btn.classList.remove('btn-success');
    if (!btn.classList.contains('btn-danger')) {
      btn.classList.add('btn-danger');
    }
    btn.innerHTML = '<i class="fas fa-bell"></i> Send Reminder to Canteen';
  }

  function markReminderAsSent() {
    const btn = document.getElementById('sendReminderBtn');
    if (!btn) return;
    btn.disabled = true;
    btn.classList.remove('btn-danger');
    if (!btn.classList.contains('btn-success')) {
      btn.classList.add('btn-success');
    }
    btn.innerHTML = '<i class="fas fa-check"></i> Reminder Sent';
  }

  function updateReminderVisibilityAndState(details) {
    if (!details) {
      console.error('No details provided to updateReminderVisibilityAndState');
      hideReminderButton();
      return;
    }

    // Get status from either status or status_name field, default to empty string
    const statusRaw = (details.status || details.status_name || '').toString().toLowerCase().trim();
    currentOrderStatus = statusRaw;
    const orderIdForCheck = details.order_id || currentOrderId;
    
    console.log('updateReminderVisibilityAndState - Status:', statusRaw, 'Order ID:', orderIdForCheck, 'Details:', details);

    // Show reminder button for these statuses - handle various formats
    const showForStatuses = ['on-going', 'ongoing', 'pending'];
    const isCompletedOrCancelled = ['completed', 'cancelled'].includes(statusRaw);
    const shouldShowReminder = showForStatuses.includes(statusRaw) && !isCompletedOrCancelled;

    console.log('Status check - Raw status:', statusRaw, 'Should show reminder:', shouldShowReminder);

    if (orderIdForCheck && shouldShowReminder) {
      console.log('Showing reminder button for order', orderIdForCheck, 'with status', statusRaw);
      updateReminderButtonState(orderIdForCheck);
    } else {
      console.log('Hiding reminder button - Status:', statusRaw, 'Order ID:', orderIdForCheck);
      hideReminderButton();
      
      // Update status text to explain why button is hidden
      const infoEl = document.getElementById('reminderStatusText');
      if (infoEl) {
        if (isCompletedOrCancelled) {
          infoEl.textContent = `Order is ${statusRaw}. No reminders needed.`;
        } else if (!orderIdForCheck) {
          infoEl.textContent = 'Order details not loaded.';
        } else {
          infoEl.textContent = 'Reminders only available for on-going and pending orders.';
        }
      }
    }
  }

  function updateReminderButtonState(orderId) {
    const btn = document.getElementById('sendReminderBtn');
    const infoEl = document.getElementById('reminderStatusText');
    if (infoEl) {
      infoEl.textContent = '';
    }
    if (!btn || !orderId) return;

    $.ajax({
      url: 'php/processes.php',
      method: 'POST',
      dataType: 'json',
      data: { action: 'checkOrderReminder', order_id: orderId },
      success: function (res) {
        const status = (currentOrderStatus || '').toLowerCase().trim();
        const isCompletedOrCancelled = ['completed', 'cancelled'].includes(status);
        const isAllowedStatus = ['on-going', 'ongoing', 'pending'].includes(status);

        console.log('Reminder check - Status:', status, 'Completed/Cancelled:', isCompletedOrCancelled, 'Allowed:', isAllowedStatus, 'Response:', res);

        if (res && res.success && res.hasReminder) {
          // Update button state if reminder exists
          if (isAllowedStatus && !isCompletedOrCancelled) {
            showReminderButton();
            markReminderAsSent();
          } else {
            hideReminderButton();
          }

          // Update reminder info text
          if (infoEl && res.reminder) {
            const r = res.reminder;
            const acknowledged = String(r.is_acknowledged ?? '0') === '1';
            let text = 'Reminder sent.';
            if (acknowledged) {
              const ackAt = r.acknowledged_at ? r.acknowledged_at : '';
              const ackName = r.acknowledged_by_name || '';
              if (ackName && ackAt) {
                text = `Reminder acknowledged by ${ackName} on ${ackAt}.`;
              } else if (ackName) {
                text = `Reminder acknowledged by ${ackName}.`;
              } else if (ackAt) {
                text = `Reminder acknowledged on ${ackAt}.`;
              } else {
                text = 'Reminder acknowledged.';
              }
            } else {
              text = 'Reminder sent, not yet acknowledged.';
            }
            infoEl.textContent = text;
          }
        } else {
          // No reminder yet
          if (isAllowedStatus && !isCompletedOrCancelled) {
            showReminderButton();
            resetReminderButtonState();
            if (infoEl) {
              infoEl.textContent = 'No reminder sent for this order yet.';
            }
          } else {
            hideReminderButton();
            if (infoEl) {
              if (isCompletedOrCancelled) {
                infoEl.textContent = 'Order is ' + status + '. No reminders needed.';
              } else {
                infoEl.textContent = 'Reminders only available for on-going and pending orders.';
              }
            }
          }
        }
      },
      error: function () {
        hideReminderButton();
        if (infoEl) {
          infoEl.textContent = 'Error checking reminder status.';
        }
      }
    });
  }

  function sendReminderFromModal() {
    if (!currentOrderId || !currentJobOrderNo || !currentOfficeEmail) {
      alert('Order details not loaded. Please try again.');
      return;
    }

    // Show email confirmation modal for security
    showEmailConfirmationModal();
  }

  function showEmailConfirmationModal() {
    // Create modal HTML for email confirmation
    const modalHtml = `
      <div class="modal fade" id="emailConfirmationModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">Confirm Email Address</h5>
              <button type="button" class="close" onclick="closeEmailModal()" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <p>For security purposes, please confirm your email address to send a reminder:</p>
              <div class="form-group">
                <label for="confirmEmail">Email Address:</label>
                <input type="email" class="form-control" id="confirmEmail" placeholder="Enter your email address" required>
                <small class="form-text text-muted">This must match the email associated with the order.</small>
              </div>
              <div id="emailError" class="text-danger" style="display: none;"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" onclick="closeEmailModal()">Cancel</button>
              <button type="button" class="btn btn-primary" id="confirmSendReminderBtn">Send Reminder</button>
            </div>
          </div>
        </div>
      </div>
    `;

    // Remove existing modal if any
    $('#emailConfirmationModal').remove();
    
    // Add modal to body
    $('body').append(modalHtml);
    
    // Show modal
    const modal = $('#emailConfirmationModal');
    modal.modal('show');
    
    // Handle modal close events
    modal.on('hidden.bs.modal', function () {
      // Clean up event listeners and remove modal
      $(this).remove();
      $('#confirmSendReminderBtn').off('click');
      $('#confirmEmail').off('input');
    });
    
    // Handle form submission
    $('#confirmSendReminderBtn').on('click', function() {
      const confirmEmail = $('#confirmEmail').val().trim();
      const errorDiv = $('#emailError');
      
      // Validate email
      if (!confirmEmail) {
        errorDiv.text('Please enter your email address.');
        errorDiv.show();
        return;
      }
      
      // Validate email format
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(confirmEmail)) {
        errorDiv.text('Please enter a valid email address.');
        errorDiv.show();
        return;
      }
      
      // Check if email matches the order email
      if (confirmEmail.toLowerCase() !== currentOfficeEmail.toLowerCase()) {
        errorDiv.text('Email does not match the email associated with this order.');
        errorDiv.show();
        return;
      }
      
      // Email validated, proceed with sending reminder
      errorDiv.hide();
      closeEmailModal();
      
      // Show loading state
      const reminderBtn = document.getElementById('sendReminderBtn');
      if (reminderBtn) {
        reminderBtn.disabled = true;
        reminderBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
      }
      
      // Send reminder
      $.ajax({
        url: 'send_reminder.php',
        method: 'POST',
        data: {
          order_id: currentOrderId,
          job_order_no: currentJobOrderNo,
          office_email: confirmEmail
        },
        dataType: 'json',
        success: function (res) {
          if (res.success) {
            if (res.alreadySent) {
              alert('Reminder already sent for this order.');
            } else {
              alert('Reminder sent successfully!');
            }
            // Update button state
            if (reminderBtn) {
              markReminderAsSent();
            }
            // Refresh reminder status
            updateReminderButtonState(currentOrderId);
          } else {
            alert('Failed to send reminder: ' + (res.message || 'Unknown error'));
            // Reset button state
            if (reminderBtn) {
              reminderBtn.disabled = false;
              reminderBtn.innerHTML = 'Send Reminder';
            }
          }
        },
        error: function () {
          alert('Error sending reminder. Please try again.');
          // Reset button state
          if (reminderBtn) {
            reminderBtn.disabled = false;
            reminderBtn.innerHTML = 'Send Reminder';
          }
        }
      });
    });
    
    // Clear error when user starts typing
    $('#confirmEmail').on('input', function() {
      $('#emailError').hide();
    });
  }

  function closeEmailModal() {
    const modal = $('#emailConfirmationModal');
    if (modal.length) {
      modal.modal('hide');
      // Force remove after a short delay
      setTimeout(function() {
        modal.remove();
        $('#confirmSendReminderBtn').off('click');
        $('#confirmEmail').off('input');
      }, 200);
    }
  }

  function formatTimeOnly(datetimeString) {
    if (!datetimeString) return '';
    const date = new Date(datetimeString.replace(' ', 'T'));

    if (isNaN(date.getTime())) return '';

    let hour = date.getHours();
    const minute = String(date.getMinutes()).padStart(2, '0');
    const ampm = hour >= 12 ? 'p' : 'a';

    hour = hour % 12;
    hour = hour === 0 ? 12 : hour;

    return `${hour}:${minute}${ampm}`;
  }

  function formatSingleDateTime(datetimeString) {
    if (!datetimeString) return '';

    const date = new Date(datetimeString);

    if (isNaN(date.getTime())) {
      return datetimeString;
    }

    const months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];

    const month = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();

    let hour = date.getHours();
    const minute = String(date.getMinutes()).padStart(2, '0');
    const ampm = hour >= 12 ? 'PM' : 'AM';

    hour = hour % 12;
    hour = hour === 0 ? 12 : hour;
    hour = String(hour).padStart(2, '0');

    return `${month} ${day}, ${year} ${hour}:${minute} ${ampm}`;
  }

  function formatFullDateTime(datetimeString) {
    if (!datetimeString) return 'N/A';

    const slots = datetimeString.split(' | ');

    const formattedSlots = slots.map(slot => formatSingleDateTime(slot.trim()));

    return formattedSlots.join('<br>');
  }

  function formatDateOnly(datetimeString) {
    if (!datetimeString) return '';

    const date = new Date(datetimeString.replace(' ', 'T'));

    if (isNaN(date.getTime())) {
      return '';
    }

    const months = ["Jan.", "Feb.", "Mar.", "Apr.", "May", "Jun.", "Jul.", "Aug.", "Sept.", "Oct.", "Nov.", "Dec."];
    const month = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();

    return `${month} ${day}, ${year}`;
  }

  document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    var calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
      },
      eventContent: function (arg) {
        const item = arg.event.extendedProps.rawData;

        if (!item) {
          return { html: arg.event.title };
        }

        const formattedTime = formatTimeOnly(item.needed_datetime);
        const formattedDate = formatDateOnly(item.needed_datetime);
        const baseOfficeName = (item.office_name || '').toString();
        const subOfficeName  = (item.office_under_name || '').toString();
        let combinedOffice   = baseOfficeName;
        if (baseOfficeName && subOfficeName) {
          combinedOffice = baseOfficeName + ' - ' + subOfficeName;
        }
        const capitalizedOfficeName = combinedOffice.toUpperCase();

        let statusLabel = "";
        switch (item.status.toLowerCase()) {
          case "completed": statusLabel = "COMPLETED"; break;
          case "cancelled": statusLabel = "CANCELLED"; break;
          case "on-going": default: statusLabel = "ON-GOING"; break;
        }

        const htmlContent = `
            <div>
                <div style="font-weight: bold; font-size: 1.1em; line-height: 1;">${formattedTime}</div>
                <div>${capitalizedOfficeName} - ${formattedDate}</div>
                <div style="font-size: 0.9em; opacity: 0.9;">${statusLabel}</div>
            </div>
        `;

        return { html: htmlContent };
      },
      // ***************************************************************
      events: function (fetchInfo, successCallback, failureCallback) {
        fetch("php/ajax.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: new URLSearchParams({ action: "getOrders" })
        })
          .then(res => res.json())
          .then(data => {
            const events = data.flatMap(item => {
              const neededDatetimes = item.needed_datetime ? item.needed_datetime.split(' | ') : [];

              let color = "#3b82f6";
              switch (item.status.toLowerCase()) {
                case "completed":
                  color = "#10b981";
                  break;
                case "cancelled":
                  color = "#ef4444";
                  break;
                case "on-going":
                default:
                  color = "#3b82f6";
                  break;
              }

              const baseOfficeName = (item.office_name || '').toString();
              const subOfficeName  = (item.office_under_name || '').toString();
              let combinedOffice   = baseOfficeName;
              if (baseOfficeName && subOfficeName) {
                combinedOffice = baseOfficeName + ' - ' + subOfficeName;
              }
              const eventTitle = combinedOffice.toUpperCase();

              return neededDatetimes.map(singleDatetime => {
                const singleEventRawData = { ...item, needed_datetime: singleDatetime };

                return {
                  id: item.order_id,
                  title: eventTitle,
                  start: singleDatetime,
                  color: color,
                  extendedProps: {
                    job_order_no: item.job_order_no,
                    total_amount: parseFloat(item.total_amount).toFixed(2),
                    rawData: singleEventRawData
                  }
                };
              });
            });
            successCallback(events);
          })
          .catch(err => failureCallback(err));
      },
      eventClick: function (info) {
        let orderId = info.event.id;
        let jobNo = info.event.extendedProps.job_order_no;
        let totalAmount = info.event.extendedProps.total_amount;

        document.getElementById("orderJobNo").textContent = jobNo + " (₱" + totalAmount + ")";

        $("#orderItems").html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

        $("#modalRequesterName").text('Loading...');
        $("#modalOfficeName").text('Loading...');
        $("#modalOfficeEmail").text('Loading...');
        $("#modalNeededTime").text('Loading...');
        $("#modalEvent").text('Loading...');
        $("#modalDatePlaced").text('Loading...');
        $("#modalTotalAmount").text('');

        $.ajax({
          url: "php/processes.php",
          method: "POST",
          data: { action: "fetchOrderItems", order_id: orderId },
          dataType: "json",
          success: function (res) {
            if (res.success) {
              const details = res.details;
              const items = res.items;
              const history = res.history || [];

              if (details) {
                const neededFormatted = formatFullDateTime(details.needed_datetime);
                const placedFormatted = formatFullDateTime(details.order_placed_at);

                // Ensure consistent status field
                if (!details.status && details.status_name) {
                  details.status = details.status_name;
                }

                console.log('Calendar Click - Order details:', details);

                // Store current order details for reminder button (calendar click)
                currentOrderId = orderId;
                currentJobOrderNo = jobNo;
                currentOfficeEmail = details.email || details.office_email;
                updateReminderVisibilityAndState(details);

                $("#modalRequesterName").text(details.job_requester_name || 'N/A');
                $("#modalOfficeName").text(details.office_name || 'N/A');
                $("#modalOfficeEmail").text('');
                $("#modalNeededTime").html(neededFormatted || 'N/A');
                $("#modalEvent").text(details.event || 'N/A');
                $("#modalDatePlaced").text(placedFormatted || 'N/A');
                $("#modalTotalAmount").text('₱' + parseFloat(details.total_amount).toFixed(2));
              }

              if (items && items.length > 0) {
                let rows = '';
                items.forEach(item => {
                  rows += `
                                <tr>
                                  <td>${item.description}</td>
                                  <td class="text-center">${item.quantity}</td>
                                  <td class="text-center">₱${parseFloat(item.price).toFixed(2)}</td>
                                  <td class="text-center">₱${parseFloat(item.total).toFixed(2)}</td>
                                </tr>
                            `;
                });
                $("#orderItems").html(rows);
              } else {
                $("#orderItems").html('<tr><td colspan="4" class="text-center">No items found</td></tr>');
              }

              const hbody = $("#statusHistoryBody");
              hbody.empty();
              if (history.length > 0) {
                history.forEach(entry => {
                  const statusName = entry.status_name || 'N/A';
                  const updatedAt = entry.updated_at ? entry.updated_at : '';
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
              $("#orderItems").html('<tr><td colspan="4" class="text-center text-danger">Error fetching details.</td></tr>');
              $("#statusHistoryBody").html('<tr><td colspan="2" class="text-center text-danger">Error fetching status history.</td></tr>');
            }
          },
          error: function () {
            $("#orderItems").html('<tr><td colspan="4" class="text-center text-danger">Error loading items</td></tr>');
            $("#statusHistoryBody").html('<tr><td colspan="2" class="text-center text-danger">Error loading status history</td></tr>');
          }
        });

        $("#orderModal").modal("show");
      }
    });

    calendar.render();

    $(".view-items-btn").click(function () {
      const orderId = $(this).data("id");
      const jobNo = $(this).data("job");

      $("#orderJobNo").text(jobNo);
      currentOrderId = orderId;
      currentJobOrderNo = jobNo;
      currentOfficeEmail = null;
      hideReminderButton();

      $("#orderItems").html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');

      $("#modalRequesterName").text('Loading...');
      $("#modalOfficeName").text('Loading...');
      $("#modalOfficeEmail").text('');
      $("#modalNeededTime").text('Loading...');
      $("#modalEvent").text('Loading...');
      $("#modalDatePlaced").text('Loading...');
      $("#modalTotalAmount").text('');

      $.ajax({
        url: "php/processes.php",
        method: "POST",
        data: { action: "fetchOrderItems", order_id: orderId },
        dataType: "json",
        success: function (res) {
          if (res.success) {
            const details = res.details;
            const items = res.items;
            const history = res.history || [];

            if (details) {
              const neededFormatted = formatFullDateTime(details.needed_datetime);
              const placedFormatted = formatFullDateTime(details.order_placed_at);
              const totalAmountDisplay = '₱' + parseFloat(details.total_amount).toFixed(2);

              $("#modalRequesterName").text(details.job_requester_name || 'N/A');
              $("#modalOfficeName").text(details.office_name || 'N/A');
              $('#modalOfficeEmail').text('');
              $("#modalNeededTime").html(neededFormatted || 'N/A');
              $("#modalEvent").text(details.event || 'N/A');
              $("#modalDatePlaced").text(placedFormatted || 'N/A');
              $("#modalTotalAmount").text(totalAmountDisplay);
              updateReminderVisibilityAndState(details);
            } else {
              $("#modalOfficeName, #modalOfficeEmail, #modalNeededTime, #modalEvent, #modalDatePlaced").text('Error/N/A');
              $("#modalTotalAmount").text('N/A');
            }

            if (items && items.length > 0) {
              let rows = '';
              items.forEach(item => {
                const unitPriceDisplay = `₱${parseFloat(item.price).toFixed(2)}`;
                const subTotalDisplay = `₱${parseFloat(item.total).toFixed(2)}`;

                rows += `
                              <tr>
                                  <td>${item.description}</td>
                                  <td class="text-center">${item.quantity}</td>
                                  <td class="text-center">${unitPriceDisplay}</td>
                                  <td class="text-center">${subTotalDisplay}</td>
                              </tr>
                          `;
              });
              $("#orderItems").html(rows);
            } else {
              $("#orderItems").html('<tr><td colspan="4" class="text-center">No items found</td></tr>');
            }

            const hbody = $("#statusHistoryBody");
            hbody.empty();
            if (history.length > 0) {
              history.forEach(entry => {
                const statusName = entry.status_name || 'N/A';
                const updatedAt = entry.updated_at ? entry.updated_at : '';
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
            $("#orderItems").html('<tr><td colspan="4" class="text-center text-danger">Error fetching details.</td></tr>');
            $("#modalOfficeName, #modalOfficeEmail, #modalNeededTime, #modalEvent, #modalDatePlaced").text('Error');
            $("#modalTotalAmount").text('');
            $("#statusHistoryBody").html('<tr><td colspan="2" class="text-center text-danger">Error fetching status history.</td></tr>');
          }
        },
        error: function () {
          $("#orderItems").html('<tr><td colspan="4" class="text-center text-danger">Error loading items</td></tr>');
          $("#modalOfficeName, #modalOfficeEmail, #modalNeededTime, #modalEvent, #modalDatePlaced").text('Error');
          $("#modalTotalAmount").text('');
          $("#statusHistoryBody").html('<tr><td colspan="2" class="text-center text-danger">Error loading status history</td></tr>');
        }
      });

      $("#orderModal").modal("show");
    });

    // Handle job order number search on public index
    $('#jobOrderSearchForm').on('submit', function (e) {
      e.preventDefault();
      const jobOrderNo = $('#job_order_no').val().trim();
      const msgEl = $('#jobOrderSearchMessage');
      msgEl.text('');

      if (!jobOrderNo) {
        msgEl.text('Please enter a Job Order No.');
        return;
      }

      $('#orderJobNo').text(jobOrderNo);
      $('#orderItems').html('<tr><td colspan="4" class="text-center">Loading...</td></tr>');
      $('#statusHistoryBody').html('<tr><td colspan="2" class="text-center text-muted">Loading...</td></tr>');
      $("#modalOfficeName, #modalOfficeEmail, #modalNeededTime, #modalEvent, #modalDatePlaced").text('Loading...');
      $("#modalTotalAmount").text('');

      $.ajax({
        url: 'php/processes.php',
        method: 'POST',
        data: { action: 'trackOrderByJobNo', job_order_no: jobOrderNo },
        dataType: 'json',
        success: function (res) {
          if (res.success) {
            const details = res.details;
            const items = res.items || [];
            const history = res.history || [];

            if (details) {
              const neededFormatted = formatFullDateTime(details.needed_datetime);
              const placedFormatted = formatFullDateTime(details.order_placed_at);
              const totalAmountDisplay = '₱' + parseFloat(details.total_amount).toFixed(2);

              console.log('Job Order Search - Raw details from API:', details);
              console.log('Job Order Search - Order ID from details:', details.order_id);
              
              // Store current order details for reminder button (job order search)
              currentOrderId = details.order_id;
              currentJobOrderNo = details.job_order_no;
              currentOfficeEmail = details.email;
              console.log('Job Order Search - Setting order details:', {
                orderId: currentOrderId,
                jobOrderNo: currentJobOrderNo,
                email: currentOfficeEmail,
                status: details.status,
                details: details
              });
              
              // Ensure we have the order ID before calling reminder function
              if (currentOrderId) {
                updateReminderVisibilityAndState(details);
              } else {
                console.error('Order ID is missing from details:', details);
                hideReminderButton();
              }

              $('#orderJobNo').text(`${details.job_order_no} (${totalAmountDisplay})`);
              $("#modalOfficeName").text(details.office_name || 'N/A');
              $("#modalOfficeEmail").text('');
              $("#modalNeededTime").html(neededFormatted || 'N/A');
              $("#modalEvent").text(details.event || 'N/A');
              $("#modalDatePlaced").text(placedFormatted || 'N/A');
              $("#modalTotalAmount").text(totalAmountDisplay);
            } else {
              $("#modalOfficeName, #modalOfficeEmail, #modalNeededTime, #modalEvent, #modalDatePlaced").text('Error/N/A');
              $("#modalTotalAmount").text('N/A');
            }

            if (items.length > 0) {
              let rows = '';
              items.forEach(item => {
                const unitPriceDisplay = item.price ? `₱${parseFloat(item.price).toFixed(2)}` : 'N/A';
                const subTotalDisplay = item.total ? `₱${parseFloat(item.total).toFixed(2)}` : 'N/A';
                rows += `
                  <tr>
                    <td>${item.description || ''}</td>
                    <td class="text-center">${item.quantity || ''}</td>
                    <td class="text-center">${unitPriceDisplay}</td>
                    <td class="text-center">${subTotalDisplay}</td>
                  </tr>
                `;
              });
              $('#orderItems').html(rows);
            } else {
              $('#orderItems').html('<tr><td colspan="4" class="text-center">No items found</td></tr>');
            }

            const hbody = $('#statusHistoryBody');
            hbody.empty();
            if (history.length > 0) {
              history.forEach(entry => {
                const statusName = entry.status_name || 'N/A';
                const updatedAt = entry.updated_at ? entry.updated_at : '';
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

            $('#orderModal').modal('show');
          } else {
            msgEl.text(res.message || 'No order found for the provided Job Order No.');
            $('#orderItems').html('<tr><td colspan="4" class="text-center text-muted">No items to display</td></tr>');
            $('#statusHistoryBody').html('<tr><td colspan="2" class="text-center text-muted">No status history to display</td></tr>');
            
            // Clear current order details and hide reminder button on error
            currentOrderId = null;
            currentJobOrderNo = null;
            currentOfficeEmail = null;
            hideReminderButton();
            const infoEl = document.getElementById('reminderStatusText');
            if (infoEl) {
              infoEl.textContent = 'Order not found or error loading details.';
            }
          }
        },
        error: function () {
          msgEl.text('An error occurred while searching for the order.');
          $('#orderItems').html('<tr><td colspan="4" class="text-center text-danger">Error loading items</td></tr>');
          $('#statusHistoryBody').html('<tr><td colspan="2" class="text-center text-danger">Error loading status history</td></tr>');
          
          // Clear current order details and hide reminder button on error
          currentOrderId = null;
          currentJobOrderNo = null;
          currentOfficeEmail = null;
          hideReminderButton();
          const infoEl = document.getElementById('reminderStatusText');
          if (infoEl) {
            infoEl.textContent = 'Error loading order details.';
          }
        }
      });
    });


  });
</script>