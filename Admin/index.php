<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Professional Dashboard</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="page-title">
    <div class="title_left">
      <h3>Professional Dashboard</h3>
    </div>
  </div>

  <div class="clearfix"></div>

  <div class="x_content">
    <div class="row">

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-shield"></i></div>
          <div class="count" id="admin_count"></div>
          <h3>Administrators</h3>
          <h6 class="ml-2 mt-2 d-none" id="admin_gender"></h6>
        </div>
      </div>

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-user"></i></div>
          <div class="count" id="staff_count"></div>
          <h3>Canteen Staff</h3>
          <h6 class="ml-2 mt-2 d-none" id="staff_gender"></h6>
        </div>
      </div>

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-users"></i></div>
          <div class="count" id="manager_count"></div>
          <h3>Canteen Manager</h3>
          <h6 class="ml-2 mt-2 d-none" id="manager_gender"></h6>
        </div>
      </div>

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-group"></i></div>
          <div class="count" id="total_users"></div>
          <h3>Total Users</h3>
          <h6 class="ml-2 mt-2 d-none" id="total_gender"></h6>
        </div>
      </div>

    </div>

    <div class="row">

      <!-- Order Status Tiles -->
      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-hourglass"></i></div>
          <div class="count" id="ongoing_count">0</div>
          <h3>On-Going</h3>
        </div>
      </div>

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-money"></i></div>
          <div class="count" id="completed_count">0</div>
          <h3>Completed</h3>
        </div>
      </div>

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <div class="tile-stats p-2">
          <div class="icon"><i class="fa fa-times"></i></div>
          <div class="count" id="cancelled_count">0</div>
          <h3>Cancelled</h3>
        </div>
      </div>

    </div>
  </div>

</div>
<!-- /page content -->
<br><br><br><br><br><br><br><br><br><br>

<?php require_once 'footer.php'; ?>
<script>
  $(function () {

    // Trigger chart update on filter change

    function renderUserCharts() {
      $.ajax({
        url: '../php/analytics.php?action=get_all_users',
        method: 'GET',
        dataType: 'json',
        success: function (data) {
          console.log("User Counts Data:", data);

          const formatGender = (obj) => `Male: ${obj.Male || 0}; Female: ${obj.Female || 0}`;

          // Administrators
          $('#admin_count').text((data.Administrators.Male || 0) + (data.Administrators.Female || 0));
          $('#admin_gender').text(formatGender(data.Administrators));

          // Canteen Staff
          $('#staff_count').text((data.CanteenStaff.Male || 0) + (data.CanteenStaff.Female || 0));
          $('#staff_gender').text(formatGender(data.CanteenStaff));

          // Canteen Manager
          $('#manager_count').text((data.CanteenManager.Male || 0) + (data.CanteenManager.Female || 0));
          $('#manager_gender').text(formatGender(data.CanteenManager));

          // Total Users
          const totalUsers =
            (data.Administrators.Male || 0) + (data.Administrators.Female || 0) +
            (data.CanteenStaff.Male || 0) + (data.CanteenStaff.Female || 0) +
            (data.CanteenManager.Male || 0) + (data.CanteenManager.Female || 0)

          $('#total_users').text(totalUsers);
          $('#total_gender').text(formatGender(data.TotalGender));
        },
        error: function (error) {
          console.error("Error fetching user counts data:", error);
        }
      });
    }

    function renderOrderCounts() {
        $.ajax({
            url: '../php/analytics.php?action=get_order_counts',
            method: "GET",
            dataType: "json",
            success: function (data) {
                $('#ongoing_count').text(data.ongoing_count);
                $('#completed_count').text(data.completed_count);
                $('#cancelled_count').text(data.cancelled_count);
            },
            error: function (err) {
                $('#ongoing_count').text('Err');
                $('#completed_count').text('Err');
                $('#cancelled_count').text('Err');
                console.error("Error fetching order counts:", err);
            }
        });
    }

    renderOrderCounts();
    renderUserCharts();
   
  });
</script>