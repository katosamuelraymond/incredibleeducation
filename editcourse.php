<?php
session_start();
include 'database.php';

// Get course ID from URL
if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Fetch course details
    $query = "SELECT * FROM courses WHERE course_id = $course_id";
    $result = mysqli_query($conn, $query);
    $course = mysqli_fetch_assoc($result);

    if (!$course) {
        header("Location: managecourses.php");
        exit;
    }
}

// Handle course update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $_POST['course_name'];
    $course_description = $_POST['course_description'];
    $category_id = $_POST['course_category']; // From select dropdown
    $price = $_POST['price'];
    $status = $_POST['status'];
    $instructor_id = $_POST['instructor_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    if ($_FILES['course_image']['name']) {
        $image_name = $_FILES['course_image']['name'];
        $image_tmp = $_FILES['course_image']['tmp_name'];
        $target_path = "uploads/course_images/" . $image_name;
        move_uploaded_file($image_tmp, $target_path);
    } else {
        $image_name = $course['course_image'];
    }

    $update_query = "
        UPDATE courses SET 
            course_name = '$course_name',
            course_description = '$course_description',
            category_id = '$category_id',
            price = '$price',
            status = '$status',
            instructor_id = '$instructor_id',
            course_image = '$image_name',
            course_start_date = '$start_date',
            course_end_date = '$end_date'
        WHERE course_id = $course_id
    ";

    if (mysqli_query($conn, $update_query)) {
        header("Location: managecourses.php");
        exit;
    } else {
        echo "Error updating course: " . mysqli_error($conn);
    }
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Edit Course</h2>
        <a href="managecourses.php" class="btn btn-outline-secondary">Back to Courses</a>
    </div>

    <div class="container">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="card-title text-primary">Edit Course</h4>

                <form action="editcourse.php?id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="course_description" class="form-label">Course Description</label>
                        <textarea class="form-control" id="course_description" name="course_description" rows="3" required><?php echo htmlspecialchars($course['course_description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="course_category" class="form-label">Category</label>
                        <select class="form-control" id="course_category" name="course_category" required>
                            <option value="">Select Category</option>
                            <?php
                            $categories_query = "SELECT * FROM categories";
                            $categories_result = mysqli_query($conn, $categories_query);
                            while ($category = mysqli_fetch_assoc($categories_result)) {
                                $selected = ($category['category_id'] == $course['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' $selected>{$category['category_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($course['price']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active" <?php echo ($course['status'] === 'active') ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo ($course['status'] === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="instructor_id" class="form-label">Instructor</label>
                        <select class="form-control" id="instructor_id" name="instructor_id" required>
                            <option value="">Select Instructor</option>
                            <?php
                            $instructors_query = "SELECT user_id, firstname, lastname FROM users WHERE role = 'instructor'";
                            $instructors_result = mysqli_query($conn, $instructors_query);
                            while ($instructor = mysqli_fetch_assoc($instructors_result)) {
                                $selected = ($instructor['user_id'] == $course['instructor_id']) ? 'selected' : '';
                                echo "<option value='{$instructor['user_id']}' $selected>{$instructor['firstname']} {$instructor['lastname']}</option>";
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
                        <small class="form-text text-muted">Leave empty to retain the current image.</small>
                    </div>
                    <div class="mb-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $course['course_start_date']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $course['course_end_date']; ?>" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Course</button>
                </form>
            </div>
        </div>
    </div>

<?php include "includes/footer.php"; ?>


</main>

