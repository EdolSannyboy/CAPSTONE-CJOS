    <?php require_once 'header.php'; ?>
    <title>Home | <?= $system_info['system_name'] ?></title>

    <!-- Toast container -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 2000;">
      <div id="cartToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" style="background-color: #f9a825; color: #fff;">
        <div class="d-flex">
          <div class="toast-body" id="cartToastMessage">
            Added to cart successfully!
          </div>
          <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- Hero Section -->
    <section id="hero" class="hero section dark-background d-none">

      <div id="hero-carousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

        <div class="carousel-item active">
          <img src="assets/img/hero-carousel/hero-carousel-1.jpg" alt="">
          <div class="carousel-container">
            <h2><span>USTP</span> Canteen Job Order System</h2>
            <p>Making meal orders faster, more accurate, and more convenient for USTP faculty and staff — no more wrong orders, stockouts, or delays.</p>
            <div>
              <a href="#menu" class="btn-get-started">Our Menu</a>
              <a href="#book-a-table" class="btn-get-started">Book a table</a>
            </div>
          </div>
        </div><!-- End Carousel Item -->

        <div class="carousel-item">
          <img src="assets/img/hero-carousel/hero-carousel-2.jpg" alt="">
          <div class="carousel-container">
            <h2>Streamlined Ordering</h2>
            <p>Order your meals ahead of time, track requests, and avoid the hassle of manual paper-based processes.</p>
            <div>
              <a href="#menu" class="btn-get-started">Our Menu</a>
              <a href="#book-a-table" class="btn-get-started">Book a table</a>
            </div>
          </div>
        </div><!-- End Carousel Item -->

        <div class="carousel-item">
          <img src="assets/img/hero-carousel/hero-carousel-3.jpg" alt="">
          <div class="carousel-container">
            <h2>Smart Inventory Management</h2>
            <p>Real-time stock monitoring prevents shortages and overstocking, ensuring the canteen always meets your needs.</p>
            <div>
              <a href="#menu" class="btn-get-started">Our Menu</a>
              <a href="#book-a-table" class="btn-get-started">Book a table</a>
            </div>
          </div>
        </div><!-- End Carousel Item -->

        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>

        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>

        <ol class="carousel-indicators"></ol>

      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section light-background">

      <div class="container" style="margin-top: 100px;">

        <div class="row gy-4">
          <div class="col-lg-6 position-relative align-self-start" data-aos="fade-up" data-aos-delay="100">
            <img src="assets/img/about.jpg" class="img-fluid" alt="Canteen kitchen and food service area">
          </div>
          <div class="col-lg-6 content" data-aos="fade-up" data-aos-delay="200">
            <h3>Freshly Prepared Meals, Snacks, and Drinks — Just for You!</h3>
            <p class="fst-italic">
              Our Canteen Job Order System ensures fast, accurate, and convenient ordering — whether you’re craving a hearty meal, a quick snack, or a refreshing drink.
            </p>
            <ul>
              <li><i class="bi bi-check2-all"></i> <span>Wide selection of freshly cooked meals served daily.</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Easy online ordering with real-time stock updates.</span></li>
              <li><i class="bi bi-check2-all"></i> <span>Convenient tracking of your orders from preparation to pick-up.</span></li>
            </ul>
            <p>
              With our user-friendly system, you can browse products, place orders, and enjoy a hassle-free canteen experience. Whether for students, staff, or visitors — we make sure every serving is fresh, fast, and satisfying.
            </p>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Why Us Section -->
    <section id="why-us" class="why-us section d-none">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Why Us</h2>
        <div><span>Why choose</span> <span class="description-title">Our Restaurant</span></div>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
            <div class="card-item">
              <span>01</span>
              <h4><a href="#" class="stretched-link">Fresh & Locally Sourced</a></h4>
              <p>We use only the freshest ingredients from local farmers and suppliers to bring you flavorful, healthy meals every day.</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
            <div class="card-item">
              <span>02</span>
              <h4><a href="#" class="stretched-link">Exceptional Service</a></h4>
              <p>Our friendly staff is dedicated to making your dining experience warm, welcoming, and unforgettable from start to finish.</p>
            </div>
          </div><!-- Card Item -->

          <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
            <div class="card-item">
              <span>03</span>
              <h4><a href="#" class="stretched-link">A Taste You’ll Remember</a></h4>
              <p>Every dish is carefully crafted with passion and love, ensuring each bite is a burst of taste that keeps you coming back.</p>
            </div>
          </div><!-- Card Item -->

        </div>

      </div>

    </section><!-- /Why Us Section -->

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section  d-none">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Gallery</h2>
        <div><span>Some photos from</span> <span class="description-title">Our Canteen</span></div>
      </div><!-- End Section Title -->

      <div class="container-fluid" data-aos="fade-up" data-aos-delay="100">
        <div class="row g-0">
          <?php
            if (!empty($system_info['gallery'])) {
                $galleryImages = explode(',', $system_info['gallery']);
                foreach ($galleryImages as $index => $image) {
                    $imagePath = "assets/img/gallery/" . trim($image);
                    ?>
                    <div class="col-lg-3 col-md-4">
                      <div class="gallery-item">
                        <a href="<?= $imagePath ?>" class="glightbox" data-gallery="images-gallery">
                          <img src="<?= $imagePath ?>" alt="Gallery Image <?= $index + 1 ?>" class="img-fluid">
                        </a>
                      </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center text-muted'>No gallery images available.</p>";
            }
          ?>
        </div>
      </div>

    </section><!-- /Gallery Section -->

    <!-- Chefs Section -->
    <section id="chefs" class="chefs section  d-none">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Chefs</h2>
        <div><span>Our Professional</span> <span class="description-title">Chefs</span></div>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-5">

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-1.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Walter White</h4>
                <span>Master Chef</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-2.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>Sarah Jhonson</h4>
                <span>Patissier</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
            <div class="member">
              <div class="pic"><img src="assets/img/chefs/chefs-3.jpg" class="img-fluid" alt=""></div>
              <div class="member-info">
                <h4>William Anderson</h4>
                <span>Cook</span>
                <div class="social">
                  <a href=""><i class="bi bi-twitter-x"></i></a>
                  <a href=""><i class="bi bi-facebook"></i></a>
                  <a href=""><i class="bi bi-instagram"></i></a>
                  <a href=""><i class="bi bi-linkedin"></i></a>
                </div>
              </div>
            </div>
          </div><!-- End Team Member -->

        </div>

      </div>

    </section><!-- /Chefs Section -->

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section dark-background  d-none">

      <img src="assets/img/testimonials-bg.jpg" class="testimonials-bg" alt="">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              }
            }
          </script>
          <div class="swiper-wrapper">
            <?php 
              // $ratings = $db->getRecentProductRatings(5); // Fetch top 5
              // foreach ($ratings as $r) {
              //     $stars = str_repeat('<i class="bi bi-star-fill"></i>', $r['rating']);
              //     echo '
              //     <div class="swiper-slide">
              //         <div class="testimonial-item">
              //             <img src="assets/img/office-staff/'.htmlspecialchars($r['image']).'" class="testimonial-img" alt="">
              //             <h3>'.htmlspecialchars($r['user_name']).'</h3>
              //             <h4>'.htmlspecialchars($r['product_name']).'</h4>
              //             <div class="stars">'.$stars.'</div>
              //             <p>
              //                 <i class="bi bi-quote quote-icon-left"></i>
              //                 <span>'.htmlspecialchars($r['review']).'</span>
              //                 <i class="bi bi-quote quote-icon-right"></i>
              //             </p>
              //         </div>
              //     </div>
              //     ';
              // }
            ?>
          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Contact Section -->
    <section id="contact" class="contact section  d-none">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>Contact</h2>
        <div><span>Check Our</span> <span class="description-title">Contact</span></div>
      </div><!-- End Section Title -->

      <div class="mb-5">
        <iframe style="width: 100%; height: 400px;" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3946.1501841342833!2d124.6541418750644!3d8.484774497270305!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x32fff2c3ca5ae8c7%3A0x880805868ab84491!2sUniversity%20of%20Science%20and%20Technology%20of%20Southern%20Philippines%20-%20CDO%20Campus!5e0!3m2!1sen!2sph!4v1755502481490!5m2!1sen!2sph" frameborder="0" allowfullscreen=""></iframe>
      </div><!-- End Google Maps -->

      <div class="container" data-aos="fade">

        <div class="row gy-5 gx-lg-5">

          <div class="col-lg-4">

            <div class="info">
              <h3>Get in touch</h3>
              <p>Order your favorite meals from the canteen without leaving your desk — fast, fresh, and convenient.</p>

              <div class="info-item d-flex">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h4>Location:</h4>
                  <p><?= $system_info['address'] ?></p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h4>Email:</h4>
                  <p><?= $system_info['email'] ?></p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-phone flex-shrink-0"></i>
                <div>
                  <h4>Call:</h4>
                  <p><?= $system_info['contact'] ?></p>
                </div>
              </div><!-- End Info Item -->

            </div>

          </div>

          <div class="col-lg-8">
            <form id="contactForm" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" autocomplete="off" value="<?= isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : '' ?>" required="">
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" autocomplete="off" value="<?= isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : '' ?>" required="">
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" autocomplete="off" required="">
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" placeholder="Message" autocomplete="off" required=""></textarea>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>

    </section><!-- /Contact Section -->
  
    <?php require_once 'footer.php'; ?>
  
    <script>

      $(document).on('click', '.add-to-cart-btn', function() {
        let product_ID = $(this).data('product-id');
        let quantity = $('#product_' + product_ID).val();

        $.ajax({
            url: 'php/ajax.php',
            type: 'POST',
            data: {
                action: 'addToCart',
                product_ID: product_ID,
                quantity: quantity
            },
            dataType: 'json',
            success: function(response) {
                let toastEl = $('#cartToast');
                let toastBody = $('#cartToastMessage');

                if (response.status === 'success') {
                    toastEl.css({
                        'background-color': '#f9a825',
                        'color': '#fff'
                    });
                    toastBody.text('Added to cart successfully!');

                    // Update cart count badge
                    $('#cart-count-badge').text(response.cart_count);
                } else {
                    toastEl.css({
                        'background-color': '#d32f2f',
                        'color': '#fff'
                    });
                    toastBody.text(response.message);
                }

                let toast = new bootstrap.Toast(toastEl[0]);
                toast.show();
            }
        });
      });

      
      document.addEventListener("DOMContentLoaded", function () {
        
        // Increment / Decrement Quantity
        document.querySelectorAll(".quantity-group").forEach(group => {
          const input = group.querySelector(".quantity-input");
          const min = parseInt(input.min);
          const max = parseInt(input.max);

          group.querySelector(".btn-increment").addEventListener("click", () => {
            let value = parseInt(input.value);
            if (value < max) input.value = value + 1;
          });

          group.querySelector(".btn-decrement").addEventListener("click", () => {
            let value = parseInt(input.value);
            if (value > min) input.value = value - 1;
          });
        });
        

        // MENU SECTION JS CODE *********************************************************
        const itemsPerPage = 12;
        let currentPage = 1;

        const productContainer = document.getElementById("productContainer");
        const productItems = Array.from(productContainer.getElementsByClassName("product-item"));
        const pagination = document.getElementById("pagination");
        const searchInput = document.getElementById("searchInput");

        let filteredItems = [...productItems]; // Start with all items

        function displayPage(page) {
            const start = (page - 1) * itemsPerPage;
            const end = start + itemsPerPage;

            // Hide all items first
            productItems.forEach(item => item.style.display = "none");

            // Show only the filtered items for this page
            filteredItems.slice(start, end).forEach(item => item.style.display = "block");
        }

        function setupPagination() {
            pagination.innerHTML = "";
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

            if (totalPages <= 1) return; // No pagination if only 1 page

            const ul = document.createElement("ul");
            ul.className = "pagination pagination-sm mb-0";

            for (let i = 1; i <= totalPages; i++) {
                const li = document.createElement("li");
                li.className = "page-item" + (i === currentPage ? " active" : "");

                const a = document.createElement("a");
                a.className = "page-link";
                a.href = "#";
                a.innerText = i;

                a.style.backgroundColor = i === currentPage ? "#d96b13" : "#f57f17";
                a.style.color = "#fff";
                a.style.border = "none";

                a.addEventListener("click", function (e) {
                    e.preventDefault();
                    currentPage = i;
                    displayPage(currentPage);
                    setupPagination();
                });

                li.appendChild(a);
                ul.appendChild(li);
            }

            pagination.appendChild(ul);
        }

        function filterProducts() {
            const filter = searchInput.value.toLowerCase();

            // Remove old "no product" message if exists
            let noProductMsg = document.getElementById("noProductMessage");
            if (noProductMsg) noProductMsg.remove();

            filteredItems = productItems.filter(item => {
                const name = item.querySelector(".card-title").textContent.toLowerCase();
                return name.includes(filter);
            });

            if (filteredItems.length === 0) {
                noProductMsg = document.createElement("div");
                noProductMsg.id = "noProductMessage";
                noProductMsg.innerHTML = `<div class="no-product-container"><div class="no-product-msg">No product found</div></div>`;
                productContainer.appendChild(noProductMsg);
            }

            currentPage = 1; // Reset to first page after filtering
            displayPage(currentPage);
            setupPagination();
        }

        // Live Search
        searchInput.addEventListener("input", filterProducts);

        // See More Toggle
        document.querySelectorAll(".see-more-link").forEach(link => {
            link.addEventListener("click", function () {
                const cardText = this.closest(".card-text");
                cardText.querySelector(".short-desc").classList.toggle("d-none");
                cardText.querySelector(".full-desc").classList.toggle("d-none");
                this.textContent = this.textContent === "See more" ? "See less" : "See more";
            });
        });

        // Initialize default view
        displayPage(currentPage);
        setupPagination();
        // END MENU SECTION JS CODE *********************************************************
      });
      

    </script>