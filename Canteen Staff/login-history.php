<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Login History</title>

<!-- page content -->
<div class="right_col" role="main">
  <div class="">
    <div class="page-title">
      <div class="title_left">
        <h3>Login History</h3>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="x_panel">
      <div class="x_title">
        <h2>View login history records</h2>

        <div class="clearfix"></div>
      </div>
      <div class="x_content">
        <div class="card-box bg-white table-responsive pb-2">
          <table id="datatable" class="table table-striped table-bordered table-hover" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <?php if ($user_type === "Administrator"): ?>
                  <th>FULL NAME</th>
                <?php endif; ?>
                <th>LOGIN DATE & TIME</th>
                <th>LOGOUT DATE & TIME</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $i = 1;
              $logs = $db->getLoginHistory($id, $user_type);
              while ($row2 = $logs->fetch_assoc()) {
                // Build full name from tbluser data
                $fullname = trim($row2['firstname'] . ' ' . 
                              ($row2['middlename'] ? $row2['middlename'] . ' ' : '') . 
                              $row2['lastname'] . 
                              ($row2['suffix'] ? ', ' . $row2['suffix'] : ''));
                
                // If no user data found, show Unknown
                if (empty($row2['firstname']) && empty($row2['lastname'])) {
                  $fullname = "Unknown User";
                }
              ?>
                <tr>
                  <td><?= $i++ ?></td>

                  <?php if ($user_type === "Administrator"): ?>
                    <td><?= $fullname ?></td>
                  <?php endif; ?>

                  <td><?= date("F d, Y h:i A", strtotime($row2['login_datetime'])) ?></td>
                  <td>
                    <?php
                    if ($row2['logout_datetime'] == NULL && $row2['logout_remarks'] == 1) {
                      echo '<span class="badge badge-warning">Unable to logout last login</span>';
                    } else {
                      echo $row2['logout_datetime'] != NULL ? date("F d, Y h:i A", strtotime($row2['logout_datetime'])) : '<span class="badge badge-success">On-going session</span>';
                    }
                    ?>
                  </td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- /page content -->

<?php require_once 'footer.php'; ?>