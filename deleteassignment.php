<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['id'];

// Delete assignment
$delete_query = "DELETE FROM assignments WHERE assignment_id = $assignment_id";

if (mysqli_query($conn, $delete_query)) {
    header("Location: instructor.php#assignments");
} else {
    echo "Error deleting assignment: " . mysqli_error($conn);
}
?>
