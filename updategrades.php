<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_id'])) {
    $submission_id = intval($_POST['submit_id']);
    $grades = $_POST['grades'];

    if (isset($grades[$submission_id])) {
        $grade = floatval($grades[$submission_id]);

        $update_query = "
            UPDATE assignment_submissions
            SET grade = '$grade'
            WHERE submission_id = $submission_id
        ";

        if (mysqli_query($conn, $update_query)) {
            $_SESSION['success'] = "Grade updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update grade: " . mysqli_error($conn);
        }
    }
}

header("Location: instructor.php#grading");
exit();
