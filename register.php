<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php

include 'database.php';
include "includes/header.php";
include "includes/navbar.php";

if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username']; // Capture the username
    $password = $_POST['password'];

    // Check if email already exists
    $check_query = "SELECT * FROM users WHERE email = '$email' OR username = '$username'"; // Check for both email and username
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Email or username is already registered.";
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Set default role as student
        $insert_query = "INSERT INTO users (firstname, lastname, email, username, password, role)
                         VALUES ('$firstname', '$lastname', '$email', '$username', '$hashed_password', 'student')";

        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Account created successfully. You can now log in.";
            header("Location: login.php");
        } else {
            $error_message = "Something went wrong. Please try again.";
        }
    }
}
?>

<!-- Registration Form -->
<div class="container my-4">
    <h2>Create an Account</h2>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php elseif (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" autocomplete="off">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" class="form-control" name="firstname" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" class="form-control" name="lastname" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-control" name="email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label> <!-- Added username field -->
                    <input type="text" class="form-control" name="username" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" name="register" href="register.php" class="btn btn-primary">Register</button>

                <p class="mt-3">Already have an account? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
