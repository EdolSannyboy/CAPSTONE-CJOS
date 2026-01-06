<?php require_once 'header.php'; ?>
<title>Login | <?= $system_info['system_name'] ?></title>

<!-- Contact Section -->
<section id="contact" class="contact section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
    <img src="assets/img/logo/<?= $system_info['logo'] ?>" alt="Logo">
    <div><span>Access your</span> <span class="description-title">Account</span></div>
    <p>Please enter your credentials to continue</p>
  </div><!-- End Section Title -->

  <div class="container" data-aos="fade">
    <div class="row justify-content-center">
      <div class="col-lg-5">
        <form id="loginForm" method="post" role="form" class="php-email-form" auto-complete="off">

          <div class="form-group mt-3">
            <input type="email" class="form-control text-dark text-center" name="email" id="email" placeholder="Email" required="" auto-complete="off">
          </div>
          <div class="form-group mt-3" style="position: relative;">
            <input type="password" class="form-control text-dark text-center" name="password" id="password" placeholder="Password" required="" auto-complete="off">
          </div>
          <div>
            <input type="checkbox" class="form-check-input" id="showAllPasswords" onchange="toggleAllPasswords()">
            <label class="form-check-label" for="showAllPasswords">Show password</label>
          </div>
          <div class="form-links">
            <!-- <a href="register.php">Register here</a> -->
            <a href="forgot-password.php">Forgot password?</a>
          </div>

          <div class="text-center" style="margin-top: 15px;">
            <button type="submit">Login</button>
          </div>
        </form>
      </div><!-- End Login Form -->
    </div>
  </div>

</section>
<!-- /Contact Section -->

<?php require_once 'footer.php'; ?>

<script>
$(document).ready(function() {
    // Handle login form submission
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        if (!email || !password) {
            showSweetAlert("Error", "Please fill in all fields", "error");
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: 'php/authenticate.php',
            data: {
                action: 'login',
                email: email,
                password: password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showSweetAlert("Success!", response.message, "success", response.redirect_url);
                } else {
                    showSweetAlert("Error", response.message, "error");
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                showSweetAlert("Error", "An error occurred during login", "error");
            }
        });
    });
});
</script>
