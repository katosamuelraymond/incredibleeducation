<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<?php



// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor') {
    header("Location: login.php"); // Redirect to login if not logged in or not an admin
    exit();
}
include 'database.php';
$user_id = $_SESSION['user_id'];
$user_query ="SELECT username FROM users WHERE user_id = '$user_id' AND role = 'instructor' ";
$user =mysqli_query($conn, $user_query);
$user_data= mysqli_fetch_assoc($user);

?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
  <h2 class="fw-bold">Instructor Dashboard</h2>

  <div class="container my-4">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
      <h4 class="text-primary">Welcome back, <?php echo $user_data['username'] ;?>!</h4>
        <p class="text-muted">Manage your courses and interact with your students.</p>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-md-4">
      <div class="card border-start border-primary border-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">My Courses</h5>
          <a href="mycourses.php" class="btn btn-outline-primary btn-sm">Go</a>
        </div>
      </div>
    </div>
    
    <div class="col-md-4">
      <div class="card border-start border-info border-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Create Lesson</h5>
          <a href="addcoursecontent.php" class="btn btn-outline-info btn-sm">Create</a>
        </div>
      </div>
    </div>
  </div>

<?php include "includes/footer.php"; ?>
</main>
