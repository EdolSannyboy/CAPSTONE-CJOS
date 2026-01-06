<?php require_once 'header.php'; ?>
<title>Verify Account | <?= $system_info['system_name'] ?></title>

<!-- Contact Section -->
<section id="contact" class="contact section">
  <?php
  if (isset($_GET['email']) && isset($_GET['id']) && isset($_GET['type'])) {

    $email = $_GET['email'];
    $id = $_GET['id'];
    $type = $_GET['type'];

    $redirectURL = "";
    if ($type == "Administrator") {
      $redirectURL = "login-admin.php";
    } else {
      $redirectURL = "login.php";
    }

    $validUser = false;
    $error_title = "";
    $error_message = "";
    $fullname = "";
    $user_email = "";

    $fetch_user = $db->getUserByType($id, $type, $email);
    if ($fetch_user && $fetch_user->num_rows > 0) {
      $validUser = true;
      $user = $fetch_user->fetch_array();
      $fullname = ucwords($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname'] . ' ' . $user['suffix']);
      $user_email = $user['email'];
    } else {
      $validUser = false;
      $error_title = $type . " Not Found";
      $error_message = "The " . $type . " account does not exist. Please check the email and try again.";
    }

    if ($validUser) {
      ?>
      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt="Logo">
        <div><span>Verify Your </span> <span class="description-title">Account</span></div>
        <p>Please verify your account to proceed by clicking the button below</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade">
        <div class="row justify-content-center">
          <div class="col-lg-5">

            <form id="verifyAccountForm" method="post" role="form" class="php-email-form" auto-complete="off">
              <input type="hidden" name="email" id="email" value="<?= $email; ?>">
              <input type="hidden" name="user_ID" id="user_ID" value="<?= $id; ?>">
              <input type="hidden" name="type" id="type" value="<?= $type; ?>">

              <div class="text-center" style="margin-top: 15px;">
                <button type="submit" id="submit_button">Verify Account</button>
              </div>
            </form>

          </div><!-- End Login Form -->
        </div>
      </div>
      <?php
    } else {
      require_once 'error.php';
    }

  } else {
    $error_title = "Invalid Request";
    $error_message = "The required parameters are missing. Please try again.";
    require_once 'error.php';
  }
  ?>
</section>
<!-- /Contact Section -->

<?php require_once 'footer.php'; ?>