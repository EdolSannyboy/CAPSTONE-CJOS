<?php require_once 'header.php'; ?>
<title>Forgot password | <?= $system_info['system_name'] ?></title>

<!-- Contact Section -->
<section id="contact" class="contact section">

  <?php
  $allowedTypes = ["Administrator", "Canteen Staff", "Canteen Manager"];
  if (isset($_GET['type']) && in_array($_GET['type'], $allowedTypes)) {
    $type = $_GET['type'];

    $folderMap = [
        "Administrator" => ["login" => "login-admin.php", "folder" => "admin"],
        "Canteen Staff" => ["login" => "login.php", "folder" => "canteen-staff"],
        "Canteen Manager" => ["login" => "login.php", "folder" => "chefs"]
    ];

    $redirectURL = $folderMap[$type]['login'];
    $imgFolder = $folderMap[$type]['folder'];

      ?>
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt="Logo">
        <div><span>Forgot</span> <span class="description-title">Password</span></div>
        <p>Enter your email to search for your account.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade">
        <div class="row justify-content-center">

          <div class="col-lg-5">

            <form id="forgotPasswordForm" method="post" role="form" class="php-email-form" auto-complete="off">
              <input type="hidden" name="type" class="form-control" id="type" value="<?= $type ?>" required="">
              <div class="form-group">
                <input type="email" class="form-control text-dark" name="email" id="email" placeholder="Email" required=""
                  auto-complete="off">
              </div>
              <div class="form-links">
                <a href="<?= $redirectURL ?>">Login</a>
              </div>

              <div class="text-center" style="margin-top: 15px;">
                <button type="submit">Search</button>
              </div>
            </form>
          </div><!-- End Login Form -->

        </div>
      </div>
      <?php
    
  } else {
    // Show user type selection form when no type is provided
    ?>
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
      <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt="Logo">
      <div><span>Forgot</span> <span class="description-title">Password</span></div>
      <p>Select your account type to reset your password.</p>
    </div><!-- End Section Title -->

    <div class="container" data-aos="fade">
      <div class="row justify-content-center">
        <div class="col-lg-5">
          <form method="get" role="form" class="php-email-form">
            <div class="form-group">
              <label for="type">Select Account Type:</label>
              <select name="type" id="type" class="form-control text-dark" required="">
                <option value="">-- Select Account Type --</option>
                <option value="Administrator">Administrator</option>
                <option value="Canteen Staff">Canteen Staff</option>
                <option value="Canteen Manager">Canteen Manager</option>
              </select>
            </div>
            <div class="text-center" style="margin-top: 15px;">
              <button type="submit">Continue</button>
            </div>
          </form>
          
          <div class="form-links text-center mt-3">
            <a href="login.php">Back to Login</a>
          </div>
        </div>
      </div>
    </div>
    <?php
  }
  ?>
</section>
<!-- /Contact Section -->

<?php require_once 'footer.php'; ?>

