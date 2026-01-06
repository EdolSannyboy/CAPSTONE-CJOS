<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Professional Dashboard</title>

<!-- page content -->
<div class="right_col" role="main">

  <div class="title_left">
      <h3>Professional Dashboard</h3>
    </div>


  <div class="clearfix"></div>


  <div class="x_content">
    <div class="row">
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

      <div class="animated flipInY col-lg-3 col-md-3 col-sm-6">
        <a href="items.php?filter=low_stock" style="text-decoration:none; color:inherit;">
          <div class="tile-stats p-2">
            <div class="icon"><i class="fa fa-exclamation-triangle"></i></div>
            <div class="count" id="low_stock_count">0</div>
            <h3>Low Stock Items</h3>
          </div>
        </a>
      </div>


    </div>


  </div>


</div>
<!-- /page content -->
<br><br><br><br><br><br><br><br><br><br>


<?php require_once 'footer.php'; ?>
<script>
  $(function () {

    function renderOrderCounts() {
        $.ajax({
            url: '../php/analytics.php?action=get_order_counts',
            method: "GET",
            dataType: "json",
            success: function (data) {
                $('#ongoing_count').text(data.ongoing_count);
                $('#completed_count').text(data.completed_count);
                $('#cancelled_count').text(data.cancelled_count);
                if (typeof data.low_stock_count !== 'undefined') {
                    $('#low_stock_count').text(data.low_stock_count);
                }
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

  });
</script>