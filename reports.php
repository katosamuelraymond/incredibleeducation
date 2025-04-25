<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}


include('database.php'); 

// Fetch total instructors
$instructorQuery = "SELECT COUNT(*) AS total_instructors FROM users WHERE role = 'instructor'";
$instructorResult = mysqli_query($conn, $instructorQuery);
$instructorData = mysqli_fetch_assoc($instructorResult);

// Fetch total courses
$courseQuery = "SELECT COUNT(*) AS total_courses FROM courses";
$courseResult = mysqli_query($conn, $courseQuery);
$courseData = mysqli_fetch_assoc($courseResult);

// Fetch total students
$studentQuery = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$studentResult = mysqli_query($conn, $studentQuery);
$studentData = mysqli_fetch_assoc($studentResult);

// Fetch total revenue
$revenueQuery = "SELECT SUM(price) AS total_revenue FROM courses WHERE course_id IN (SELECT course_id FROM course_enrollments)";
$revenueResult = mysqli_query($conn, $revenueQuery);
$revenueData = mysqli_fetch_assoc($revenueResult);

// Fetch recent enrollments
$enrollmentQuery = "SELECT e.enrollment_id, u.firstname, u.lastname, c.course_name, i.firstname AS instructor_firstname, i.lastname AS instructor_lastname, e.enrollment_date 
                    FROM course_enrollments e
                    JOIN users u ON e.user_id = u.user_id
                    JOIN courses c ON e.course_id = c.course_id
                    JOIN users i ON c.instructor_id = i.user_id
                    ORDER BY e.enrollment_date DESC LIMIT 5";
$enrollmentResult = mysqli_query($conn, $enrollmentQuery);
?>


<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border: none;
      border-left: 4px solid;
    }
    .table thead {
      background-color: #343a40;
      color: white;
    }
  </style>

<main class="main-content" id="mainContent">
  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
<div class="container mt-4">
  <h2 class="mb-4">Platform Reports</h2>

  <div class="row g-4">
      <div class="col-md-3">
        <div class="card border-primary">
          <div class="card-body">
            <h5 class="card-title text-primary"><i class="bi bi-person-badge"></i> Total Instructors</h5>
            <h2><?php echo $instructorData['total_instructors']; ?></h2>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card border-warning">
          <div class="card-body">
            <h5 class="card-title text-warning"><i class="bi bi-book"></i> Total Courses</h5>
            <h2><?php echo $courseData['total_courses']; ?></h2>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card border-success">
          <div class="card-body">
            <h5 class="card-title text-success"><i class="bi bi-people"></i> Total Students</h5>
            <h2><?php echo $studentData['total_students']; ?></h2>
          </div>
        </div>
      </div>

      <div class="col-md-3">
        <div class="card border-danger">
          <div class="card-body">
            <h5 class="card-title text-danger"><i class="bi bi-currency-dollar"></i> Total Revenue</h5>
            <h2 class="fs-3">UGX<?php echo number_format($revenueData['total_revenue'], 2); ?></h2>
          </div>
        </div>
      </div>
    </div>

    <!-- Detailed Table -->
    <div class="mt-5">
      <h4>Recent Course Enrollments</h4>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead>
            <tr>
              <th>#</th>
              <th>Student</th>
              <th>Course</th>
              <th>Instructor</th>
              <th>Date Enrolled</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $index = 1;
            while ($enrollment = mysqli_fetch_assoc($enrollmentResult)) {
            ?>
              <tr>
                <td><?php echo $index++; ?></td>
                <td><?php echo $enrollment['firstname'] . ' ' . $enrollment['lastname']; ?></td>
                <td><?php echo $enrollment['course_name']; ?></td>
                <td><?php echo $enrollment['instructor_firstname'] . ' ' . $enrollment['instructor_lastname']; ?></td>
                <td><?php echo $enrollment['enrollment_date']; ?></td>
              </tr>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>


</div>

<?php include "includes/footer.php"; ?>
</main>
