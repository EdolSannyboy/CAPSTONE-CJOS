<?php

  require_once '../php/db_config.php';
  require_once '../php/classes.php';
  $db = new db_class();

  if(!isset($_SESSION['user_ID'])) {
    header("Location: ../index.php");
    exit();
  }

  if($_SESSION['user_type'] !== "Administrator" && $_SESSION['user_type'] !== "Staff") {
    header("Location: ../index.php");
    exit();
  }

  $id = $_SESSION['user_ID'];
  $login_time = $_SESSION['login_time'] ?? null;
  $user_type = $_SESSION['user_type'];

  // Load full user details from tbluser using the logged-in user_ID
  $conn = $db->getConnection();
  $stmt = $conn->prepare("SELECT u.*, l.userlevel_name FROM tbluser u INNER JOIN tbluserlevel l ON u.userlevel_id = l.userlevel_id WHERE u.user_id = ? LIMIT 1");
  if ($stmt) {
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
  }

  if (empty($row)) {
    $row = [
      'firstname' => $_SESSION['fullname'] ?? 'User',
      'lastname'  => '',
      'image'     => 'avatar.png',
      'userlevel_name' => $user_type ?? 'Administrator',
    ];
  }

  switch ($row['userlevel_name'] ?? $user_type) {
    case "Administrator":
      $badgeClass = 'badge-primary';
      $userTypeName = 'Administrator';
      break;
    case "Staff":
    case "Canteen Staff":
      $badgeClass = 'badge-info';
      $userTypeName = 'Canteen Staff';
      break;
    default:
      $badgeClass = 'badge-dark';
      $userTypeName = $row['userlevel_name'] ?? 'Unknown';
      break;
  }

  require_once 'header.php';
  
  $current_page = basename($_SERVER['PHP_SELF']);
  $ongoing_count = $db->countOnGoingOrders(); 
 
  // Low stock and out of stock item counts for sidebar alerts
  $low_stock_count = 0;
  $out_of_stock_count = 0;
  $itemQuery = "
    SELECT 
      SUM(CASE WHEN stock_qty > 0 AND stock_qty <= low_stock_threshold THEN 1 ELSE 0 END) AS low_stock_count,
      SUM(CASE WHEN stock_qty <= 0 THEN 1 ELSE 0 END) AS out_of_stock_count
    FROM tblitem
  ";
  if ($itemResult = $conn->query($itemQuery)) {
    if ($itemRow = $itemResult->fetch_assoc()) {
      $low_stock_count = (int) ($itemRow['low_stock_count'] ?? 0);
      $out_of_stock_count = (int) ($itemRow['out_of_stock_count'] ?? 0);
    }
  }
?>
<div class="col-md-3 left_col menu_fixed" style="height:100vh; overflow-y:auto;"><!-- FOR FIXED SIDEBAR -->
  <div class="left_col scroll-view">
    <div class="navbar nav_title" style="border: 0;">
      <a href="dashboard.php" class="site_title"><img src="../assets/img/logo/<?= $system_info['logo'] ?>" alt="..." class="img-circle" width="40"> <span><?= $system_info['system_name'] ?></span></a>
    </div>

    <div class="clearfix"></div>

    <!-- menu profile quick info -->
   <!--  <div class="profile clearfix">
      <div class="profile_pic">
        <img src="../assets/img/logo/<?= $system_info['logo'] ?>" alt="..." class="img-circle profile_img">
      </div>
      <div class="profile_info">
        <span>Welcome,</span>
        <h2><?= ucwords($row['firstname'].' '.$row['lastname']) ?></h2>
        <span class="d-none"><?= $userTypeName ?></span>
      </div>
    </div> -->
    <!-- /menu profile quick info -->

    <br />
  <!-- sidebar menu -->
  <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
    <div class="menu_section">

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">General</h3>
      <ul class="nav side-menu mb-3">
        <li><a href="index.php"><i class="fa fa-tachometer"></i> Dashboard</a></li>
        <li>
            <a href="orders.php" class="position-relative">
              <i class="fa fa-shopping-cart"></i> Orders
              <?php if ($ongoing_count > 0): ?>
                <span class="badge bg-danger position-absolute" 
                      style="font-size: 0.7rem; top: 0px; margin-left: 5px;">
                  <?= $ongoing_count; ?>
                </span>
              <?php endif; ?>
            </a>
          </li>
      </ul>

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">User Management</h3>
      <ul class="nav side-menu mb-3">
        <li>
          <a><i class="fa fa-users"></i> System Users <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li><a href="admin.php">Administrator</a></li>
            <li><a href="canteen_staff.php">Canteen Staff</a></li>
            <li><a href="canteen_manager.php">Canteen Manager</a></li>
          </ul>
        </li>
      </ul>

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">Office Management</h3>
      <ul class="nav side-menu mb-3">
        <li>
          <a><i class="fa fa-building"></i> Offices <span class="fa fa-chevron-down"></span></a>
          <ul class="nav child_menu">
            <li><a href="office_types.php">Office Types</a></li>
            <li><a href="offices.php">Offices</a></li>
            <li><a href="sub_offices.php">Departments</a></li>
          </ul>
        </li>
      </ul>

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">Item Management</h3>
      <ul class="nav side-menu mb-3">
        <li>
          <a href="items.php" class="position-relative">
            <i class="fa fa-cutlery"></i> Items
            <?php if (($low_stock_count + $out_of_stock_count) > 0): ?>
              <span class="badge bg-warning position-absolute" 
                    style="font-size: 0.7rem; top: 0px; margin-left: 5px;" 
                    title="Items with low or zero stock">
                <?= $low_stock_count + $out_of_stock_count; ?>
              </span>
            <?php endif; ?>
          </a>
        </li>
        <li>
          <a href="create_orders.php"><i class="fa fa-plus"></i> Create Order</a>
        </li>
      </ul>

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">Reports</h3>
        <ul class="nav side-menu mb-3">
          <li class="<?php echo ($current_page == 'report_orders.php') ? 'active' : ''; ?>">
            <a><i class="fa fa-clipboard"></i> Generate Reports <span class="fa fa-chevron-down"></span></a>
            <ul class="nav child_menu" style="<?php echo ($current_page == 'report_orders.php') ? 'display:block;' : ''; ?>">
              <li><a href="report_orders.php">Order Records</a></li>
            </ul>
          </li>
        </ul>

      <h3 style="opacity: .3; margin-bottom: -10px; margin-left: -8px;">Login</h3>
      <ul class="nav side-menu mb-3">
        <li>
          <a href="login-history.php">
            <i class="fa fa-history"></i> Login History
          </a>
        </li>
        <li class="<?php echo ($current_page == 'user_logs.php') ? 'active' : ''; ?>">
          <a href="user_logs.php">
            <i class="fa fa-list-alt"></i> User Activity Logs
          </a>
        </li>
      </ul>
    </div>

    

    </ul>
  </div>
  <!-- /sidebar menu -->

    <!-- /menu footer buttons -->
    <!-- <div class="sidebar-footer hidden-small">
      <a data-toggle="tooltip" data-placement="top" title="Settings">
        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
      </a>
      <a data-toggle="tooltip" data-placement="top" title="FullScreen">
        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
      </a>
      <a data-toggle="tooltip" data-placement="top" title="Lock">
        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
      </a>
      <a data-toggle="tooltip" data-placement="top" title="Logout" href="login.html">
        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
      </a>
    </div> -->
    <!-- /menu footer buttons -->
  </div>
</div>

<!-- top navigation -->
<div class="top_nav">
  <div class="nav_menu">
      <div class="nav toggle">
        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
      </div>
      <nav class="nav navbar-nav">

      <ul class=" navbar-right">
        <li class="nav-item dropdown open" style="padding-left: 15px;">
          <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
            <img src="../assets/img/admin/<?= !empty($row['image']) ? $row['image'] : 'avatar.png' ?>" alt=""> Welcome, <?= ucwords($row['firstname']) ?>!
          </a>
          <div class="dropdown-menu dropdown-usermenu pull-right mt-4" aria-labelledby="navbarDropdown">
              <a class="dropdown-item" href="profile.php">
                  <i class="fa fa-user"></i> Profile
              </a>
              <a class="dropdown-item" href="settings.php">
                  <!-- <span class="badge bg-red pull-right">50%</span> -->
                  <i class="fa fa-cog"></i> Settings
              </a>
              <a class="dropdown-item" href="#" id="signOutButton">
                  <i class="fa fa-sign-out pull-right"></i> Log Out
              </a>
          </div>
        </li>
      </ul>
      
    </nav>
  </div>
</div>
<!-- /top navigation -->