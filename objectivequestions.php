<?php
session_start();
include 'database.php';

$assignment_id = $_GET['assignment_id'];



// Handle adding questions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $question_text = $_POST['question_text'];
    $option_1 = $_POST['option_1'];
    $option_2 = $_POST['option_2'];
    $option_3 = $_POST['option_3'];
    $option_4 = $_POST['option_4'];
    $correct_option = $_POST['correct_option'];

    $insert_query = "INSERT INTO objective_questions (assignment_id, question_text, option_1, option_2, option_3, option_4, correct_option)
                     VALUES ('$assignment_id', '$question_text', '$option_1', '$option_2', '$option_3', '$option_4', '$correct_option')";
    if (mysqli_query($conn, $insert_query)) {
        $success_message = "Question added successfully!";
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>
    <div class="container my-4">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="card-title text-primary">Add Objective Questions</h4>

                <!-- Display success or error messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Objective Question Form -->
                <form action="" method="POST">
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question</label>
                        <textarea class="form-control" id="question_text" name="question_text" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="option_1" class="form-label">Option 1</label>
                        <input type="text" class="form-control" id="option_1" name="option_1" required>
                    </div>
                    <div class="mb-3">
                        <label for="option_2" class="form-label">Option 2</label>
                        <input type="text" class="form-control" id="option_2" name="option_2" required>
                    </div>
                    <div class="mb-3">
                        <label for="option_3" class="form-label">Option 3</label>
                        <input type="text" class="form-control" id="option_3" name="option_3" required>
                    </div>
                    <div class="mb-3">
                        <label for="option_4" class="form-label">Option 4</label>
                        <input type="text" class="form-control" id="option_4" name="option_4" required>
                    </div>
                    <div class="mb-3">
                        <label for="correct_option" class="form-label">Correct Option (1, 2, 3, or 4)</label>
                        <input type="number" class="form-control" id="correct_option" name="correct_option" min="1" max="4" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </form>
            </div>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</body>
</html>
