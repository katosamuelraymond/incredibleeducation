<?php
session_start();
include 'database.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_query = "DELETE FROM users WHERE user_id = '$delete_id' AND role = 'student'";
    mysqli_query($conn, $delete_query);
    header("Location: managestudent.php");
    exit();
}

// Fetch students and their courses
$query = "SELECT 
            u.user_id, 
            u.firstname, 
            u.lastname, 
            u.email, 
            GROUP_CONCAT(c.course_name SEPARATOR ', ') AS courses
          FROM users u
          LEFT JOIN course_enrollments ce ON u.user_id = ce.user_id
          LEFT JOIN courses c ON ce.course_id = c.course_id
          WHERE u.role = 'student' OR u.role = 'admin' OR u.role ='instructor'
          GROUP BY u.user_id";

$students_courses = mysqli_query($conn, $query);
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold">Manage Students</h2>
    </div>

    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <!-- Add the table-responsive class to this div -->
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Student Name</th>
                <th>Email</th>
                <th>Enrolled Courses</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php if (mysqli_num_rows($students_courses) > 0): $count = 1; ?>
                <?php while ($row = mysqli_fetch_assoc($students_courses)): ?>
                  <tr>
                    <td><?php echo $count++; ?></td>
                    <td><?php echo $row['firstname'] . ' ' . $row['lastname']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['courses'] ?: 'No courses yet'; ?></td>
                    <td>
                      <a href="managestudent.php?delete=<?php echo $row['user_id']; ?>" class="btn btn-danger btn-sm"
                        onclick="return confirm('Are you sure you want to remove this student?')">
                        Remove
                      </a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="5" class="text-center">No students found.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
</div>


  <?php include "includes/footer.php"; ?>
</main>

