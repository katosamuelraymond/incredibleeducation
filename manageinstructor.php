<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ob_start(); // Start output buffering
include 'database.php';
include "dashboard includes/header.php";
include "includes/navbar.php";

// Update instructor
if (isset($_POST['update_instructor'])) {
    $id = $_POST['user_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $username = $_POST['username'];

    $sql = "UPDATE users SET firstname='$firstname', lastname='$lastname', email='$email', username='$username' WHERE user_id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: manageinstructor.php?success=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating instructor.</div>";
    }
}

// Demote instructor to student (instead of deleting)
if (isset($_POST['delete_instructor'])) {
    $id = $_POST['user_id'];
    $sql = "UPDATE users SET role='student', is_instructor=0 WHERE user_id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: manageinstructor.php?demoted=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error updating instructor role.</div>";
    }
}

// Approve instructor from pending list
if (isset($_POST['approve_instructor'])) {
    $id = $_POST['pending_user_id'];

    $pending_sql = "SELECT * FROM pending_instructors WHERE user_id = '$id'";
    $pending_result = mysqli_query($conn, $pending_sql);

    if ($row = mysqli_fetch_assoc($pending_result)) {
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];
        $email = $row['email'];
        $username = $row['username'];
        $password = $row['password'];
        $course_id = $row['course_id'];

        // Check if email already exists
        $check_sql = "SELECT * FROM users WHERE email = '$email'";
        $check_result = mysqli_query($conn, $check_sql);

        if (mysqli_num_rows($check_result) > 0) {
            $existing_user = mysqli_fetch_assoc($check_result);

            if ($existing_user['role'] == 'student') {
                // Upgrade student to instructor and set is_instructor to 1
                $update_sql = "UPDATE users SET role='instructor', is_instructor=1, course_id='$course_id' WHERE email='$email'";
                if (mysqli_query($conn, $update_sql)) {
                    mysqli_query($conn, "DELETE FROM pending_instructors WHERE user_id='$id'");
                    header("Location: manageinstructor.php");
                    exit();
                } else {
                    echo "<div class='alert alert-danger'>Failed to upgrade existing student.</div>";
                }
            } elseif ($existing_user['role'] == 'instructor' || $existing_user['is_instructor'] == 1) {
                echo "<div class='alert alert-warning'>This email is already registered as an instructor.</div>";
            } else {
                echo "<div class='alert alert-warning'>This email is used with another role.</div>";
            }
        } else {
            // Email does not exist — insert as new instructor and set is_instructor to 1
            $insert_sql = "INSERT INTO users (firstname, lastname, email, username, password, role, is_instructor, course_id)
                           VALUES ('$firstname', '$lastname', '$email', '$username', '$password', 'instructor', 1, '$course_id')";
            if (mysqli_query($conn, $insert_sql)) {
                mysqli_query($conn, "DELETE FROM pending_instructors WHERE user_id='$id'");
                header("Location: manageinstructor.php?approved=1");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error inserting new instructor.</div>";
            }
        }
    } else {
        echo "<div class='alert alert-danger'>Instructor not found in pending list.</div>";
    }
}

// Decline instructor
if (isset($_POST['decline_instructor'])) {
    $id = $_POST['pending_user_id'];
    $sql = "DELETE FROM pending_instructors WHERE user_id='$id'";
    if (mysqli_query($conn, $sql)) {
        header("Location: manageinstructor.php?declined=1");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error declining instructor.</div>";
    }
}
?>

<body>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Manage Instructors</h2>
            <a href="addinstructor.php" class="btn btn-success">Add Instructor</a>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Instructor updated successfully!</div>
        <?php elseif (isset($_GET['demoted'])): ?>
            <div class="alert alert-success">Instructor demoted to student successfully!</div>
        <?php elseif (isset($_GET['approved'])): ?>
            <div class="alert alert-success">Instructor approved successfully!</div>
        <?php elseif (isset($_GET['declined'])): ?>
            <div class='alert alert-success'>Instructor declined successfully!</div>
        <?php endif; ?>

        <!-- Pending Instructors -->
        <h3>Pending Instructor Applications</h3>
        <div class="card mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $pending_sql = "SELECT * FROM pending_instructors";
                            $pending_result = mysqli_query($conn, $pending_sql);
                            $count = 1;

                            while ($row = mysqli_fetch_assoc($pending_result)) {
                                $course_id = $row['course_id'];
                                $course_name = "N/A";

                                $course_query = "SELECT course_name FROM courses WHERE course_id = '$course_id'";
                                $course_result = mysqli_query($conn, $course_query);
                                if ($course_data = mysqli_fetch_assoc($course_result)) {
                                    $course_name = $course_data['course_name'];
                                }

                                echo "<tr>
                                    <form method='POST'>
                                        <td>{$count}</td>
                                        <td>{$row['firstname']}</td>
                                        <td>{$row['lastname']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$course_name}</td>
                                        <td>
                                            <input type='hidden' name='pending_user_id' value='{$row['user_id']}'>
                                            <button type='submit' name='approve_instructor' class='btn btn-success btn-sm mb-1 w-100'>Approve</button>
                                            <button type='submit' name='decline_instructor' class='btn btn-danger btn-sm w-100' onclick=\"return confirm('Are you sure you want to decline this application?')\">Decline</button>
                                        </td>
                                    </form>
                                </tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Approved Instructors -->
        <h3>Instructors</h3>
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Firstname</th>
                                <th>Lastname</th>
                                <th>Email</th>
                                <th>Username</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $instructors_sql = "SELECT * FROM users WHERE role = 'instructor'";
                            $instructors_result = mysqli_query($conn, $instructors_sql);
                            $count = 1;

                            while ($row = mysqli_fetch_assoc($instructors_result)) {
                                echo "<tr>
                                    <form method='POST'>
                                        <td>{$count}</td>
                                        <td><input type='text' name='firstname' value='{$row['firstname']}' class='form-control form-control-sm' required></td>
                                        <td><input type='text' name='lastname' value='{$row['lastname']}' class='form-control form-control-sm' required></td>
                                        <td><input type='email' name='email' value='{$row['email']}' class='form-control form-control-sm' required></td>
                                        <td><input type='text' name='username' value='{$row['username']}' class='form-control form-control-sm' required></td>
                                        <td>
                                            <input type='hidden' name='user_id' value='{$row['user_id']}'>
                                            <button type='submit' name='update_instructor' class='btn btn-warning btn-sm mb-1 w-100'>Save</button>
                                            <button type='submit' name='delete_instructor' class='btn btn-danger btn-sm w-100' onclick=\"return confirm('Are you sure you want to demote this instructor?')\">Demote</button>
                                        </td>
                                    </form>
                                </tr>";
                                $count++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

<?php include "includes/footer.php"; ?>
</main>
