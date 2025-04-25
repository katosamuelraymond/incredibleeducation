<?php
session_start();
include 'database.php';

$user_id = $_SESSION['user_id'];

$profile_sql = "SELECT * FROM users WHERE user_id = $user_id";
$profile_data = mysqli_query($conn, $profile_sql);

if ($profile_data && mysqli_num_rows($profile_data) > 0) {
    $profile = mysqli_fetch_assoc($profile_data); // Get user details
} else {
    echo "User not found!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $firstname = mysqli_real_escape_string($conn, $_POST['firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $about = mysqli_real_escape_string($conn, $_POST['about']);
    $twitter = mysqli_real_escape_string($conn, $_POST['twitter']);
    $facebook = mysqli_real_escape_string($conn, $_POST['facebook']);
    $instagram = mysqli_real_escape_string($conn, $_POST['instagram']);
    $linkedin = mysqli_real_escape_string($conn, $_POST['linkedin']);
    
    // Profile Image Upload Logic
    $new_image = $profile['user_image'];
    if ($_FILES['profile_image']['name']) {
        $image_name = time() . "_" . $_FILES['profile_image']['name'];
        $target_dir = "uploads/users/img/";
        $target_file = $target_dir . basename($image_name);
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
            $new_image = $image_name;
        }
    }

    // Update the profile in the database
    $update_sql = "UPDATE users SET 
        firstname = '$firstname', 
        lastname = '$lastname', 
        email = '$email', 
        country = '$country', 
        phone = '$phone', 
        about = '$about', 
        twitter = '$twitter', 
        facebook = '$facebook', 
        instagram = '$instagram', 
        linkedin = '$linkedin', 
        user_image = '$new_image' 
        WHERE user_id = $user_id";

    if (mysqli_query($conn, $update_sql)) {
        echo "<script>alert('Profile updated successfully!'); window.location = 'profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = mysqli_real_escape_string($conn, $_POST['current_password']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Verify current password
    $current_password_hash = $profile['password'];
    if (password_verify($current_password, $current_password_hash)) {
        if ($new_password === $confirm_password) {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            // Update password in the database
            $update_password_sql = "UPDATE users SET password = '$new_password_hash' WHERE user_id = $user_id";
            if (mysqli_query($conn, $update_password_sql)) {
                echo "<script>alert('Password changed successfully!'); window.location = 'profile.php';</script>";
            } else {
                echo "<script>alert('Error updating password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('New passwords do not match.');</script>";
        }
    } else {
        echo "<script>alert('Incorrect current password.');</script>";
    }
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>

<!-- Sidebar -->

<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">


  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

  <div class="row mx-1">
  <div class="col-12 col-md-4 mb-2">
    <div class="card">
      <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
        <img src="uploads/users/img/<?= $profile['user_image']; ?>" alt="Profile" class="rounded-circle" style="width:90px; height:90px">
        <h2><?= ucfirst($profile['firstname']) . " " . ucfirst($profile['lastname']); ?></h2>
        <h3><?= ucfirst($profile['role']); ?></h3>
        <div class="social-links mt-2">
          <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-md-8">
    <div class="card">
      <div class="card-body pt-3">
        <ul class="nav nav-tabs nav-tabs-bordered">
          <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
          </li>
          <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
          </li>
        </ul>

        <div class="tab-content pt-2">

          <!-- Overview Tab -->
          <div class="tab-pane fade show active profile-overview" id="profile-overview">
            <h5 class="card-title">About</h5>
            <p class="small fst-italic">
              <?=$profile ['about'] ?>
            </p>

            <h5 class="card-title">Profile Details</h5>

            <div class="row">
            <div class="col-6 col-md-3 col-lg-3 label">Full Name</div>
            <div class="col-6 col-md-9 col-lg-9 fs-4 fs-md-3 fs-sm-2"><?= $profile['firstname'] . " " . $profile['lastname']; ?></div>
            </div>


            <div class="row">
              <div class="col-6 col-md-3 col-lg-3 label">Role</div>
              <div class="col-6 col-md-9 col-lg-9"><?= $profile['role']; ?></div>
            </div>

            <div class="row">
              <div class="col-6 col-md-3 col-lg-3 label">Country</div>
              <div class="col-6 col-md-9 col-lg-9"><?= $profile['country']; ?></div>
            </div>

            <div class="row">
              <div class="col-6 col-md-3 col-lg-3 label">Phone</div>
              <div class="col-6 col-md-9 col-lg-9"><?= $profile['phone']; ?></div>
            </div>

            <div class="row">
              <div class="col-6 col-md-3 col-lg-3 label">Email</div>
              <div class="col-6 col-md-9 col-lg-9"><?= $profile['email']; ?></div>
            </div>
          </div>

          <!-- Edit Profile Tab -->
          <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
            <form action="profile.php" method="POST" enctype="multipart/form-data">
              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                <div class="col-md-8 col-lg-9">
                  <img src="uploads/users/img/<?= $profile['user_image']; ?>" alt="Profile" class="rounded-circle" style="width:90px; height:90px">
                  <div class="pt-2">
                    <input type="file" name="profile_image" class="form-control">
                  </div>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">First Name</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="firstname" class="form-control" value="<?= $profile['firstname']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Last Name</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="lastname" class="form-control" value="<?= $profile['lastname']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">About</label>
                <div class="col-md-8 col-lg-9">
                  <textarea name="about" class="form-control" style="height: 100px"><?= $profile['about']; ?></textarea>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Country</label>
                  <div class="col-md-8 col-lg-9">
                      <select name="country" class="form-control">
                          <?php
                         
                          $countries_sql = "SELECT * FROM countries ORDER BY name ASC";
                          $countries_result = mysqli_query($conn, $countries_sql);
                          
                          
                          if (mysqli_num_rows($countries_result) > 0) {
                              while ($country = mysqli_fetch_assoc($countries_result)) {
                                  $selected = ($country['name'] == $profile['country']) ? "selected" : "";
                                  echo "<option value='" . $country['name'] . "' $selected>" . $country['name'] . "</option>";
                              }
                          } else {
                              echo "<option>No countries available</option>";
                          }
                          ?>
                      </select>
                  </div>
              </div>


              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Phone</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="phone" class="form-control" value="<?= $profile['phone']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Email</label>
                <div class="col-md-8 col-lg-9">
                  <input type="email" name="email" class="form-control" value="<?= $profile['email']; ?>">
                </div>
              </div>

              <!-- Optional social links -->
              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Twitter</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="twitter" class="form-control" value="<?= $profile['twitter']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Facebook</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="facebook" class="form-control" value="<?= $profile['facebook']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Instagram</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="instagram" class="form-control" value="<?= $profile['instagram']; ?>">
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">LinkedIn</label>
                <div class="col-md-8 col-lg-9">
                  <input type="text" name="linkedin" class="form-control" value="<?= $profile['linkedin']; ?>">
                </div>
              </div>

              <div class="text-center">
                <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>

          <!-- Change Password Tab -->
          <div class="tab-pane fade profile-change-password pt-3" id="profile-change-password">
            <form action="profile.php" method="POST">
              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                <div class="col-md-8 col-lg-9">
                  <input type="password" name="current_password" class="form-control" required>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">New Password</label>
                <div class="col-md-8 col-lg-9">
                  <input type="password" name="new_password" class="form-control" required>
                </div>
              </div>

              <div class="row mb-3">
                <label class="col-md-4 col-lg-3 col-form-label">Confirm New Password</label>
                <div class="col-md-8 col-lg-9">
                  <input type="password" name="confirm_password" class="form-control" required>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    </div>
  </div>
  </div>
  <?php include 'includes/footer.php'; ?>
</main>




