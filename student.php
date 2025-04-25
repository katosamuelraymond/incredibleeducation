<?php
session_start();

// Check if the user is logged in and is a student
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

$pageTitle = 'Student Dashboard';

include 'database.php';

$user_id = $_SESSION['user_id']; // Use session user_id

// Fetch the user's full name safely using prepared statements
$stmt = $conn->prepare("SELECT firstname, lastname FROM users WHERE user_id = ? AND role = ?");
$stmt->bind_param('is', $user_id, $role);
$role = 'student'; // Always check for the student role
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Fetch the Welcome Message
$welcome_query = "SELECT message FROM welcome_message WHERE user_id = '$user_id' ORDER BY updated_at DESC LIMIT 1";
$welcome_result = mysqli_query($conn, $welcome_query);
$welcome_message = mysqli_fetch_assoc($welcome_result);

// Fetch enrolled courses
$courses_query = "
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
// $enrollment_result = mysqli_query($conn, $query);
// $courses_query = "SELECT courses.course_name, courses.course_description 
                  // FROM courses 
                  // JOIN user_courses ON courses.course_id = user_courses.course_id 
                  // WHERE user_courses.user_id = '$user_id'";
$courses_result = mysqli_query($conn, $courses_query);

// Fetch upcoming lessons (Simplified query as requested)
$upcoming_lessons_query = "SELECT * FROM upcoming_lessons WHERE lesson_date > NOW() ORDER BY lesson_date ASC";
$upcoming_lessons_result = mysqli_query($conn, $upcoming_lessons_query);
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>

<!-- Sidebar -->
<?php include 'dashboard includes/aside.php'; ?>

<!-- Main Content -->
<main class="main-content" id="mainContent">

  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Dashboard Overview</h2>
    <span class="btn btn-outline-primary d-md-none" id="openSidebar"><i class="bi bi-list"></i></span>
  </div>

  <?php if (isset($_GET['application']) && $_GET['application'] == 'success'): ?>
    <div class="alert alert-info">
        <?php echo $_GET['msg'] ?? 'Your application was submitted successfully. Please wait for admin approval.'; ?>
    </div>
  <?php endif; ?>

  <!-- Welcome Card -->
  <div class="container my-4">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <?php if ($user_data): ?>
          <h4 class="text-primary">Welcome back, <?php echo htmlspecialchars($user_data['firstname'] . ' ' . $user_data['lastname']); ?>!</h4>
          <?php if ($welcome_message): ?>
            <p class="text-success"><?php echo htmlspecialchars($welcome_message['message']); ?></p>
          <?php endif; ?>
          <p class="text-muted">Access your courses, track progress, and stay updated on lessons.</p>
        <?php else: ?>
          <div class="alert alert-warning">Student information not found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- My Courses -->
  <div class="container my-4">
    <div class="card shadow-sm border-0 rounded-4">
      <div class="card-body">
        <h5 class="card-title text-primary">My Courses</h5>
        <?php if (mysqli_num_rows($courses_result) > 0): ?>
          <div class="row">
            <?php while ($course = mysqli_fetch_assoc($courses_result)): ?>
              <div class="col-md-4 mb-3">
                <div class="card">
                  <div class="card-body">
                    <h6 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h6>
                    <p class="card-text"><?php echo htmlspecialchars($course['course_description']); ?></p>
                    <a href="coursepage.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-outline-primary btn-sm">View Course</a>

                  </div>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p>No courses enrolled yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Upcoming Lessons -->
  <div class="card shadow-sm border-0 rounded-4 my-4">
    <div class="card-body">
      <h5 class="card-title text-primary mb-3">Upcoming Lessons</h5>
      <ul class="list-group list-group-flush">
        <?php if (mysqli_num_rows($upcoming_lessons_result) > 0): ?>
          <?php while ($lesson = mysqli_fetch_assoc($upcoming_lessons_result)): ?>
            <li class="list-group-item">
              <h6 class="fw-bold"><?php echo htmlspecialchars($lesson['lesson_title']); ?></h6>
              <p><?php echo nl2br(htmlspecialchars($lesson['description'])); ?></p>
              <p><small class="text-muted">
                Scheduled for: <?php echo date('l, F j, Y, g:i A', strtotime($lesson['lesson_date'])); ?>
              </small></p>
            </li>
          <?php endwhile; ?>
        <?php else: ?>
          <li class="list-group-item">You have no upcoming lessons.</li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <?php include "includes/footer.php"; ?>
</main>


