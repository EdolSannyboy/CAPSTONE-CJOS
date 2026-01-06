<?php
require_once 'header.php';

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

// Fetch items for the service select (including stock and low stock warnings)
$items = [];
$lowStockItems = [];
$criticalStockItems = [];
$outOfStockItems = [];
try {
  $conn = $db->getConnection();
  $stmt = $conn->prepare("SELECT item_id, item_name, item_unit_price, stock_qty, low_stock_threshold, item_added_by FROM tblitem WHERE 1 ORDER BY item_name ASC");
  if ($stmt && $stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
      $items[] = $row;
      
      // Categorize items by stock status
      $stockQty = isset($row['stock_qty']) ? (int)$row['stock_qty'] : 0;
      $threshold = isset($row['low_stock_threshold']) ? (int)$row['low_stock_threshold'] : 10;
      
      if ($stockQty <= 0) {
        $outOfStockItems[] = $row;
      } elseif ($stockQty <= $threshold) {
        $criticalStockItems[] = $row;
      } elseif ($stockQty <= ($threshold * 2)) {
        $lowStockItems[] = $row;
      }
    }
    $stmt->close();
  }
} catch (Exception $e) {
  // In case of error, keep arrays empty so the select still renders
}
?>
<title>Create Order | <?= $system_info['system_name'] ?></title>
<style>
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

<section id="contact" class="contact section bg-light">
  <div class="container section-title" data-aos="fade-up">
    <div><span>Create</span> <span class="description-title">Order</span></div>
    <p>Fill out the required fields below</p>
  </div>

  <div class="container" data-aos="fade-up">
    <!-- Low Stock Warnings -->
    <?php if (!empty($outOfStockItems) || !empty($criticalStockItems) || !empty($lowStockItems)): ?>
      <div class="stock-warnings">
        <?php if (!empty($outOfStockItems)): ?>
          <div class="stock-warning out-of-stock">
            <h5><i class="bi bi-exclamation-triangle-fill"></i> Out of Stock Items</h5>
            <p class="mb-2">The following items are currently out of stock and cannot be ordered:</p>
            <ul class="mb-0">
              <?php foreach ($outOfStockItems as $item): ?>
                <li><?= htmlspecialchars($item['item_name']) ?> - Current Stock: 0</li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($criticalStockItems)): ?>
          <div class="stock-warning critical">
            <h5><i class="bi bi-exclamation-circle-fill"></i> Critical Stock Level</h5>
            <p class="mb-2">The following items are at or below their minimum threshold:</p>
            <ul class="mb-0">
              <?php foreach ($criticalStockItems as $item): ?>
                <li><?= htmlspecialchars($item['item_name']) ?> - Current Stock: <?= (int)$item['stock_qty'] ?> (Threshold: <?= (int)$item['low_stock_threshold'] ?>)</li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($lowStockItems)): ?>
          <div class="stock-warning">
            <h5><i class="bi bi-info-circle-fill"></i> Low Stock Alert</h5>
            <p class="mb-2">The following items are running low on stock:</p>
            <ul class="mb-0">
              <?php foreach ($lowStockItems as $item): ?>
                <li><?= htmlspecialchars($item['item_name']) ?> - Current Stock: <?= (int)$item['stock_qty'] ?> (Threshold: <?= (int)$item['low_stock_threshold'] ?>)</li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

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

        <!-- Order Item Template -->
        <div class="order-item border rounded p-3 mb-3">
          <div class="row g-3 align-items-end">
            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
              <label for="description">Article Description / Services</label>
              <select name="description[]" class="form-control form-select description" onchange="updatePrice()"
                style="color: #000; background-color: #fff;">
                <option value="" disabled selected>Select a service</option>
                <?php foreach ($items as $item): ?>
                  <?php
                    $stockQty = isset($item['stock_qty']) ? (int)$item['stock_qty'] : 0;
                    $threshold = isset($item['low_stock_threshold']) ? (int)$item['low_stock_threshold'] : 10;
                    $disabled = $stockQty <= 0 ? 'disabled' : '';
                    $label = htmlspecialchars($item['item_name']);
                    
                    // Determine stock status and styling
                    if ($stockQty <= 0) {
                      $stockStatus = 'OUT';
                      $stockClass = 'stock-out';
                      $label .= ' (Out of Stock)';
                    } elseif ($stockQty <= $threshold) {
                      $stockStatus = 'CRITICAL';
                      $stockClass = 'stock-critical';
                      $label .= ' - Stock: ' . $stockQty . ' (Critical!)';
                    } elseif ($stockQty <= ($threshold * 2)) {
                      $stockStatus = 'LOW';
                      $stockClass = 'stock-low';
                      $label .= ' - Stock: ' . $stockQty . ' (Low)';
                    } else {
                      $stockStatus = 'OK';
                      $stockClass = 'stock-normal';
                      $label .= ' - Stock: ' . $stockQty;
                    }
                  ?>
                  <option value="<?= htmlspecialchars($item['item_id']) ?>"
                          data-price="<?= htmlspecialchars($item['item_unit_price']) ?>"
                          data-stock="<?= $stockQty; ?>"
                          data-threshold="<?= $threshold; ?>"
                          data-status="<?= $stockStatus; ?>" 
                          <?= $disabled; ?>>
                    <?= $label; ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-12">
              <label for="price">Price Per Unit (₱)</label>
              <input type="number" name="price[]" class="form-control price" readonly
                style="color: #000; background-color: #fff;">
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-12">
              <label for="quantity">No. of Pax</label>
              <input type="number" name="quantity[]" class="form-control quantity" min="1" value="1"
                oninput="calculateTotal()" style="color: #000; background-color: #fff;">
            </div>
            <div class="col-md-12 mt-2">
              <label class="form-label">Remarks / Instructions</label>
              <textarea name="remarks[]" class="form-control" rows="1"
                placeholder="Additional instructions (e.g., no onions, add sauce)"
                style="color: #000; background-color: #fff;"></textarea>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-6 col-12">
              <label for="total">Total (₱)</label>
              <input type="number" name="total[]" class="form-control total" readonly
                style="color: #000; background-color: #fff;">
            </div>
            <div class="col-md-12 text-end mt-2">
              <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn">
                <i class="bi bi-trash"></i> Remove
              </button>
            </div>
          </div>
        </div>

      </div>

      <div class="text-end">
        <button type="button" id="addItemBtn" class="buy-btn">
          <i class="bi bi-plus-circle"></i> Add Another Food
        </button>
      </div>

      <!-- <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button> -->
      <button type="submit"><i class="fa fa-save"></i> Submit Order</button>
    </form>


  </div>
</section>

<?php require_once 'footer.php'; ?>

<!-- Date range picker (Bootstrap-compatible) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

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

    if (hour < 7 || hour >= 17) {
      showSweetAlert("Information", "Pickup can only be scheduled between **7:00 AM and 5:00 PM**.", "info");
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
          url: 'php/processes.php',
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

  function generateSlotsFromRange(start, end) {
    const container = document.getElementById('datetime-slots-container');
    if (!container) return;

    container.innerHTML = '';

    if (!start || !end || typeof moment === 'undefined') return;

    const startMoment = moment(start);
    const endMoment = moment(end);
    if (!startMoment.isValid() || !endMoment.isValid() || endMoment.isBefore(startMoment, 'day')) {
      showSweetAlert('Information', 'Invalid date range. Please select a valid start and end date.', 'info');
      return;
    }

    // Always use 5:00 PM as the pickup time for all dates in the range
    const hour = 17;
    const minute = 0;

    let current = startMoment.clone().startOf('day');
    let hasValid = false;

    while (!current.isAfter(endMoment, 'day')) {
      const slotMoment = current.clone().hour(hour).minute(minute).second(0);
      const value = slotMoment.format('YYYY-MM-DD[T]HH:mm');

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'needed_datetime[]';
      input.value = value;

      container.appendChild(input);

      // Reuse existing validation rules
      validateDateTime(input);
      if (input.value !== '') {
        hasValid = true;
      } else {
        // If invalid, remove it
        container.removeChild(input);
      }

      current.add(1, 'day');
    }

    if (!hasValid) {
      showSweetAlert('Information', 'No valid pickup dates were generated from the selected range.', 'info');
    }
  }

  document.addEventListener('DOMContentLoaded', function () {

    const rangeInput = document.getElementById('needed_range');
    if (rangeInput && typeof $(rangeInput).daterangepicker === 'function') {
      $(rangeInput).daterangepicker({
        autoUpdateInput: true,
        timePicker: true,
        timePicker24Hour: false,
        timePickerIncrement: 30,
        locale: {
          format: 'YYYY-MM-DD HH:mm',
          cancelLabel: 'Clear'
        }
      }, function (start, end) {
        generateSlotsFromRange(start, end);
      });

      // Hide the end-time picker so only one time selector is visible
      const drp = $(rangeInput).data('daterangepicker');
      if (drp && drp.container) {
        drp.container.find('.drp-calendar.right .calendar-time').hide();
      }

      $(rangeInput).on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
        const container = document.getElementById('datetime-slots-container');
        if (container) {
          container.innerHTML = '';
        }
      });
    }

    const container = document.getElementById('orderItemsContainer');
    const addBtn = document.getElementById('addItemBtn');

    addBtn.addEventListener('click', () => {
      const newItem = container.firstElementChild.cloneNode(true);

      newItem.querySelectorAll('input, textarea, select').forEach(el => {
        if (el.tagName === 'SELECT') {
          el.selectedIndex = 0;
        } else if (el.type === 'number') {
          el.value = el.classList.contains('quantity-input') ? 1 : '';
        } else {
          el.value = '';
        }
      });

      container.appendChild(newItem);
    });

    container.addEventListener('click', (e) => {
      if (e.target.closest('.remove-item-btn')) {
        const items = container.querySelectorAll('.order-item');
        if (items.length > 1) {
          e.target.closest('.order-item').remove();
        } else {
          alert("You must have at least one food item.");
        }
      }
    });

    container.addEventListener('change', (e) => {
      if (e.target.classList.contains('description')) {
        const item = e.target.closest('.order-item');
        const selectedOption = e.target.options[e.target.selectedIndex];
        const price = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;
        const threshold = parseInt(selectedOption.getAttribute('data-threshold')) || 10;
        const stockStatus = selectedOption.getAttribute('data-status') || 'OK';

        const priceInput = item.querySelector('.price');
        const qtyInput = item.querySelector('.quantity');
        const totalInput = item.querySelector('.total');

        priceInput.value = price.toFixed(2);
        
        // Set max quantity based on available stock
        qtyInput.max = stock;
        
        let currentQty = parseFloat(qtyInput.value) || 0;
        if (stock > 0 && currentQty > stock) {
          currentQty = stock;
          qtyInput.value = stock;
          showSweetAlert("Information", `Maximum available stock for this item is ${stock}. Quantity has been adjusted.`, "info");
        }
        
        totalInput.value = (price * currentQty).toFixed(2);
        
        // Show stock status warning if needed
        if (stockStatus === 'CRITICAL') {
          showSweetAlert("Critical Stock Warning", `This item is at critical stock level (${stock} remaining). Please consider ordering less or restocking soon.`, "warning");
        } else if (stockStatus === 'LOW') {
          showSweetAlert("Low Stock Alert", `This item is running low on stock (${stock} remaining). It's within the low stock threshold range (≤ ${threshold * 2}). Consider restocking soon.`, "warning");
        } else if (stock > 0 && stock <= threshold) {
          showSweetAlert("Threshold Alert", `This item has reached its low stock threshold (${stock} remaining, threshold: ${threshold}). Immediate restocking recommended.`, "warning");
        }
      }
    });

    container.addEventListener('input', (e) => {
      if (e.target.classList.contains('quantity')) {
        const item = e.target.closest('.order-item');
        const price = parseFloat(item.querySelector('.price').value) || 0;
        const selectedItem = item.querySelector('.description');
        const selectedOption = selectedItem ? selectedItem.options[selectedItem.selectedIndex] : null;
        const stock = selectedOption ? parseInt(selectedOption.getAttribute('data-stock')) || 0 : 0;
        const threshold = selectedOption ? parseInt(selectedOption.getAttribute('data-threshold')) || 10 : 10;
        const stockStatus = selectedOption ? selectedOption.getAttribute('data-status') || 'OK' : 'OK';
        let qty = parseFloat(e.target.value) || 0;

        if (stock > 0 && qty > stock) {
          qty = stock;
          e.target.value = stock;
          showSweetAlert("Information", `Quantity cannot exceed available stock (${stock}). It has been adjusted to the maximum allowed.`, "info");
        }

        // Warn if ordering too much from low stock items
        if (stockStatus === 'CRITICAL' && qty >= stock) {
          showSweetAlert("Critical Stock Warning", `You are ordering the last ${qty} units of this item. This will deplete the stock completely!`, "warning");
        } else if (stockStatus === 'LOW' && qty >= (stock - threshold)) {
          showSweetAlert("Low Stock Warning", `You are ordering most of the remaining stock (${qty} of ${stock} available). This item is within the low stock threshold range. Consider restocking soon.`, "warning");
        } else if (stock > 0 && stock <= threshold && qty > 0) {
          showSweetAlert("Threshold Alert", `This item is within the low stock threshold range (${stock} remaining, threshold: ${threshold}). Be cautious with this order.`, "warning");
        }

        const totalInput = item.querySelector('.total');
        totalInput.value = (price * qty).toFixed(2);
      }
    });
  });

  $(document).ready(function () {
    $('#createOrderForm').submit(function (e) {
      e.preventDefault();

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
            url: 'php/processes.php',
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