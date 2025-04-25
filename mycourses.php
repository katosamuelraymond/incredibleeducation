<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
include 'database.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit();
}

// Get user ID from session
$user_id = $_SESSION['user_id']; // Use the logged-in user's ID from the session

// Fetch courses where this user is enrolled
$query = "
    SELECT 
        c.course_id, 
        c.course_name, 
        c.course_description, 
        u.firstname, 
        u.lastname,
        c.course_image
    FROM courses c
    JOIN users u ON c.instructor_id = u.user_id
    JOIN course_enrollments ce ON c.course_id = ce.course_id
    WHERE ce.user_id = '$user_id'
";

// Execute the query and check if it's successful
$enrollment_result = mysqli_query($conn, $query);

// Check for errors in the query
if (!$enrollment_result) {
    die('Query Error: ' . mysqli_error($conn));
}

?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>

<!-- Sidebar -->
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">

  <!-- Close Sidebar Button on Right -->
  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">My Courses</h2>
    <span class="btn btn-outline-primary d-md-none" id="openSidebar"><i class="bi bi-list"></i></span>
  </div>

  <div class="container">
    <?php if (mysqli_num_rows($enrollment_result) > 0): ?>
      <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($enrollment_result)): ?>
          <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
              <img src="<?php echo $row['course_image']; ?>" class="card-img-top" alt="<?php echo $row['course_name']; ?>" style="height:250px;">
              <div class="card-body">
                <h5 class="card-title"><?php echo $row['course_name']; ?></h5>
                <p class="card-text text-muted"><?php echo substr($row['course_description'], 0, 100) . '...'; ?></p>
                <p><strong>Instructor:</strong> <?php echo $row['firstname'] . ' ' . $row['lastname']; ?></p>
                <hr>
                <a href="coursepage.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-outline-primary btn-sm">View Course</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    <?php else: ?>
      <div class="alert alert-info">
        You are not enrolled in any courses yet.
      </div>
    <?php endif; ?>
  </div>

<?php include "includes/footer.php"; ?>

</main>

<!-- Custom CSS for Hover Effects -->
<style>
  .card:hover {
    transform: scale(1.05);
    transition: transform 0.3s ease-in-out;
  }
</style>

</body>
</html>
