<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


include 'database.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the form data
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $instructor_id = $_POST['course_instructor'];
    $course_start_date = $_POST['course_start_date'];
    $course_end_date = $_POST['course_end_date'];
    $category_id = $_POST['course_category'];
    $price = $_POST['price'];
    $original_price = $_POST['original_price'];
    $education_level_id = $_POST['course_level'];  

    // Handle the image upload
    $image_path = ''; 
    if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] == 0) {
        $image_name = $_FILES['course_image']['name'];
        $image_tmp = $_FILES['course_image']['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($image_ext, $allowed_extensions)) {
            $new_image_name = uniqid('course_', true) . '.' . $image_ext;
            $image_path = 'uploads/course_images/' . $new_image_name;

            if (!move_uploaded_file($image_tmp, $image_path)) {
                $error_message = "Error: Failed to upload image.";
            }
        } else {
            $error_message = "Error: Only image files (jpg, jpeg, png, gif) are allowed.";
        }
    }

    // Get category name using the category ID
    $category_name = '';
    $category_query = "SELECT category_name FROM categories WHERE category_id = '$category_id'";
    $category_result = mysqli_query($conn, $category_query);
    if ($category_row = mysqli_fetch_assoc($category_result)) {
        $category_name = $category_row['category_name'];
    }

    // Validate form fields
    if (
        empty($course_name) || empty($course_description) || empty($instructor_id) ||
        empty($course_start_date) || empty($course_end_date) ||
        empty($category_id) || empty($category_name) || empty($price) || empty($original_price) || empty($education_level_id)
    ) {
        $error_message = "All fields are required!";
    } else {
        // Start a transaction
        mysqli_begin_transaction($conn);
        try {
            // Insert into courses table
            $query = "INSERT INTO courses 
                (course_name, course_description, instructor_id, course_start_date, course_end_date, category_id, category_name, course_image, price, original_price, education_level_id) 
                VALUES 
                ('$course_name', '$course_description', '$instructor_id', '$course_start_date', '$course_end_date', '$category_id', '$category_name', '$image_path', '$price', '$original_price', '$education_level_id')";

            if (mysqli_query($conn, $query)) {
                // Get the course_id of the newly inserted course
                $course_id = mysqli_insert_id($conn);

                // Insert into instructor_courses table
                $instructor_course_query = "INSERT INTO instructor_courses (user_id, course_id) 
                                            VALUES ('$instructor_id', '$course_id')";

                if (mysqli_query($conn, $instructor_course_query)) {
                    // Commit the transaction
                    mysqli_commit($conn);

                    // Success message
                    $success_message = "Course and Instructor Course added successfully!";
                    header("Location: managesections.php?course_id=$course_id");
                    exit();
                } else {
                    // Rollback the transaction if inserting into instructor_courses fails
                    mysqli_rollBack($conn);
                    $error_message = "Error inserting into instructor_courses: " . mysqli_error($conn);
                }
            } else {
                // Rollback the transaction if inserting into courses fails
                mysqli_rollBack($conn);
                $error_message = "Error: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            // Rollback the transaction if an exception occurs
            mysqli_rollBack($conn);
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Add New Course</h2>
        <span class="btn btn-outline-primary d-md-none" id="openSidebar"><i class="bi bi-list"></i></span>
    </div>

    <div class="container my-2">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="card-title text-primary">Add a New Course</h4>

                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Course Form -->
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_description" class="form-label">Course Description</label>
                        <textarea class="form-control" id="course_description" name="course_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="course_instructor" class="form-label">Instructor</label>
                        <select class="form-control" id="course_instructor" name="course_instructor" required>
                            <option value="">Select Instructor</option>
                            <?php
                            $instructors_query = "SELECT user_id, firstname, lastname FROM users WHERE role = 'instructor'";
                            $instructors_result = mysqli_query($conn, $instructors_query);
                            while ($row = mysqli_fetch_assoc($instructors_result)) {
                                echo "<option value='{$row['user_id']}'>{$row['firstname']} {$row['lastname']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course_start_date" class="form-label">Course Start Date</label>
                        <input type="date" class="form-control" id="course_start_date" name="course_start_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_end_date" class="form-label">Course End Date</label>
                        <input type="date" class="form-control" id="course_end_date" name="course_end_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_category" class="form-label">Category</label>
                        <select class="form-control" id="course_category" name="course_category" required>
                            <option value="">Select Category</option>
                            <?php
                            $categories_query = "SELECT category_id, category_name FROM categories";
                            $categories_result = mysqli_query($conn, $categories_query);
                            while ($row = mysqli_fetch_assoc($categories_result)) {
                                echo "<option value='{$row['category_id']}'>{$row['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course_level" class="form-label">Education Level</label>
                        <select class="form-control" id="course_level" name="course_level" required>
                            <option value="">Select Education Level</option>
                            <?php
                            $levels_query = "SELECT education_level_id, level_name FROM education_levels";
                            $levels_result = mysqli_query($conn, $levels_query);
                            while ($row = mysqli_fetch_assoc($levels_result)) {
                                echo "<option value='{$row['education_level_id']}'>{$row['level_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course_image" class="form-label">Course Image</label>
                        <input type="file" class="form-control" id="course_image" name="course_image">
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Course Price (UGX)</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="original_price" class="form-label">Original Price (UGX)</label>
                        <input type="number" class="form-control" id="original_price" name="original_price" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Course</button>
                </form>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</main>

