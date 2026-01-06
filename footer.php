</main>
<footer id="footer" class="footer dark-background">

    <div class="container">
        <div class="row gy-3 d-flex justify-content-between">
            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-geo-alt icon"></i>
                <div class="address">
                    <h4>Address</h4>
                    <p><?= $system_info['address'] ?></p>
                </div>

            </div>

            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-telephone icon"></i>
                <div>
                    <h4>Contact</h4>
                    <p>
                        <strong>Phone:</strong> <span><?= $system_info['contact'] ?></span><br>
                        <strong>Email:</strong> <span><?= $system_info['email'] ?></span><br>
                    </p>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 d-flex">
                <i class="bi bi-clock icon"></i>
                <div>
                    <h4>Opening Hours</h4>
                    <p>
                        <strong>Mon-Sat:</strong> <span>08:00 AM - 05:00 PM</span><br>
                        <strong>Sunday</strong>: <span>Closed</span>
                    </p>
                </div>
            </div>

            <!-- <div class="col-lg-3 col-md-6">
                <h4>Follow Us</h4>
                <div class="social-links d-flex">
                    <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                    <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
                </div>
            </div> -->

        </div>
    </div>

    <div class="container copyright text-center mt-4">
        <p>© <span>Copyright</span> <strong class="px-1 sitename"><?= $system_info['system_name'] ?></strong> <span>All Rights Reserved</span>
        </p>
        <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you've purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
            Designed by <a href="#"><?= $system_info['system_name'] ?> Researchers</a>
        </div>
    </div>

</footer>

<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- jQuery -->
<script src="assets/js/jquery/jquery.min.js"></script>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- <script src="assets/vendor/php-email-form/validate.js"></script> -->
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Datatables -->
<script src="vendors/datatables.net/js/jquery.dataTables.min.js"></script>
<script src="vendors/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
<script src="vendors/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
<script src="vendors/datatables.net-buttons-bs/js/buttons.bootstrap.min.js"></script>
<script src="vendors/datatables.net-buttons/js/buttons.flash.min.js"></script>
<script src="vendors/datatables.net-buttons/js/buttons.html5.min.js"></script>
<script src="vendors/datatables.net-buttons/js/buttons.print.min.js"></script>
<script src="vendors/datatables.net-fixedheader/js/dataTables.fixedHeader.min.js"></script>
<script src="vendors/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
<script src="vendors/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
<script src="vendors/datatables.net-responsive-bs/js/responsive.bootstrap.js"></script>
<script src="vendors/datatables.net-scroller/js/dataTables.scroller.min.js"></script>
<script src="vendors/jszip/dist/jszip.min.js"></script>
<script src="vendors/pdfmake/build/pdfmake.min.js"></script>
<script src="vendors/pdfmake/build/vfs_fonts.js"></script>


<!-- FullCalendar -->
<script src="vendors/moment/min/moment.min.js"></script>
<script src="vendors/fullcalendar/dist/fullcalendar.min.js"></script>
<!-- Chart JS -->
<script src="vendors/Chart.js/dist/Chart.min.js"></script>
<!-- Custom JS -->
<script src="assets/js/script.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>

<script>

    document.addEventListener("DOMContentLoaded", () => {
        const pages = [
            "index.php", "about.php", "login.php", "login-admin.php", "forgot-password.php",
            "send-code.php", "verify-code.php", "change-password.php",
            "account-verification.php", "register.php", "profile.php", 
            "cart.php", "checkout.php", "orders.php", "orders_today.php",
            "create_orders.php", "update_order.php"
        ];
        if (pages.some(p => location.pathname.includes(p))) {
            document.querySelector(".header").style.setProperty(
            "--background-color", "rgba(21, 17, 13, 0.85)"
            );
        }
    });

    $(document).ready(function () {

        $('#contactForm').submit(function(e) {
            e.preventDefault();
            showSweetAlert("Please wait", "An email message will be sent to the Administrator", "info"); //FORMAT: TITLE, TEXT, ICON
            var formData = new FormData($(this)[0]);
            formData.append('action', 'contact_form');
            $.ajax({
              type: 'POST',
              url: 'php/processes.php',
              data: formData,
              contentType: false,
              processData: false,
              success: function(response) {
                  if (response.success) {
                    showSweetAlert("Sent successfully", response.message, "success", "index.php?#contact"); //FORMAT: TITLE, TEXT, ICON, URL
                  } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                  }
              }, error: function(xhr, status, error) {
                  console.error(xhr.responseText);
              }
            });
        });


        $('#forgotPasswordForm').submit(function (e) {
            e.preventDefault();

            var email = $('#email').val();
            var type = $('#type').val();

            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: {
                    action: 'checkEmail',
                    email: email,
                    type: type
                }, success: function (response) {
                    console.log(response); // Log the response object to check its structure
                    if (response.exists) {
                        var id = response.id;
                        window.location.href = 'send-code.php?email=' + encodeURIComponent(email) + '&id=' + id + '&type=' + encodeURIComponent(response.user_type);
                    } else {
                        showSweetAlert("Not Found", "Email does not exist", "error", response.redirect); //FORMAT: TITLE, TEXT, ICON, URL
                    }
                }, error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#sendCodeForm').submit(function (e) {
            e.preventDefault();
            showSweetAlert("Please wait", "An email message will be sent to your email shortly.", "info"); //FORMAT: TITLE, TEXT, ICON
            var formData = new FormData($(this)[0]);
            formData.append('action', 'sendCode');
            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                if (response.success) {
                    showSweetAlert("Successfully sent", response.message, "success", response.redirect); //FORMAT: TITLE, TEXT, ICON, URL
                } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                }
                }, error: function (xhr, status, error) {
                console.error(xhr.responseText);
                }
            });
            });
           
        $('#verifyCodeForm').submit(function (e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            formData.append('action', 'verifyCode');
            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function (response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                }
                }, error: function (xhr, status, error) {
                console.error(xhr.responseText);
                }
            });
            });

        $('#changePasswordForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            formData.append('action', 'changePassword');
            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                    showSweetAlert("Success", response.message, "success", response.redirect); //FORMAT: TITLE, TEXT, ICON, URL
                    } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                    }
                }, error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#verifyAccountForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            formData.append('action', 'verifyAccountFromEmail');
            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                    showSweetAlert("Success", response.message, "success", response.redirect); //FORMAT: TITLE, TEXT, ICON, URL
                    } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                    }
                }, error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

        $('#RegistrationForm').submit(function(e) {
            e.preventDefault();
            showSweetAlert("Please wait", "A confirmation will be sent to your email to verify your account.", "info"); //FORMAT: TITLE, TEXT, ICON
            var formData = new FormData($(this)[0]);
            formData.append('action', 'Registration');
            $.ajax({
                type: 'POST',
                url: 'php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                    showSweetAlert("Success", response.message, "success", "register.php"); //FORMAT: TITLE, TEXT, ICON, URL
                    } else {
                    showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                    }
                }, error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });

    });
    
</script>

</body>

</html>