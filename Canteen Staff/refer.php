<?php
require_once 'header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<title>Create Order | <?= $system_info['system_name'] ?></title>

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
            <select name="office_name" id="office_select" class="form-control form-select"
              style="color: #000; background-color: #fff;" required onchange="handleOfficeChange()">
              <option value="" disabled selected>Select Office</option>
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
              <option value="others">Others (Specify)</option>
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
              <select name="description[]" class="form-control form-select description" style="color: #000; background-color: #fff;">
                <option value="" disabled selected>Select a service</option>
                <option value="AM Snacks" data-price="80">AM Snacks</option>
                <option value="PM Snacks" data-price="80">PM Snacks</option>
                <option value="Snacks" data-price="70">Snacks</option>
                <option value="Breakfast" data-price="90">Breakfast</option>
                <option value="Lunch" data-price="100">Lunch</option>
                <option value="Dinner" data-price="120">Dinner</option>
              </select>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-12">
              <label for="price">Price Per Unit (₱)</label>
              <input type="number" name="price[]" class="form-control price" readonly
                style="color: #000; background-color: #fff;">
            </div>
            <div class="col-lg-3 col-md-3 col-sm-6 col-12">
              <label for="quantity">No. of Pax</label>
              <input type="number" name="quantity[]" class="form-control quantity" min="1" value="1"  style="color: #000; background-color: #fff;">
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
<script>

 
  function handleOfficeChange() {
    const selectElement = document.getElementById('office_select');
    const emailInput = document.getElementById('office_email');
    const othersInputGroup = document.getElementById('others_input_group');
    const otherOfficeNameInput = document.getElementById('other_office_name');
    const selectedOffice = selectElement.value;

    const officeEmails = {
      'CITC': 'citc.cdo@ustp.edu.ph',
      'CEA': 'cea@ustp.edu.ph',
      'COT': 'helengrace.gonzales@ustp.edu.ph',
      'CSM': 'elmerc.castillano@ustp.edu.ph',
      'CSTE': 'grace.pimentel@ustp.edu.ph',
      'Office of the President': 'president.office@ustp.edu.ph',
      'Finance / Office of the Vice Chancellor for Finance and Administration': 'vcfa.cdo@ustp.edu.ph',
      'USG / University Student Government': 'cdo.ustp.edu.ph', // Note: This looks like a URL, but using as provided.
      'Office of the Chancellor / Chancellor Office': 'chancelloroffice.cdo@ustp.edu.ph',
      'Admission Office / Admission and Scholarship Office (ASO)': 'admissionunit.asocdo@ustp.edu.ph',
      'Registrar Office': 'registrar.cdo@ustp.edu.ph',
      'Office of Student Affairs (OSA)': 'studentaffairs-cdo@ustp.edu.ph',
      'Federation of Accredited Extra-curricular Student Organizations (FAESO)': 'faeso@ustp.edu.ph',
    };

    if (selectedOffice in officeEmails) {
      emailInput.value = officeEmails[selectedOffice];
      emailInput.value = officeEmails[selectedOffice];
      emailInput.setAttribute('name', 'email');
    } else if (selectedOffice === 'others') {
      emailInput.value = 'N/A';
      emailInput.setAttribute('name', 'office_email');
    } else {
      emailInput.value = '';
    }


    if (selectedOffice === 'others') {
      othersInputGroup.style.display = 'block';
      otherOfficeNameInput.setAttribute('required', 'required');
      otherOfficeNameInput.focus();
    } else {
      othersInputGroup.style.display = 'none';
      otherOfficeNameInput.removeAttribute('required');
      otherOfficeNameInput.value = '';
    }
  }

  const MIN_DATETIME = "<?= date('Y-m-d\TH:i'); ?>";
  const MIN_DATE = new Date(MIN_DATETIME).setHours(0, 0, 0, 0);

  document.addEventListener('DOMContentLoaded', function () {

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
            if(selectedDates.length > 0) {
                instance.input.removeAttribute('required');
            } else {
                instance.input.setAttribute('required', 'required');
            }
        }
    });
   
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

        const priceInput = item.querySelector('.price');
        const qtyInput = item.querySelector('.quantity');
        const totalInput = item.querySelector('.total');

        priceInput.value = price.toFixed(2);
        totalInput.value = (price * (parseFloat(qtyInput.value) || 0)).toFixed(2);
      }
    });

    container.addEventListener('input', (e) => {
      if (e.target.classList.contains('quantity')) {
        const item = e.target.closest('.order-item');
        const price = parseFloat(item.querySelector('.price').value) || 0;
        const qty = parseFloat(e.target.value) || 0;
        const totalInput = item.querySelector('.total');
        totalInput.value = (price * qty).toFixed(2);
      }
    });
  });

  $(document).ready(function () {
    $('#createOrderForm').submit(function (e) {
      e.preventDefault();

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
        if (hour < 7 || hour >= 17) {
            showSweetAlert("Invalid Time", "Pickup time must be between **7:00 AM and 5:00 PM**.", "info");
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