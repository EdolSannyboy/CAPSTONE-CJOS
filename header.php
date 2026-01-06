<?php

  require_once 'php/db_config.php';
  require_once 'php/classes.php';
  $db = new db_class();

  if (isset($_SESSION['user_ID'], $_SESSION['login_time'], $_SESSION['user_type']) && !in_array($_SESSION['user_type'], ['Office Staff']) ) {
    $redirectURL = determineRedirectURL($_SESSION['user_type']);
    if ($redirectURL) {
      header("Location: $redirectURL");
      exit();
    }
  }

  $system_settings = $db->getActiveSystemSettings();
  $system_info = null;

  if ($system_settings && $system_settings->num_rows === 0) {
      $system_info = [
          'system_name' => 'Default System Name',
          'address' => 'Default Address',
          'contact' => '09123456789',
          'email' => 'mail@gmail.com',
          'about_us' => 'Sample description',
          'logo' => 'avatar.png'
      ];
  } elseif ($system_settings) {
      $system_info = $system_settings->fetch_assoc();
  } else {
      // Handle the case where $system_settings is null or false
  }

  function setActive($page){
    return basename($_SERVER['PHP_SELF']) == $page ? 'active' : '';
  }
  $current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <!-- <title>Home | <?= $system_info['system_name'] ?></title> -->
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/logo/<?= $system_info['logo'] ?>" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Satisfy:wght@400&display=swap"
    rel="stylesheet">
  
  <!-- Font Awesome -->
  <link href="vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <!-- FullCalendar -->
  <link href="vendors/fullcalendar/dist/fullcalendar.min.css" rel="stylesheet">
  <link href="vendors/fullcalendar/dist/fullcalendar.print.css" rel="stylesheet" media="print">
  <!-- Sweetalert Files-->
  <link rel="stylesheet" href="assets/css/sweetalert2.min.css">
  <script src="assets/js/sweetalert2.all.min.js"></script>
  <script src="assets/js/alerts.js"></script>

  <!-- Datatables -->
  <link href="vendors/datatables.net-bs/css/dataTables.bootstrap.min.css" rel="stylesheet">
  <link href="vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css" rel="stylesheet">
  <link href="vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css" rel="stylesheet">
  <link href="vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css" rel="stylesheet">
  <link href="vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

  <style>
    .scrolled .header {
      --background-color: rgba(21, 17, 13, 0.85);
    }

    .contact .header {
      --background-color: rgba(21, 17, 13, 0.85);
    }

    #contact, #testimonials {
      padding-top: 125px;
    }

    .section-title {
      text-align: center;
      padding-bottom: 30px;
    }

    .section-title img {
      max-width: 120px;
      margin-bottom: 15px;
    }

    .section-title p {
      margin-top: 5px;
      font-size: 14px;
      color: #666;
    }

    .php-email-form .form-links {
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
      font-size: 14px;
    }

    .header .topbar .btn-logout {
      color: var(--contrast-color);
      background: #d32f2f;
      font-weight: 500;
      font-size: 14px;
      letter-spacing: 1px;
      display: inline-block;
      padding: 6px 30px 8px 30px;
      border-radius: 50px;
      transition: 0.3s;
      cursor: pointer
    }

    .header .topbar .btn-logout:hover {
      background: #b71c1c
    }

    .BookATable {
      color: var(--contrast-color);
      background: #f9a825;
      font-weight: 500;
      font-size: 16px;
      letter-spacing: 1px;
      display: inline-block;
      padding: 6px 30px 8px 30px;
      border-radius: 50px;
      transition: 0.3s;
      cursor: pointer
    }

    .BookATable:hover {
      opacity: 0.8;
    }

    .EditProfileButton,
    .confirmUpload,
    .cancelUpload {
      font-weight: 500;
      font-size: 15px;
      letter-spacing: 1px;
      display: inline-block;
      padding: 10px 40px 10px 40px;
      border-radius: 5px;
      transition: 0.3s;
      cursor: pointer;
      color: var(--contrast-color);
    }

    .EditProfileButton,
    .confirmUpload { background: #f9a825; }
    .cancelUpload { background: #7a746bff; }

    .EditProfileButton:hover,
    .confirmUpload:hover,
    .cancelUpload:hover { opacity: 0.8; }


    /* MENU SECTION */
    .add-to-cart-btn {
      background-color: #f9a825;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 20px; 
      /* border-radius: 50px;  */
      /* padding: 5px 15px; */
      font-size: 12px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .add-to-cart-btn:hover {
        background-color: #f57f17; 
    }

    .checkout-btn {
      background-color: #f9a825;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px; 
      padding: 8px 30px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .checkout-btn:hover {
        background-color: #f57f17; 
    }

    .rating-btn {
      background-color: #f9a825;
      padding: 8px 15px;
      font-size: 13px;
      color: white;
      font-weight: bold;
      border: none;
      border-radius: 5px; 
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .rating-btn:hover {
        background-color: #f57f17; 
    }

    .buy-btn {
        background-color: #ffffff;
        color: #f9a825; 
        border: 1px solid #f9a825;
        padding: 8px 15px;
        font-size: 13px;
        font-weight: bold;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    .buy-btn:hover {
        background-color: #f9a825; 
        color: #ffffff;
    }

    .fixed-img {
      height: 150px;
      object-fit: cover;
    }

    #searchInput {
      border: 1px solid #ccc;
      transition: border-color 0.3s ease;
    }

    #searchInput:focus {
      outline: none;
      border-color: #f57f17;
      box-shadow: none; 
    }

    .no-product-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 200px; 
      width: 100%;
    }

    .no-product-msg {
      padding: 15px 20px;
      background-color: #fff3e0; 
      border: 1px solid #f57f17;
      border-radius: 8px;
      color: #d84315; 
      font-weight: 500;
      font-size: 16px;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

    .quantity-group .quantity-input::-webkit-outer-spin-button,
    .quantity-group .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .quantity-group .quantity-input[type=number] {
        -moz-appearance: textfield;
    }

    .quantity-group .quantity-input {
        text-align: center;
        line-height: normal;
    }

    .quantity-group .quantity-input {
      border: 1px solid #e6e0d7ff;
      border-radius: 0; 
      outline: none; 
      box-shadow: none; 
    }

    .quantity-group .btn-decrement,
    .quantity-group .btn-increment {
      border: 1px solid #e6e0d7ff; 
      color: #f9a825;
      background-color: #fff;
    }

    .quantity-group .btn-decrement:hover,
    .quantity-group .btn-increment:hover {
      background-color: #f9a825; 
      color: #fff;
      border-color: #f9a825;
    }

    /* MENU SECTION */

    /* CART CHECKBOXES */
      .form-check-input {
      appearance: none;
      -webkit-appearance: none;
      -moz-appearance: none;
      width: 18px;
      height: 18px;
      border-radius: 4px;
      background-color: #fff;
      border: 2px solid #ccc;
      cursor: pointer;
      position: relative;
      transition: background-color 0.2s, border-color 0.2s;
    }

    /* Remove blue focus ring */
    .form-check-input:focus,
    .form-check-input:focus-visible {
      outline: none;
      box-shadow: none;
      border-color: #ccc; 
    }

    /* Checked state */
    .form-check-input:checked {
      background-color: #f57f17;
      border-color: #f57f17;
    }

    /* Checkmark */
    .form-check-input:checked::after {
      content: '';
      position: absolute;
      top: 2px;
      left: 5px;
      width: 4px;
      height: 8px;
      border: solid #fff;
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    .form-check-input:hover {
      border-color: #f57f17;
    }

    .cart-wrapper {
      overflow-x: auto;
    }

    .cart-row {
      min-width: 800px; 
    }
    /* END CART CHECKBOXES */

    /* Profile Tabs */
    .nav-tabs .nav-link.active {
      border: none;
      border-bottom: 2px solid #f57f17;
    }
    .nav-tabs .nav-link {
      border: none;
      color: #333;
    }
    /* End Profile Tabs */

  </style>
</head>

<body class="index-page">

  <header id="header" class="header fixed-top">
    
    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-end justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-phone d-flex align-items-center d-none d-lg-block">
            <span><?= $system_info['contact'] ?></span>
          </i>
          <i class="bi bi-clock ms-4 d-none d-lg-flex align-items-center">
            <span>Mon-Sat: 08:00 AM - 05:00 PM</span>
          </i>
        </div>

      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-cente">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="index.php" class="logo d-flex align-items-center">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <!-- <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt=""> -->
          <h1 class="sitename"><?= $system_info['system_name'] ?></h1>
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="index.php?#gallery">Home</a></li>
            <li><a href="about.php?#hero">About</a></li>
            <?php if ($current_page != 'login.php'): ?>
              <li><a href="login.php" class="cta-btn">Login</a></li>
            <?php endif; ?>
            <!-- <li><a href="index.php?#gallery">Gallery</a></li>
            <li><a href="index.php?#chefs">Chefs</a></li> -->
            <!-- <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Dropdown 1</a></li>
                <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                  <ul>
                    <li><a href="#">Deep Dropdown 1</a></li>
                  </ul>
                </li>
                <li><a href="#">Dropdown 2</a></li>
              </ul>
            </li> -->
            <!-- <li><a href="index.php?#contact">Contact</a></li> -->
            <?php 
            // if (isset($_SESSION['user_ID'], $_SESSION['login_time'], $_SESSION['user_type']) && in_array($_SESSION['user_type'], ['Office Staff'])): 
              ?>
            <!-- <li><a href="orders.php?#contact">Order Records</a></li> -->
            <?php 
          // endif; 
          ?>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
      </div>
    </div>
  </header>
  <main class="main"></main>