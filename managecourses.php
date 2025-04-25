<?php
session_start();
include 'database.php';

// Handle course deletion
if (isset($_GET['delete_course_id'])) {
    $course_id = mysqli_real_escape_string($conn, $_GET['delete_course_id']);

    // Start transaction for safety
    mysqli_begin_transaction($conn);

    try {
        // Delete from objective_questions -> depends on assignments
        $assignmentQuery = mysqli_query($conn, "SELECT assignment_id FROM assignments WHERE course_id = '$course_id'");
        while ($assignment = mysqli_fetch_assoc($assignmentQuery)) {
            $assignment_id = $assignment['assignment_id'];
            mysqli_query($conn, "DELETE FROM objective_questions WHERE assignment_id = '$assignment_id'");
        }

        // Delete assignments
        mysqli_query($conn, "DELETE FROM assignments WHERE course_id = '$course_id'");

        // Delete lesson materials
        mysqli_query($conn, "DELETE FROM lesson_materials WHERE course_id = '$course_id'");

        // Delete course_sections
        mysqli_query($conn, "DELETE FROM course_sections WHERE course_id = '$course_id'");

        // Delete instructor-course relationships
        mysqli_query($conn, "DELETE FROM instructor_courses WHERE course_id = '$course_id'");

        // Delete from cart
        mysqli_query($conn, "DELETE FROM cart WHERE course_id = '$course_id'");

        

        // Finally, delete the course
        mysqli_query($conn, "DELETE FROM courses WHERE course_id = '$course_id'");

        // Commit transaction
        mysqli_commit($conn);

        $_SESSION['message'] = "✅ Course and all associated data deleted successfully.";
        header("Location: managecourses.php");
        exit;
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        $_SESSION['message'] = "❌ Failed to delete course: " . $e->getMessage();
        header("Location: managecourses.php");
        exit;
    }
}

// Fetch all courses with category names
$query = "
    SELECT c.*, cat.category_name 
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.category_id
    ORDER BY c.course_start_date ASC
";
$resultz = mysqli_query($conn, $query);
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<body>
<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <!-- SESSION ALERT MESSAGE -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-info text-center alert-dismissible fade show" role="alert">
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manage Courses</h2>
        <a href="addcourse.php" class="btn btn-primary">+ Add New Course</a>
    </div>

    <div class="container">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Course Name</th>
                        <th>Instructors</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultz)): ?>
                        <tr>
                            <td><?php echo $row['course_id']; ?></td>
                            <td>
                                <img src="<?php echo $row['course_image']; ?>" width="60" height="40" alt="Course Image">
                            </td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td>
                                <?php
                                $courseId = $row['course_id'];
                                $instructorQuery = "
                                    SELECT u.firstname, u.lastname 
                                    FROM users u 
                                    JOIN instructor_courses ic ON u.user_id = ic.user_id 
                                    WHERE ic.course_id = $courseId
                                ";
                                $instructorResult = mysqli_query($conn, $instructorQuery);
                                $instructors = [];

                                while ($inst = mysqli_fetch_assoc($instructorResult)) {
                                    $instructors[] = $inst['firstname'] . ' ' . $inst['lastname'];
                                }

                                echo !empty($instructors) ? implode(", ", $instructors) : "No instructor assigned";
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo '$' . number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['course_start_date']; ?></td>
                            <td><?php echo $row['course_end_date']; ?></td>
                            <td><?php echo ucfirst($row['status']); ?></td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm d-flex flex-wrap gap-1 justify-content-center">
                                    <a href="editcourse.php?id=<?php echo $row['course_id']; ?>" class="btn btn-outline-warning">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="managecourses.php?delete_course_id=<?php echo $row['course_id']; ?>" 
                                    class="btn btn-outline-danger"
                                    onclick="return confirm('Are you sure you want to delete this course and all related data?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                    <a href="addcoursecontent.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-outline-success">
                                        <i class="bi bi-plus-circle"></i> Add Lesson
                                    </a>
                                    <a href="managesections.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-outline-info">
                                        <i class="bi bi-layout-text-window-reverse"></i> Edit Sections
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if (mysqli_num_rows($resultz) == 0): ?>
                        <tr>
                            <td colspan="10" class="text-center">No courses found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php include "includes/footer.php"; ?>
</main>
