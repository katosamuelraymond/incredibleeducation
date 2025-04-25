<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['course_id'])) {
    echo "Course ID is missing.";
    exit();
}

$course_id = intval($_GET['course_id']);

// Check if the student is already enrolled in this course
$check_sql = "SELECT * FROM course_enrollments WHERE user_id = $user_id AND course_id = $course_id";
$check_result = mysqli_query($conn, $check_sql);

if (mysqli_num_rows($check_result) > 0) {
    header("Location: courses.php?status=already_enrolled");


    echo "<div> . already enrolled' .</div>";
    exit();
}

// Enroll the student by inserting into the course_enrollments table
$enroll_date = date('Y-m-d H:i:s'); // Current date and time
$enroll_sql = "INSERT INTO course_enrollments (user_id, course_id, enrollment_date) 
               VALUES ($user_id, $course_id, '$enroll_date')";

if (mysqli_query($conn, $enroll_sql)) {
    header("Location: index.php?status=enrolled_successfully");
    exit();
} else {
    echo "Failed to enroll. Please try again.";
}
?>
