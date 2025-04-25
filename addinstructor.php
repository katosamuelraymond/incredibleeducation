<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'database.php';
include "dashboard includes/header.php";
include "includes/navbar.php";

// Handle form submission to add instructor directly to users table
if (isset($_POST['add_instructor'])) {
    // Get form data
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $course_id = $_POST['course_id'];

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Default image
    $user_image = 'default_avatar.png';

    // Handle uploaded image if provided
    if (!empty($_FILES['user_image']['name'])) {
        $image_name = $_FILES['user_image']['name'];
        $image_tmp = $_FILES['user_image']['tmp_name'];
        $upload_folder = "uploads/users/img/"; // Fixed folder path

        // Create folder if it doesn't exist
        if (!is_dir($upload_folder)) {
            mkdir($upload_folder, 0777, true);
        }

        // Save uploaded image
        if (move_uploaded_file($image_tmp, $upload_folder . $image_name)) {
            $user_image = $image_name;
        }
    }

    // Check if email exists
    $check_email_sql = "SELECT * FROM users WHERE email = '$email'";
    $email_result = mysqli_query($conn, $check_email_sql);

    if (mysqli_num_rows($email_result) > 0) {
        $error_message = "This email is already associated with another user.";
    } else {
        // Insert new instructor with hashed password
        $insert_sql = "INSERT INTO users 
            (firstname, lastname, email, username, password, role, is_instructor, course_id, user_image)
            VALUES 
            ('$firstname', '$lastname', '$email', '$username', '$hashed_password', 'instructor', 1, '$course_id', '$user_image')";

        if (mysqli_query($conn, $insert_sql)) {
            $success_message = "Instructor added successfully!";
        } else {
            $error_message = "Error adding instructor: " . mysqli_error($conn);
        }
    }
}

// Fetch courses
$sql_courses = "SELECT * FROM courses";
$courses_result = mysqli_query($conn, $sql_courses);
?>

<div class="container my-4">
    <h2>Add Instructor to Users Table</h2>

    <?php
    if (isset($success_message)) {
        echo "<div class='alert alert-success'>$success_message</div>";
    } elseif (isset($error_message)) {
        echo "<div class='alert alert-danger'>$error_message</div>";
    }
    ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="firstname" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>

                <div class="mb-3">
                    <label for="lastname" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="course_id" class="form-label">Course</label>
                    <select class="form-select" id="course_id" name="course_id" required>
                        <option value="">Select Course</option>
                        <?php while ($course = mysqli_fetch_assoc($courses_result)) { ?>
                            <option value="<?php echo $course['course_id']; ?>">
                                <?php echo $course['course_name']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="user_image" class="form-label">User Image (optional)</label>
                    <input type="file" class="form-control" id="user_image" name="user_image" accept="image/*">
                </div>

                <button type="submit" name="add_instructor" class="btn btn-primary">Add Instructor</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
