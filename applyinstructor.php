<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $course_id = $_POST['course_id'];

    // Default image if none uploaded
    $user_image = 'default_avatar.png';

    // Handle uploaded image if provided
    if (!empty($_FILES['user_image']['name'])) {
        $image_name = $_FILES['user_image']['name'];
        $image_tmp = $_FILES['user_image']['tmp_name'];
        $upload_folder = "uploads/users/img";

        // Create folder if it doesn't exist
        if (!is_dir($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        // Move the uploaded image to the folder
        if (move_uploaded_file($image_tmp, $upload_folder . $image_name)) {
            $user_image = $image_name;
        }
    }

    // Insert into pending_instructors table
    $sql = "INSERT INTO pending_instructors (firstname, lastname, email, username, password, course_id, user_image)
            VALUES ('$firstname', '$lastname', '$email', '$username', '$password', '$course_id', '$user_image')";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?application=success&msg=Your application was submitted successfully. Please wait for admin approval.");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container mt-4">
    <h2>Apply to Become an Instructor</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>First Name</label>
                <input type="text" name="firstname" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Last Name</label>
                <input type="text" name="lastname" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Preferred Course</label>
                <select name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    <?php
                    $res = mysqli_query($conn, "SELECT course_id, course_name FROM courses");
                    while ($row = mysqli_fetch_assoc($res)) {
                        echo "<option value='{$row['course_id']}'>{$row['course_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label>Upload Your Image (optional)</label>
                <input type="file" name="user_image" class="form-control" accept="image/*">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Submit Application</button>
    </form>
</div>

<?php include "includes/footer.php"; ?>
