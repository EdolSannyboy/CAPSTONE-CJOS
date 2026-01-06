<?php require_once 'header.php'; ?>
<title>Send Code | <?= $system_info['system_name'] ?></title>

<!-- Contact Section -->
<section id="contact" class="contact section">
  <?php
  if (isset($_GET['email']) && isset($_GET['id']) && isset($_GET['type'])) {

    $email = $_GET['email'];
    $id = $_GET['id'];
    $type = $_GET['type'];

    $validUser = false;
    $error_title = "";
    $error_message = "";
    $img_path = "";
    $fullname = "";
    $user_email = "";

    $fetch_user = $db->getUserByType($id, $type, $email);
    if ($fetch_user && $fetch_user->num_rows > 0) {
      $validUser = true;
      $user = $fetch_user->fetch_array();
      $fullname = ucwords($user['firstname'] . ' ' . $user['middlename'] . ' ' . $user['lastname'] . ' ' . $user['suffix']);
      $user_email = $user['email'];
      switch ($type) {
        case 'Administrator':
            $folder = "admin";
            break;
        case 'Canteen Staff':
            $folder = "canteen-staff";
            break;
        case 'Canteen Manager':
            $folder = "chefs";
            break;
        default:
            $folder = "others";
            break;
      }
      $img_path = "assets/img/{$folder}/" . $user['image'];
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
        <div><span>Send code </span> <span class="description-title">via email</span></div>
        <p>A verification code will be sent to your email to reset your password.</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade">
        <div class="row justify-content-center">
          <div class="col-lg-5">

            <form id="sendCodeForm" method="post" role="form" class="php-email-form" auto-complete="off">
              <input type="hidden" name="email" id="email" value="<?= $email; ?>">
              <input type="hidden" name="user_ID" id="user_ID" value="<?= $id; ?>">
              <input type="hidden" name="type" id="type" value="<?= $type; ?>">

              <div class="col-12 text-center">
                <div class="input-group mb-3">
                  <img src="<?= $img_path ?>" alt="Profile Image" class="d-block mx-auto mb-n3 rounded-circle" style="width:100px; height:100px;">
                </div>
                <p class="mt-4"><?= ucwords($fullname) ?></p>
                <p class="mb-3">We can send a login code to: <strong><?= $user_email; ?></strong></p>
              </div>
              <div class="text-center" style="margin-top: 20px;">
                <button type="submit" class="mb-2">Continue</button>
                <p class="d-block m-auto"><a href="forgot-password.php?type=<?= $type ?>">Not you?</a></p>
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

