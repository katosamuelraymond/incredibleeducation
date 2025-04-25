<?php
session_start();
include 'database.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle adding assignments
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = mysqli_real_escape_string($conn, $_POST['course_id']);
    $assignment_title = mysqli_real_escape_string($conn, $_POST['assignment_title']);
    $assignment_description = mysqli_real_escape_string($conn, $_POST['assignment_description']);
    $due_date = mysqli_real_escape_string($conn, $_POST['due_date']);
    $assignment_type = mysqli_real_escape_string($conn, $_POST['assignment_type']);
    $lesson_id = mysqli_real_escape_string($conn, $_POST['lesson_id']);  // Add lesson ID

    $insert_query = "INSERT INTO assignments (course_id, assignment_title, assignment_description, due_date, assignment_type, user_id, lesson_id)
                     VALUES ('$course_id', '$assignment_title', '$assignment_description', '$due_date', '$assignment_type', '$user_id', '$lesson_id')";

    if (mysqli_query($conn, $insert_query)) {
        $assignment_id = mysqli_insert_id($conn);

        // Redirect for MCQ type
        if ($assignment_type == 'objective') {
            header("Location: objectivequestions.php?assignment_id=$assignment_id");
            exit();
        }

        $success_message = "Assignment added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}

// Fetch instructor courses
$course_query = "
    SELECT c.course_id, c.course_name 
    FROM instructor_courses ic 
    JOIN courses c ON ic.course_id = c.course_id 
    WHERE ic.user_id = $user_id
";
$course_result = mysqli_query($conn, $course_query);

// Fetch lessons for course selection
$lesson_query = "
    SELECT l.lesson_id, l.lesson_title, l.course_id 
    FROM lessons l 
    WHERE l.course_id IN (SELECT c.course_id FROM instructor_courses ic JOIN courses c ON ic.course_id = c.course_id WHERE ic.user_id = $user_id)
";
$lesson_result = mysqli_query($conn, $lesson_query);

// Fetch instructor's assignments
$assignment_query = "
    SELECT a.*, c.course_name, l.lesson_title, 
        (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.assignment_id) AS submissions
    FROM assignments a
    JOIN courses c ON a.course_id = c.course_id
    LEFT JOIN lessons l ON a.lesson_id = l.lesson_id
    WHERE a.user_id = $user_id
    ORDER BY a.due_date ASC
";
$assignment_result = mysqli_query($conn, $assignment_query);
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <div class="container my-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="card-title text-primary">Add New Assignment</h4>

                <!-- Feedback Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST">
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Select Course</label>
                        <select class="form-control" name="course_id" id="course_id" required>
                            <option value="">-- Choose a Course --</option>
                            <?php while ($course = mysqli_fetch_assoc($course_result)): ?>
                                <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="lesson_id" class="form-label">Select Lesson</label>
                        <select class="form-control" name="lesson_id" id="lesson_id" required>
                            <option value="">-- Choose a Lesson --</option>
                            <?php while ($lesson = mysqli_fetch_assoc($lesson_result)): ?>
                                <option value="<?= $lesson['lesson_id'] ?>" data-course="<?= $lesson['course_id'] ?>"><?= htmlspecialchars($lesson['lesson_title']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="assignment_title" class="form-label">Assignment Title</label>
                        <input type="text" class="form-control" name="assignment_title" id="assignment_title" required>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_description" class="form-label">Assignment Description</label>
                        <textarea class="form-control" name="assignment_description" id="assignment_description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="assignment_type" class="form-label">Assignment Type</label>
                        <select class="form-control" name="assignment_type" id="assignment_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="objective">Objective (MCQs)</option>
                            <option value="paper">Online Paper</option>
                            <option value="exercise">Exercise (File Submission)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" class="form-control" name="due_date" id="due_date" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Assignment</button>
                </form>
            </div>
        </div>

        <!-- Assignments List -->
        <div class="card mt-4">
            <div class="card-body">
                <h4>Your Assignments</h4>

                <?php if (mysqli_num_rows($assignment_result) > 0): ?>
                    <div class="list-group">
                        <?php while ($assignment = mysqli_fetch_assoc($assignment_result)): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row gap-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <?= htmlspecialchars($assignment['assignment_title']) ?>
                                        <small class="text-muted">(<?= htmlspecialchars($assignment['assignment_type']) ?>)</small>
                                    </h6>
                                    <p class="mb-1"><?= htmlspecialchars($assignment['assignment_description']) ?></p>
                                    <p class="mb-0">
                                        <strong>Course:</strong> <?= htmlspecialchars($assignment['course_name']) ?><br>
                                        <strong>Lesson:</strong> <?= htmlspecialchars($assignment['lesson_title']) ?><br>
                                        <strong>Due:</strong> <?= date('M d, Y', strtotime($assignment['due_date'])) ?><br>
                                        <strong>Submissions:</strong> <?= $assignment['submissions'] ?> student(s)
                                    </p>
                                </div>
                                <div class="d-flex flex-column align-items-end justify-content-center">
                                    <a href="edit-assignment.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-warning mb-1">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </a>
                                    <a href="delete-assignment.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">
                                        <i class="bi bi-trash"></i> Delete
                                    </a>
                                    <a href="view-submissions.php?assignment_id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-info mt-1">
                                        <i class="bi bi-eye"></i> View Submissions
                                    </a>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No assignments created yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</main>
