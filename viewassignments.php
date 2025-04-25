<?php
session_start();
include 'database.php';

// Ensure student is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch student courses (assuming you have a student_courses table or similar)
$assignments_query = "
    SELECT a.*, c.course_name, l.lesson_title
    FROM assignments a
    JOIN courses c ON a.course_id = c.course_id
    LEFT JOIN lessons l ON a.lesson_id = l.lesson_id
    WHERE a.course_id IN (
        SELECT course_id FROM student_courses WHERE user_id = $user_id
    )
    ORDER BY a.due_date ASC
";

$assignments_result = mysqli_query($conn, $assignments_query);
?>

<?php include "includes/header.php"; ?>
<?php include "includes/navbar.php"; ?>

<div class="container my-4">
    <h3 class="mb-4">Your Assignments</h3>

    <?php if (mysqli_num_rows($assignments_result) > 0): ?>
        <div class="list-group">
            <?php while ($assignment = mysqli_fetch_assoc($assignments_result)): ?>
                <div class="list-group-item mb-3 shadow-sm rounded">
                    <h5><?= htmlspecialchars($assignment['assignment_title']) ?> 
                        <small class="text-muted">(<?= htmlspecialchars($assignment['assignment_type']) ?>)</small>
                    </h5>
                    <p><?= htmlspecialchars($assignment['assignment_description']) ?></p>
                    <p><strong>Course:</strong> <?= $assignment['course_name'] ?> <br>
                       <strong>Lesson:</strong> <?= $assignment['lesson_title'] ?> <br>
                       <strong>Due:</strong> <?= date('M d, Y', strtotime($assignment['due_date'])) ?>
                    </p>

                    <!-- Link based on assignment type -->
                    <?php
                        $assignment_url = "#";
                        if ($assignment['assignment_type'] == 'objective') {
                            $assignment_url = "take-objective.php?assignment_id=" . $assignment['assignment_id'];
                        } elseif ($assignment['assignment_type'] == 'paper') {
                            $assignment_url = "take-paper.php?assignment_id=" . $assignment['assignment_id'];
                        } elseif ($assignment['assignment_type'] == 'exercise') {
                            $assignment_url = "upload-assignment.php?assignment_id=" . $assignment['assignment_id'];
                        }
                    ?>

                    <a href="<?= $assignment_url ?>" class="btn btn-primary">Start Assignment</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">You have no assignments yet.</p>
    <?php endif; ?>
</div>

<?php include "includes/footer.php"; ?>
