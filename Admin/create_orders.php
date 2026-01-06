<?php
require_once 'sidebar.php';

// Fetch offices grouped by office type for the Office select
$officeOptions = [];
try {
  $conn = $db->getConnection();
  $stmt = $conn->prepare("SELECT ot.office_type_id, ot.office_type_name, o.office_id, o.office_name, o.office_email
                          FROM tblofficetype ot
                          LEFT JOIN tbloffice o ON o.office_type_id = ot.office_type_id
                          ORDER BY ot.office_type_name, o.office_name");
  if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $typeId = $row['office_type_id'];
      if (!isset($officeOptions[$typeId])) {
        $officeOptions[$typeId] = [
          'type_name' => $row['office_type_name'],
          'offices'   => []
        ];
      }

      if (!empty($row['office_id'])) {
        $officeOptions[$typeId]['offices'][] = [
          'id'    => $row['office_id'],
          'name'  => $row['office_name'],
          'email' => $row['office_email']
        ];
      }
    }
    $stmt->close();
  }
} catch (Exception $e) {
  // In case of error, keep $officeOptions empty so the select still renders
}

// Fetch items for the service select (including stock)
$items = [];
$lowStockItems = [];
$outOfStockItems = [];
try {
  $conn = $db->getConnection();
  $stmt = $conn->prepare("SELECT item_id, item_name, item_unit_price, stock_qty, low_stock_threshold, item_added_by FROM tblitem WHERE 1 ORDER BY item_name ASC");
  if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $items[] = $row;

      $stockQty = isset($row['stock_qty']) ? (int)$row['stock_qty'] : 0;
      $threshold = isset($row['low_stock_threshold']) ? (int)$row['low_stock_threshold'] : 0;

      if ($stockQty <= 0) {
        $outOfStockItems[] = $row['item_name'];
      } elseif ($threshold > 0 && $stockQty <= $threshold) {
        $lowStockItems[] = $row['item_name'];
      }
    }
    $stmt->close();
  }
} catch (Exception $e) {
  // In case of error, keep $items empty so the select still renders
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<title><?= $system_info['system_name'] ?> | Create Order</title>
<style>
  .staff-create-order-wrapper {
    padding: 20px 10px 40px 10px;
  }

  .staff-create-order-wrapper .x_panel {
    background-color: #ffffff;
    border-radius: 8px;
  }

  .staff-create-order-wrapper .x_title h2 {
    font-weight: 600;
    font-size: 20px;
  }

  .staff-create-order-wrapper .php-email-form .form-control,
  .staff-create-order-wrapper .php-email-form .form-select {
    border-radius: 4px;
    font-size: 14px;
    padding: 8px 12px;
    height: auto;
  }

  .staff-create-order-wrapper .php-email-form label {
    font-weight: 500;
    font-size: 14px;
  }

  .staff-create-order-wrapper .php-email-form button[type="submit"] {
    background: #f9a825;
    color: #ffffff;
    border: none;
    padding: 10px 30px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 500;
    letter-spacing: 0.5px;
    transition: 0.3s;
  }

  .staff-create-order-wrapper .php-email-form button[type="submit"]:hover {
    opacity: 0.85;
  }

  .staff-create-order-wrapper .buy-btn {
    background-color: #ffffff;
    color: #f9a825;
    border: 1px solid #f9a825;
    padding: 8px 15px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease, color 0.3s ease;
  }

  .staff-create-order-wrapper .buy-btn:hover {
    background-color: #f9a825;
    color: #ffffff;
  }

  .staff-create-order-wrapper #addDatetimeSlotBtn {
    border-radius: 50px;
    padding: 6px 14px;
    font-size: 13px;
  }

  .staff-create-order-wrapper .order-item {
    background-color: #f9fafb;
  }

  .staff-create-order-wrapper .remove-slot-btn,
  .staff-create-order-wrapper .remove-item-btn {
    font-size: 13px;
  }
  .staff-create-order-wrapper .remove-slot-btn {
    margin-left: 8px;
  }

  /* Section title styling */
  .right_col.staff-create-order-wrapper .section-title div span:first-child {
    color: #433f39 !important;
    font-weight: 600 !important;
    font-size: inherit;
  }
  
  .right_col.staff-create-order-wrapper .section-title div .description-title {
    color: #f9a825 !important;
    font-weight: 600 !important;
    font-size: inherit;
  }
  
  .right_col.staff-create-order-wrapper .section-title p {
    color: #666 !important;
    font-size: 14px !important;
    margin-top: 5px !important;
  }
</style>

<div class="right_col staff-create-order-wrapper" role="main">
  <section id="contact" class="contact section bg-light">
    <div class="container section-title" data-aos="fade-up">
      <div><span>Create</span> <span class="description-title">Order</span></div>
      <p>Fill out the required fields below</p>
    </div>

    <div class="container" data-aos="fade-up">
      <form id="createOrderForm" method="post" role="form" class="php-email-form" autocomplete="off">
        <div class="row mb-3">
          <div class="col-lg-3 col-md-3 col-sm-6 col-12">
            <div class="form-group">
              <label class="form-label">Office</label>
              <select name="office_id" id="office_select" class="form-control form-select"
                style="color: #000; background-color: #fff;" required onchange="handleOfficeChange()">
                <option value="" disabled selected>Select Office</option>
                <?php foreach ($officeOptions as $type): ?>
                  <?php if (!empty($type['offices'])): ?>
                    <optgroup label="<?= htmlspecialchars($type['type_name']) ?>">
                      <?php foreach ($type['offices'] as $office): ?>
                        <option value="<?= htmlspecialchars($office['id']) ?>"
                                data-email="<?= htmlspecialchars($office['email']) ?>"
                                data-name="<?= htmlspecialchars($office['name']) ?>">
                          <?= htmlspecialchars($office['name']) ?>
                        </option>
                      <?php endforeach; ?>
                    </optgroup>
                  <?php endif; ?>
                <?php endforeach; ?>
                <option value="others">Others (Specify)</option>
              </select>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-12" id="office_under_group" style="display: none;">
            <div class="form-group">
              <label class="form-label">Office Under (Optional)</label>
              <select name="office_under_id" id="office_under_select" class="form-control form-select" style="color: #000; background-color: #fff;" disabled>
                <option value="" selected>-- No sub office --</option>
              </select>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-12" id="others_input_group" style="display: none;">
            <div class="form-group">
              <label class="form-label" for="other_office_name">Specific Office Name</label>
              <input type="text" name="other_office_name" id="other_office_name" class="form-control"
                placeholder="Enter the specific office name" />
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-12">
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="office_email" id="office_email" class="form-control" required readonly>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-12">
            <div class="form-group">
              <label class="form-label">Event</label>
              <input type="text" name="event" class="form-control" required>
            </div>
          </div>
          <div class="col-lg-3 col-md-3 col-sm-6 col-12">
            <div class="form-group">
              <label class="form-label">Requester Name</label>
              <input type="text" name="job_requester_name" class="form-control" required>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12">
            <label class="form-label">Select Dates (can be multiple)</label>
            <input type="text" id="multiple_dates_picker" class="form-control" placeholder="Click to select dates" required>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12">
            <label class="form-label">Select Pickup Time (applies to all dates)</label>
            <input type="time" id="single_time_picker" class="form-control" required>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-12">
            <div id="final-datetime-inputs-container" style="display: none;">
            </div>
          </div>

        </div>

        <div id="orderItemsContainer">

          <div class="order-item border rounded p-3 mb-3">
            <div class="row g-3 align-items-end">
              <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                <label for="description">Article Description / Services</label>
                <select name="service_picker" class="form-control form-select items_id"
                  style="color: #000; background-color: #fff;">
                  <option value="" selected disabled>Select a service</option>
                  <?php foreach ($items as $item): ?>
                    <?php
                      $stockQty = isset($item['stock_qty']) ? (int)$item['stock_qty'] : 0;
                      $threshold = isset($item['low_stock_threshold']) ? (int)$item['low_stock_threshold'] : 0;
                      $isOutOfStock = $stockQty <= 0;
                      $isLowStock = !$isOutOfStock && $threshold > 0 && $stockQty <= $threshold;
                      $disabled = $isOutOfStock ? 'disabled' : '';
                      $label = htmlspecialchars($item['item_name']);
                      if ($isOutOfStock) {
                        $label .= ' (Out of Stock)';
                      } elseif ($isLowStock) {
                        $label .= ' (Low Stock: ' . $stockQty . ' left)';
                      } else {
                        $label .= ' - Stock: ' . $stockQty;
                      }
                    ?>
                    <option value="<?= htmlspecialchars($item['item_id']) ?>"
                            data-name="<?= htmlspecialchars($item['item_name']) ?>"
                            data-price="<?= htmlspecialchars($item['item_unit_price']) ?>"
                            data-stock="<?= $stockQty; ?>"
                            data-low="<?= $isLowStock ? '1' : '0'; ?>" <?= $disabled; ?>>
                      <?= $label; ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="table-responsive mt-3">
              <table class="table table-sm" id="selectedItemsTable">
                <thead>
                  <tr>
                    <th>Article Description / Services</th>
                    <th>Price / Unit (₱)</th>
                    <th>Available</th>
                    <th>No. of Pax</th>
                    <th>Remarks / Instructions</th>
                    <th>Total (₱)</th>
                    <th style="width: 60px;">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <!-- Rows will be generated by JavaScript based on selected services -->
                </tbody>
              </table>
            </div>
          </div>

        </div>

        <button type="submit"><i class="fa fa-save"></i> Submit Order</button>
      </form>


    </div>
  </section>
</div>

<?php require_once 'footer.php'; ?>

<script>

  function validateDateTime(input) {
    const selectedDateTimeValue = input.value;
    const selectedDateTime = new Date(selectedDateTimeValue);
    const minDate = new Date(MIN_DATETIME);

    if (isNaN(selectedDateTime)) {
      return;
    }

    if (selectedDateTime < minDate) {
      showSweetAlert("Information", "The selected pickup date and time cannot be in the past.", "info");
      input.value = '';
      return;
    }

    const dayOfWeek = selectedDateTime.getDay();
    const hour = selectedDateTime.getHours();

    if (dayOfWeek === 0 || dayOfWeek === 6) {
      showSweetAlert("Information", "Pickup can only be scheduled on **Monday through Friday**.", "info");
      input.value = '';
      return;
    }

    if (hour < 8 || hour >= 17) {
      showSweetAlert("Information", "Pickup can only be scheduled between **8:00 AM and 5:00 PM**.", "info");
      input.value = '';
      return;
    }

    if (selectedDateTimeValue) {
      let isDuplicate = false;
      const allDateTimeInputs = document.querySelectorAll('input[name="needed_datetime[]"]');

      allDateTimeInputs.forEach(otherInput => {
        if (otherInput !== input && otherInput.value === selectedDateTimeValue) {
          isDuplicate = true;
        }
      });

      if (isDuplicate) {
        showSweetAlert("Duplicate Date/Time", "This date and time slot has already been selected. Please choose a unique slot.", "warning");
        input.value = ''; 
        return;
      }
    }
  }

  function handleOfficeChange() {
    const selectElement = document.getElementById('office_select');
    const emailInput = document.getElementById('office_email');
    const othersInputGroup = document.getElementById('others_input_group');
    const otherOfficeNameInput = document.getElementById('other_office_name');
    const officeUnderGroup = document.getElementById('office_under_group');
    const officeUnderSelect = document.getElementById('office_under_select');
    const selectedValue = selectElement.value;
    const selectedOption = selectElement.options[selectElement.selectedIndex];

    // Reset Office Under select whenever office changes
    if (officeUnderSelect) {
      officeUnderSelect.innerHTML = '<option value="" selected>-- No sub office --</option>';
      officeUnderSelect.disabled = true;
    }
    if (officeUnderGroup) {
      officeUnderGroup.style.display = 'none';
    }

    if (selectedValue === 'others') {
      emailInput.value = '';
      emailInput.removeAttribute('readonly');
      othersInputGroup.style.display = 'block';
      otherOfficeNameInput.setAttribute('required', 'required');
      otherOfficeNameInput.focus();
    } else if (selectedValue) {
      const email = selectedOption.getAttribute('data-email') || '';
      emailInput.value = email;
      emailInput.setAttribute('readonly', 'readonly');
      othersInputGroup.style.display = 'none';
      otherOfficeNameInput.removeAttribute('required');
      otherOfficeNameInput.value = '';

      // Load sub offices for the selected office (if any)
      if (selectedValue && selectedValue !== 'others' && typeof $ !== 'undefined') {
        $.ajax({
          type: 'POST',
          url: '../php/processes.php',
          data: {
            action: 'getSubOffices',
            office_id: selectedValue
          },
          dataType: 'json',
          success: function (response) {
            if (!officeUnderSelect || !officeUnderGroup) return;

            officeUnderSelect.innerHTML = '<option value="" selected>-- No sub office --</option>';

            if (response && response.success && Array.isArray(response.sub_offices) && response.sub_offices.length > 0) {
              response.sub_offices.forEach(function (sub) {
                const opt = document.createElement('option');
                opt.value = sub.office_under_id;
                opt.textContent = sub.office_under_name;
                if (sub.office_under_email) {
                  opt.setAttribute('data-email', sub.office_under_email);
                }
                officeUnderSelect.appendChild(opt);
              });

              officeUnderSelect.disabled = false;
              officeUnderGroup.style.display = 'block';
            } else {
              officeUnderSelect.disabled = true;
              officeUnderGroup.style.display = 'none';
            }
          },
          error: function () {
            if (officeUnderSelect && officeUnderGroup) {
              officeUnderSelect.innerHTML = '<option value="" selected>-- No sub office --</option>';
              officeUnderSelect.disabled = true;
              officeUnderGroup.style.display = 'none';
            }
          }
        });
      }
    } else {
      emailInput.value = '';
      emailInput.setAttribute('readonly', 'readonly');
      othersInputGroup.style.display = 'none';
      otherOfficeNameInput.removeAttribute('required');
      otherOfficeNameInput.value = '';
    }
  }


  const MIN_DATETIME = "<?= date('Y-m-d\TH:i'); ?>";

  document.addEventListener('DOMContentLoaded', function () {

    const lowStockItems = <?= json_encode($lowStockItems); ?>;
    const outOfStockItems = <?= json_encode($outOfStockItems); ?>;

    if ((lowStockItems && lowStockItems.length) || (outOfStockItems && outOfStockItems.length)) {
      let message = '';
      if (outOfStockItems && outOfStockItems.length) {
        message += '<strong>Out of stock:</strong><br>' + outOfStockItems.map(i => '- ' + i).join('<br>');
      }
      if (lowStockItems && lowStockItems.length) {
        if (message) message += '<br><br>';
        message += '<strong>Low stock:</strong><br>' + lowStockItems.map(i => '- ' + i).join('<br>');
      }

      if (typeof Swal !== 'undefined' && Swal.fire) {
        const Toast = Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 5000,
          timerProgressBar: true
        });

        Toast.fire({
          icon: 'warning',
          title: 'Stock Notice',
          html: message
        });
      }
    }

    const today = new Date();
    const nextDay = new Date(today);
    nextDay.setDate(today.getDate() + 1);
    const minDateString = today.toLocaleDateString('en-CA', { year: 'numeric', month: '2-digit', day: '2-digit' }).replace(/-/g, '-');

    flatpickr("#multiple_dates_picker", {
      mode: "multiple",
      dateFormat: "Y-m-d",
      minDate: minDateString,
      disable: [
        function(date) {
          return (date.getDay() === 0 || date.getDay() === 6);
        }
      ],
      onClose: function(selectedDates, dateStr, instance) {
        if (selectedDates.length > 0) {
          instance.input.removeAttribute('required');
        } else {
          instance.input.setAttribute('required', 'required');
        }
      }
    });

    // Add real-time validation for time picker
    document.getElementById('single_time_picker').addEventListener('change', function() {
      const selectedTime = this.value;
      if (selectedTime) {
        const hour = parseInt(selectedTime.split(':')[0]);
        if (hour < 8 || hour >= 17) {
          showSweetAlert("Invalid Time", "Pickup time must be between **8:00 AM and 5:00 PM**.", "info");
          this.value = '';
        }
      }
    });

    const serviceSelect = document.querySelector('.items_id');
    const selectedItemsTableBody = document.querySelector('#selectedItemsTable tbody');
    const officeUnderSelectEl = document.getElementById('office_under_select');
    const officeSelectEl = document.getElementById('office_select');
    const officeEmailInputEl = document.getElementById('office_email');

    if (officeUnderSelectEl && officeSelectEl && officeEmailInputEl) {
      officeUnderSelectEl.addEventListener('change', function () {
        const subVal = this.value;

        // If no department selected, fall back to main office email
        if (!subVal) {
          const officeOpt = officeSelectEl.options[officeSelectEl.selectedIndex];
          const officeEmail = officeOpt ? (officeOpt.getAttribute('data-email') || '') : '';
          officeEmailInputEl.value = officeEmail;
          return;
        }

        const selectedSubOpt = this.options[this.selectedIndex];
        const deptEmail = selectedSubOpt ? (selectedSubOpt.getAttribute('data-email') || '') : '';

        if (deptEmail) {
          officeEmailInputEl.value = deptEmail;
        } else {
          // Fallback again to office email if department email is missing
          const officeOpt = officeSelectEl.options[officeSelectEl.selectedIndex];
          const officeEmail = officeOpt ? (officeOpt.getAttribute('data-email') || '') : '';
          officeEmailInputEl.value = officeEmail;
        }
      });
    }

    function updateOptionStates() {
      if (!serviceSelect) return;
      const selectedIds = new Set();
      if (selectedItemsTableBody) {
        selectedItemsTableBody.querySelectorAll('tr[data-item-id]').forEach(tr => {
          selectedIds.add(tr.dataset.itemId);
        });
      }

      Array.from(serviceSelect.options).forEach(opt => {
        if (!opt.value) return; // skip placeholder
        // Disable if already selected or out of stock; low stock remains selectable
        opt.disabled = selectedIds.has(opt.value) || opt.getAttribute('data-stock') === '0';
      });
    }

    function addSelectedItem(option) {
      if (!selectedItemsTableBody || !option) return;
      const itemId = option.value;
      if (!itemId) return;

      if (selectedItemsTableBody.querySelector(`tr[data-item-id="${itemId}"]`)) {
        return;
      }
      const price = parseFloat(option.getAttribute('data-price')) || 0;
      const stock = parseInt(option.getAttribute('data-stock')) || 0;
      const isLow = option.getAttribute('data-low') === '1';
      const itemName = option.getAttribute('data-name') || option.textContent.trim();

      const row = document.createElement('tr');
      row.dataset.itemId = itemId;

      row.innerHTML = `
        <td>
          <input type="hidden" name="items_id[]" value="${itemId}">
          <div>${itemName}</div>
          <div class="text-muted" style="font-size: 11px;">Stock: ${stock}</div>
        </td>
        <td>
          <input type="number" name="price[]" class="form-control form-control-sm price" readonly
            value="${price.toFixed(2)}">
        </td>
        <td>
          <input type="text" class="form-control form-control-sm" value="${stock}" readonly>
        </td>
        <td>
          <input type="number" name="quantity[]" class="form-control form-control-sm quantity" min="1" value="1"
            data-stock="${stock}">
        </td>
        <td>
          <textarea name="remarks[]" class="form-control form-control-sm" rows="1"
            placeholder="Optional instructions"></textarea>
        </td>
        <td>
          <input type="number" name="total[]" class="form-control form-control-sm total" readonly
            value="${price.toFixed(2)}">
        </td>
        <td class="text-center">
          <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn">
            <i class="fa fa-trash"></i>
          </button>
        </td>
      `;

      selectedItemsTableBody.appendChild(row);
      updateOptionStates();

      if (isLow) {
        showSweetAlert("Low Stock Warning", `This item is currently low on stock (available: ${stock}). Please double-check the quantity before submitting the order.`, "info");
      }
    }

    if (serviceSelect) {
      serviceSelect.addEventListener('change', function () {
        const option = this.options[this.selectedIndex];
        addSelectedItem(option);
        // reset back to placeholder
        this.selectedIndex = 0;
      });
      // Initialize option states on load
      updateOptionStates();
    }

    if (selectedItemsTableBody) {
      selectedItemsTableBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity')) {
          const row = e.target.closest('tr');
          const priceInput = row.querySelector('.price');
          const totalInput = row.querySelector('.total');

          const price = parseFloat(priceInput.value) || 0;
          const stock = parseInt(e.target.getAttribute('data-stock')) || 0;
          let qty = parseFloat(e.target.value) || 0;

          if (stock > 0 && qty > stock) {
            qty = stock;
            e.target.value = stock;
            showSweetAlert("Information", `Quantity cannot exceed available stock (${stock}). It has been adjusted to the maximum allowed.`, "info");
          }

          totalInput.value = (price * qty).toFixed(2);
        }
      });

      selectedItemsTableBody.addEventListener('click', function (e) {
        if (e.target.closest('.remove-item-btn')) {
          const row = e.target.closest('tr');
          const itemId = row.dataset.itemId;
          row.remove();
          updateOptionStates();
        }
      });
    }
  });

  $(document).ready(function () {
    $('#createOrderForm').submit(function (e) {
      e.preventDefault();

      const officeSelectVal = $('#office_select').val();
      const officeEmailVal = ($('#office_email').val() || '').trim();

      if (officeSelectVal === 'others') {
        const ustpPattern = /@ustp\.edu\.ph$/i;
        if (!ustpPattern.test(officeEmailVal)) {
          showSweetAlert("Invalid Email", "For 'Others' office, please use a valid USTP email address ending with @ustp.edu.ph.", "warning");
          return;
        }
      }

      const finalContainer = $('#final-datetime-inputs-container');
      finalContainer.empty();

      const selectedDatesString = $('#multiple_dates_picker').val();
      const selectedTime = $('#single_time_picker').val();

      if (!selectedDatesString || !selectedTime) {
        showSweetAlert("Missing Information", "Please select at least one date and a pickup time.", "warning");
        return;
      }

      const datesArray = selectedDatesString.split(', ');
      let timeError = false;

      const hour = parseInt(selectedTime.split(':')[0]);
      if (hour < 8 || hour >= 17) {
        showSweetAlert("Invalid Time", "Pickup time must be between **8:00 AM and 5:00 PM**.", "info");
        return;
      }

      datesArray.forEach(date => {
        const combinedDatetime = date + 'T' + selectedTime;

        const combinedDateObj = new Date(combinedDatetime);
        const now = new Date();

        if (combinedDateObj < now) {
          timeError = true;
          return;
        }

        const hiddenInput = `<input type="hidden" name="needed_datetime[]" value="${combinedDatetime}">`;
        finalContainer.append(hiddenInput);
      });

      if (timeError) {
        showSweetAlert("Time Error", "One or more selected date/time combinations are in the past. Please adjust.", "info");
        return;
      }

      const form = $(this);
      const formData = form.serialize();

      Swal.fire({
        title: "Confirm Order Submission?",
        html: "By proceeding, you confirm this order is final. **No cancellation or modification will be allowed.** Do you wish to submit the order?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, Submit Order!",
        cancelButtonText: "No, Review Order"
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            type: 'POST',
            url: '../php/processes.php',
            data: formData + '&action=createOrderForm',
            dataType: 'json',
            success: function (response) {
              if (response.success) {
                showSweetAlert("Order Submitted!", response.message, "success", "orders.php");
              } else {
                showSweetAlert("Submission Error", response.message, "error");
              }
            },
            error: function (xhr, status, error) {
              console.error("AJAX Error:", xhr.responseText);
              showSweetAlert("System Error", "The order could not be submitted due to a technical error.", "error");
            }
          });
        }
      });
    });

  });

</script>
