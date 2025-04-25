<?php
session_start();

include 'database.php';


$education_filter = isset($_GET['education']) ? mysqli_real_escape_string($conn, $_GET['education']) : '';




// Fetch courses with their education level (filtered or all)
$query = "
    SELECT c.*, cat.category_name, edu.level_name, c.course_image, c.course_description 
    FROM courses c
    LEFT JOIN categories cat ON c.category_id = cat.category_id
    LEFT JOIN education_levels edu ON c.education_level_id = edu.education_level_id
";

if ($education_filter) {
    $query .= " WHERE edu.level_name = '$education_filter'";
}

$query .= " ORDER BY edu.level_name ASC, c.course_start_date ASC";

$education_result = mysqli_query($conn, $query);

// Group courses by education level
$courses_by_level = [];
while ($row = mysqli_fetch_assoc($education_result)) {
    $level = $row['level_name'];

   
    if (!isset($courses_by_level[$level])) {
        $courses_by_level[$level] = [];
    }
    $courses_by_level[$level][] = $row;
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>



  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold">Courses </h2>
    </div>

    <!-- Education Level Filter Buttons -->
    <div class="mb-4">
      <a href="?education=O-Level" class="btn btn-outline-primary <?php if ($education_filter == 'O-Level') echo 'active'; ?>">O-Level</a>
      <a href="?education=A-Level" class="btn btn-outline-primary <?php if ($education_filter == 'A-Level') echo 'active'; ?>">A-Level</a>
      <a href="?education=University" class="btn btn-outline-primary <?php if ($education_filter == 'University') echo 'active'; ?>">University</a>
      <a href="courses.php" class="btn btn-outline-secondary <?php if (!$education_filter) echo 'active'; ?>">All</a>
    </div>

    <?php if (!empty($courses_by_level)): ?>
      <?php foreach ($courses_by_level as $level_name => $courses): ?>
        <h4 class="text-primary mt-4"><?php echo htmlspecialchars($level_name); ?></h4>
        <div class="row g-4">
          <?php foreach ($courses as $row): ?>
            <div class="col-md-4">
              <div class="card border-0 shadow-sm rounded-4 h-100">
                <img src="<?php echo $row['course_image']; ?>" class="card-img-top" alt="<?php echo $row['course_name']; ?>" style="height:250px;">
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                  <p class="card-text text-muted"><?php echo substr($row['course_description'], 0, 100) . '...'; ?></p>
                  <p><strong>Instructor:</strong> 
                    <?php
                    $courseId = $row['course_id'];
                    $instructorQuery = "
                        SELECT u.firstname, u.lastname 
                        FROM users u 
                        JOIN instructor_courses ic ON u.user_id = ic.user_id 
                        WHERE ic.course_id = $courseId
                    ";
                    $instructorResult = mysqli_query($conn, $instructorQuery);
                    $instructors = [];

                    while ($inst = mysqli_fetch_assoc($instructorResult)) {
                        $instructors[] = $inst['firstname'] . ' ' . $inst['lastname'];
                    }

                    echo !empty($instructors) ? implode(", ", $instructors) : "No instructor assigned";
                    ?>
                  </p>
                  <hr>
                  <a href="coursepage.php?course_id=<?php echo $row['course_id']; ?>" class="btn btn-outline-primary btn-sm">View Course</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="alert alert-info">
        No courses available<?php echo $education_filter ? " for $education_filter" : ""; ?>.
      </div>
    <?php endif; ?>
  </div>

  <?php include "includes/footer.php"; ?>

