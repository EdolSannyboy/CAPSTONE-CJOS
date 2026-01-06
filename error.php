<div class="container section-title" data-aos="fade-up">
  <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt="Logo">
  <div><span class="description-title"><?= isset($error_title) ? $error_title : "Error" ?></span></div>
  <p><?= isset($error_message) ? $error_message : "Something went wrong!" ?></p>
</div><!-- End Section Title -->

<div class="container" data-aos="fade">
  <div class="form-links text-center">
    <p>If you think this is a mistake, please contact support.</p>
    <a href="<?= $redirectURL ?>">Back to login page</a>
  </div>
</div>