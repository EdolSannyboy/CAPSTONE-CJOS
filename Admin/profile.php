<?php require_once 'sidebar.php'; ?>
<title><?= $system_info['system_name'] ?> | Profile settings</title>
<style>
  #imageConfirmButtons button {
    margin: 0 5px;
  }

  select.readonly-select {
    pointer-events: none;
    background-color: #e9ecef;
  }

  @media (min-width: 768px) {
    .border-md-right {
      border-right: 1px solid #dee2e6;
    }
  }

  @media (max-width: 576px) {
    #myTab {
      margin-top: 1rem;
      margin-bottom: 1.5rem;
    }

    #myTab li {
      margin-bottom: 0.5rem;
    }
  }
</style>

  <!-- page content -->
  <div class="right_col" role="main">
    <div class="">
      <div class="page-title">
        <div class="title_left">
          <h3>Profile settings</h3>
        </div>
      </div>
      
      <div class="clearfix"></div>

      <div class="x_panel">
        <div class="x_title">
          <h2>Manage Account Settings</h2>
          <div class="clearfix"></div>
        </div>
        <div class="x_content">

          <div class="col-md-3 col-sm-3 profile_left">
            <div class="profile_img text-center position-relative">
              <form id="updateProfilePicture" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="type" value="Administrator">
                <input type="hidden" name="user_ID" value="<?= $row['user_id'] ?>">

                <div id="crop-avatar" class="position-relative d-inline-block">
                  <img id="imagePreview" class="img-responsive avatar-view img-fluid d-block m-auto rounded-circle border"  src="../assets/img/admin/<?= !empty($row['image']) ? $row['image'] : 'avatar.png' ?>" alt="Avatar"  style="width: 150px; height: 150px; object-fit: cover;" >
                  <label for="image" class="position-absolute" style="bottom: 25px; right: -20px; background: gray; color: white; padding: 6px; border-radius: 50%; cursor: pointer;"><i class="fa fa-edit"></i></label>
                  <input type="file" name="image" id="image" accept="image/jpeg, image/png, image/gif" class="d-none" autocomplete="off">
                  <div id="imageConfirmButtons" class="mt-2 text-center" style="display: none;">
                    <button type="button" id="confirmUpload" class="btn btn-sm btn-success">Confirm</button>
                    <button type="button" id="cancelUpload" class="btn btn-sm btn-secondary">Cancel</button>
                  </div>
                </div>
              </form>
            </div>

            <h3 class="text-center"><?= ucwords($row['firstname'].' '.$row['lastname']) ?></h3>
            <p class="text-center"><?= $userTypeName ?></p>

            <hr>
            <h4>Contact details</h4>
            <ul class="list-unstyled user_data">
              <li><i class="fa fa-phone user-profile-icon"></i> +63 <?= $row['contact'] ?></li>
              <li><i class="fa fa-envelope user-profile-icon"></i> <?= $row['email'] ?></li>
            </ul>
          </div>

          <div class="col-md-9 col-sm-9 ">

            <div class="" role="tabpanel" data-example-id="togglable-tabs">
              <ul id="myTab" class="nav nav-tabs bar_tabs mb-5 py-0" role="tablist">
                <li role="presentation" class="active"><a href="#tab_content1" id="home-tab" role="tab" data-toggle="tab" aria-expanded="true">Administrator information</a></li>
                <li role="presentation" class=""><a href="#tab_content2" role="tab" id="account-security-tab" data-toggle="tab" aria-expanded="false">Account information</a></li>
              </ul>
              <div id="myTabContent" class="tab-content">

                <div role="tabpanel" class="tab-pane active" id="tab_content1" aria-labelledby="home-tab">
                  <form id="UpdateInformation" method="POST" autocomplete="off">
                    <input type="hidden" name="type" value="Administrator">
                    <input type="hidden" name="user_ID" id="admin_ID" value="<?= $row['user_id'] ?>">

                    <div class="row mb-3">
                      <div class="col-md-6 col-sm-6 col-12">
                        <!-- SECTION: Basic Information -->
                        <h6 class=" text-primary">Basic Information</h6>

                        <?php
                          $genderOptions = ['Male', 'Female', 'Other'];
                        ?>

                        <div class="form-group">
                          <label for="firstname">First Name</label>
                          <input type="text" name="firstname" id="firstname" class="form-control" value="<?= $row['firstname'] ?>" oninput="lettersOnly(this)" required>
                        </div>
                        <div class="form-group">
                          <label for="middlename">Middle Name</label>
                          <input type="text" name="middlename" id="middlename" class="form-control" value="<?= $row['middlename'] ?>">
                        </div>
                        <div class="form-group">
                          <label for="lastname">Last Name</label>
                          <input type="text" name="lastname" id="lastname" class="form-control" value="<?= $row['lastname'] ?>" required>
                        </div>
                        <div class="form-group">
                          <label for="suffix">Suffix</label>
                          <input type="text" name="suffix" id="suffix" class="form-control" value="<?= $row['suffix'] ?>">
                        </div>
                        <div class="form-group">
                          <label for="gender">Gender</label>
                          <select class="form-control" name="gender" id="gender" required>
                            <option value="" selected disabled>Select gender</option>
                            <?php foreach ($genderOptions as $g): ?>
                              <option value="<?= $g ?>" <?= $row['gender'] === $g ? 'selected' : '' ?>><?= $g ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="birthdate">Birthdate</label>
                          <input type="date" name="birthdate" id="birthdate" class="form-control" value="<?= $row['birthdate'] ?>" max="<?= date('Y-m-d') ?>" oninput="validateBirthdate(this)" required>
                          <span class="birthdate-error-message text-danger"></span>
                        </div>
                        <div class="form-group">
                          <label for="nationality">Nationality</label>
                          <input type="text" name="nationality" id="nationality" class="form-control" value="<?= $row['nationality'] ?>" required>
                        </div>
                      </div>

                      <div class="col-md-6 col-sm-6 col-12">
                        <!-- SECTION: Contact Information -->
                        <h6 class="text-primary">Contact Information</h6>
                        <div class="form-group">
                          <label for="contact">Mobile Number</label>
                          <div class="input-group">
                            <span class="input-group-text">+63</span>
                            <input type="tel" name="contact" id="contact" class="form-control" maxlength="10" value="<?= $row['contact'] ?>" pattern="[7-9]{1}[0-9]{9}" required>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="email">Email Address</label>
                          <input type="email" name="email" id="email" class="form-control" value="<?= $row['email'] ?>" autocomplete="off" oninput="email_validation(this)" required>
                          <span class="email-error-message text-danger"></span>
                        </div>
                      </div>
                    </div>

                    <div class="form-group row mt-3">
                      <div class="col-sm-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success btn-sm"><i class="fa fa-save"></i> Submit</button>
                      </div>
                    </div>
                  </form>
                </div>

                <div role="tabpanel" class="tab-pane fade" id="tab_content2" aria-labelledby="account-security-tab">
                  <div class="row d-flex">
                    <div class="col-md-8 col-sm-8 col-12 pl-md-4">
                      <form id="updatePassword" method="POST" autocomplete="off">
                        <input type="hidden" name="type" value="Administrator">
                        <input type="hidden" class="form-control" value="<?= $row['user_id']; ?>" name="user_ID" id="updatePassword_ID">

                        <div class="form-group">
                          <h4 class="font-weight-bold text-primary">Change Password</h4>
                          <p class="text-muted">Ensure your account is secure by updating your password below.</p>
                        </div>
                        <div class="form-group">
                          <label for="OldPassword">Old password</label>
                          <div class="input-group">
                            <input type="password" class="form-control" placeholder="Old password" name="OldPassword" id="OldPassword" minlength="8" required>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="password">New password</label>
                          <div class="input-group">
                            <input type="password" class="form-control" placeholder="Password" name="password" id="password" onkeyup="validate_confirm_password(); passwordStrengthCheck()" minlength="8" required>
                          </div>
                          <span id="password-message" class="text-bold text-danger"></span>
                        </div>

                        <div class="form-group">
                        <label for="cpassword">Confirm password</label>
                          <div class="input-group">
                            <input type="password" class="form-control" placeholder="Confirm password" name="cpassword" id="cpassword" onkeyup="validate_confirm_password()" minlength="8" required>
                          </div>
                          <span id="confirm_pass_alert" class="text-bold text-danger"></span>
                        </div>
                        <div class="form-group form-check">
                          <input type="checkbox" class="form-check-input" id="showAllPasswords" onchange="toggleAllPasswords()">
                          <label class="form-check-label" for="showAllPasswords">Show all passwords</label>
                        </div>

                        <div class="form-group d-flex justify-content-end">
                          <button type="submit" class="btn btn-success btn-sm mt-2 mr-0" id="submit_button"><i class="fa fa-save"></i> Submit</button>
                        </div>
                      </form>
                    </div>

                  </div>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- /page content -->

<?php require_once 'footer.php'; ?>
<script>

  $(document).ready(function () {

    // UPDATE PROFILE PICTURE
    let originalImageSrc = $('#imagePreview').attr('src');
    let selectedFile = null;

    $('#image').change(function (e) {
        const file = e.target.files[0];

        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                showSweetAlert("Error", "Only JPG, PNG, or GIF files are allowed.", "error");
                return;
            }

            if (file.size > 500000) {
                showSweetAlert("Error", "File size must be less than 500KB.", "error");
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                $('#imagePreview').attr('src', e.target.result);
                $('#imageConfirmButtons').fadeIn();
            };
            reader.readAsDataURL(file);
            selectedFile = file;
        }
    });

    $('#cancelUpload').click(function () {
      $('#imagePreview').attr('src', originalImageSrc);
      $('#image').val(''); 
      selectedFile = null;
      $('#imageConfirmButtons').fadeOut();
    });

    $('#confirmUpload').click(function () {
        if (!selectedFile) return;

        var formData = new FormData($('#updateProfilePicture')[0]);
        formData.append('action', 'updateProfilePicture');
        formData.append('image', selectedFile);

        $.ajax({
            type: 'POST',
            url: '../php/processes.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
              console.log("RAW response:", response);
              try {
                var res = typeof response === 'string' ? JSON.parse(response) : response;
                if (res.success) {
                  showSweetAlert("Success!", res.message, "success", "profile.php");
                } else {
                  showSweetAlert("Error", res.message, "error");
                }
              } catch (e) {
                console.error("Invalid JSON response:", response);
                showSweetAlert("Error", "Unexpected server response:\n" + response, "error");
              }
            },
            error: function (xhr, status, error) {
                console.error(xhr.responseText);
            }
        });

        $('#imageConfirmButtons').fadeOut();
        selectedFile = null;
    });
    // END UPDATE PROFILE PICTURE

    // UPDATE INFORMATION   
    $('#UpdateInformation').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});

        formData.action = 'UpdateInformation';

        $.ajax({
            type: 'POST',
            url: '../php/processes.php',
            data: formData,
            success: function(response) {
                if (response.success) {
                  showSweetAlert("Success!", response.message, "success", "profile.php"); //FORMAT: TITLE, TEXT, ICON, URL
                } else {
                  showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
    // END UPDATE INFORMATION

    // UPDATE PASSWORD
    $('#updatePassword').submit(function(e) {
        e.preventDefault();
        var formData = new FormData($(this)[0]);
        formData.append('action', 'updatePassword');
        $.ajax({
            type: 'POST',
            url: '../php/processes.php',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                  showSweetAlert("Success!", response.message, "success", "profile.php"); //FORMAT: TITLE, TEXT, ICON, URL
                } else {
                  showSweetAlert("Error", response.message, "error"); //FORMAT: TITLE, TEXT, ICON, URL
                }
            }, error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
    // END UPDATE PASSWORD

  });

</script>