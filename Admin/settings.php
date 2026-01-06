<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | System settings</title>

    <!-- page content -->
    <div class="right_col" role="main">
      <div class="">
        <div class="page-title">
          <div class="title_left">
            <h3>System settings</h3>
          </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="x_panel">
          <div class="x_title">
            <h2>Manage System Settings</h2>
            <div class="clearfix"></div>
          </div>
          <div class="x_content">

            <!-- Image Modal -->
            <div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-body p-0">
                    <img src="" id="modalImage" class="w-100" style="max-height: 90vh; object-fit: contain;">
                  </div>
                </div>
              </div>
            </div>

            <?php 

                $fetch_settings = $db->getActiveSystemSettings();
                if ($fetch_settings->num_rows == 0) {
                    require_once '../includes/404.php';
                } else {
                    $row2 = $fetch_settings->fetch_assoc();
                    ?>
                    <div class="content">
                        <form id="UpdateSystemSettings" method="POST" enctype="multipart/form-data">
                        <div class="card-box bg-white table-responsive p-3">
                          <input type="hidden" class="form-control" name="Id" id="Id" value="<?= $row2['Id'] ?>" required>
                          <div class="row">
                            <!-- System Name -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="system_name">System Name</label>
                                    <input type="text" class="form-control" id="system_name" name="system_name" placeholder="Enter system name" value="<?= $row2['system_name'] ?>" required>
                                </div>
                            </div>
                            <!-- Address -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address">Address</label>
                                    <input type="text" class="form-control" id="address" name="address" placeholder="Enter address" value="<?= $row2['address'] ?>" required>
                                </div>
                            </div>
                            <!-- About Us -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="about_us">About Us</label>
                                    <textarea class="form-control" id="about_us" name="about_us" rows="1" placeholder="Enter information about us" required><?= $row2['about_us'] ?></textarea>
                                </div>
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="<?= $row2['email'] ?>" required>
                                </div>
                            </div>
                            <!-- Contact -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact">Contact</label>
                                    <input type="text" class="form-control" id="contact" name="contact" placeholder="Enter contact number" value="<?= $row2['contact'] ?>" required>
                                </div>
                            </div>
                            <!-- Logo -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="logo">Logo</label>
                                    <input type="file" class="form-control" id="logo" name="logo">

                                    <?php if (!empty($row2['logo'])): ?>
                                      <div class="mt-2">
                                        <label>Current Logo:</label><br>
                                        <img src="../assets/img/logo//<?= $row2['logo'] ?>" alt="Logo" class="d-block m-auto" style="height: 300px; border: 1px solid #ccc; padding: 3px;">
                                      </div>
                                    <?php endif; ?>

                                </div>
                            </div>

                            <!-- Gallery -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gallery">Gallery</label>
                                    <input type="file" class="form-control" id="gallery" name="gallery[]" multiple>

                                    <!-- Gallery Preview -->
                                    <?php 
                                    $galleryImages = !empty($row2['gallery']) ? explode(',', $row2['gallery']) : [];
                                    if (!empty($galleryImages)): ?>
                                    <label class="mt-2">Current Gallery:</label><br>
                                    <div id="galleryCarousel" class="carousel slide" data-ride="carousel">

                                      <div class="carousel-inner">
                                        <?php foreach ($galleryImages as $index => $image): ?>
                                          <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                                            <img src="../assets/img/gallery/<?= $image ?>" class="d-block w-100" style="height: 300px; object-fit: contain; cursor: pointer;" onclick="viewImage('../assets/img/gallery/<?= $image ?>')" alt="Gallery Image <?= $index + 1 ?>">
                                          </div>
                                        <?php endforeach; ?>
                                      </div>
                                      <a class="carousel-control-prev bg-light" href="#galleryCarousel" role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon"></span>
                                      </a>
                                      <a class="carousel-control-next bg-light" href="#galleryCarousel" role="button" data-slide="next">
                                        <span class="carousel-control-next-icon"></span>
                                      </a>
                                    </div>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </div>
                        <hr>    
                        <div class="row">
                            <div class="col-md-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary btn-sm" id="submit_button">
                                    <i class="fa fa-save"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>
                    </form>

                </div>
                    <?php
                }
            ?>
          </div>
        </div>
      </div>
    </div>
    <!-- /page content -->

<?php require_once 'footer.php'; ?>
<script>
    function viewImage(src) {
      $('#modalImage').attr('src', src);
      $('#imageModal').modal('show');
    }

    $(document).ready(function() {

        $('#UpdateSystemSettings').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($(this)[0]);
            formData.append('action', 'UpdateSystemSettings');

            $.ajax({
                type: 'POST',
                url: '../php/processes.php',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.success) {
                      showSweetAlert("Updated successfully", response.message, "success", "settings.php"); //FORMAT: TITLE, TEXT, ICON, URL
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