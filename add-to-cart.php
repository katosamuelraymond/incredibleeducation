<?php
session_start();
include "database.php"; 

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add items to your cart.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'], $_POST['user_id'])) {
    $courseId = $_POST['course_id'];
    $userId = $_POST['user_id'];

    // Check if the course is already in the cart
    $query = "SELECT * FROM cart WHERE user_id = ? AND course_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $userId, $courseId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity if the course is already in the cart
        $query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND course_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $courseId);
        $stmt->execute();
        $_SESSION['message'] = "Course quantity updated in cart.";
    } else {
        // Add new course to cart
        $query = "INSERT INTO cart (user_id, course_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $courseId);
        $stmt->execute();
        $_SESSION['message'] = "Course added to cart.";
    }

   
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}
?>
