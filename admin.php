<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}
$pageTitle = 'Student Dashboard';

include 'database.php';

$user_id = $_SESSION['user_id']; // Use session user_id

// Fetch the user's full name
$user_query = "SELECT firstname, lastname FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Fetch the Welcome Message
$welcome_query = "SELECT message FROM welcome_message WHERE user_id = '$user_id' ORDER BY updated_at DESC LIMIT 1";
$welcome_result = mysqli_query($conn, $welcome_query);
$welcome_message = mysqli_fetch_assoc($welcome_result);

// Fetch enrolled courses
// $courses_query = "SELECT courses.course_name, courses.course_description 
//                   FROM courses 
//                   JOIN user_courses ON courses.course_id = user_courses.course_id 
//                   WHERE user_courses.user_id = '$user_id'";
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
$courses_result = mysqli_query($conn, $courses_query);

// Fetch upcoming lessons
$lessons_query = "SELECT l.lesson_title,  c.course_name, l.lesson_date
                  FROM lessons l
                  JOIN courses c ON l.course_id = c.course_id
                  WHERE l.course_id IN (
                      SELECT course_id FROM user_courses WHERE user_id = '$user_id'
                  )
                  AND l.lesson_date > NOW()
                  ORDER BY l.lesson_date ASC
                  LIMIT 5";
$lessons_result= mysqli_query($conn, $lessons_query);

// Count total courses
$courses_count_query = "SELECT COUNT(*) AS total_courses FROM courses";
$courses_count_result = mysqli_query($conn, $courses_count_query);
$courses_count = mysqli_fetch_assoc($courses_count_result)['total_courses'] ?? 0;

// Count active instructors
$instructors_count_query = "SELECT COUNT(*) AS total_instructors FROM users WHERE role = 'instructor' ";
$instructors_count_result = mysqli_query($conn, $instructors_count_query);
$instructors_count = mysqli_fetch_assoc($instructors_count_result)['total_instructors'] ?? 0;

// Count total students
$students_count_query = "SELECT COUNT(*) AS total_students FROM users WHERE role = 'student'";
$students_count_result = mysqli_query($conn, $students_count_query);
$students_count = mysqli_fetch_assoc($students_count_result)['total_students'] ?? 0;
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<body>

<!-- Sidebar -->
<?php include 'dashboard includes/aside.php'; ?>

<!-- Main Content -->
<main class="main-content " id="mainContent">

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
          <h4 class="text-primary">Welcome back, <?php echo $user_data['firstname'] . ' ' . $user_data['lastname']; ?>!</h4>
          <?php if ($welcome_message): ?>
            <p class="text-success"><?php echo $welcome_message['message']; ?></p>
          <?php endif; ?>
          <p class="text-muted">Access your courses, track progress, and stay updated on lessons.</p>
        <?php else: ?>
          <div class="alert alert-warning">Student information not found.</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card border-start border-primary border-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Courses</h5>
          <p class="card-text"><?php echo $courses_count; ?> total</p>
          <i class="bi bi-journal-code display-6 text-primary"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-start border-success border-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Instructors</h5>
          <p class="card-text"><?php echo $instructors_count; ?> total</p>
          <i class="bi bi-person-badge display-6 text-success"></i>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-start border-warning border-4 shadow-sm">
        <div class="card-body">
          <h5 class="card-title">Students</h5>
          <p class="card-text"><?php echo $students_count; ?> enrolled</p>
          <i class="bi bi-people-fill display-6 text-warning"></i>
        </div>
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
                    <a href="#" class="btn btn-primary btn-sm">Go to Course</a>
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
        <?php if (mysqli_num_rows($lessons_result) > 0): ?>
          <?php while ($lesson = mysqli_fetch_assoc($lessons_result)): ?>
            <li class="list-group-item">
              <h6 class="fw-bold"><?php echo htmlspecialchars($lesson['lesson_title']); ?></h6>
              <p><?php echo nl2br(htmlspecialchars($lesson['lesson_content'])); ?></p>
              <p><small class="text-muted">
                <?php echo htmlspecialchars($lesson['course_name']); ?> - 
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
