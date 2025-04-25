<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch instructor profile
$sql = "SELECT * FROM users WHERE user_id = $user_id";
$instuctors_result = mysqli_query($conn, $sql);
$profile = mysqli_fetch_assoc($instuctors_result);

// Fetch instructor active courses
$course_sql = "
    SELECT c.course_id, c.course_name, c.course_image, c.category_name, c.course_start_date, c.course_end_date
    FROM instructor_courses ic
    JOIN courses c ON ic.course_id = c.course_id
    WHERE ic.user_id = $user_id AND c.status = 'active'
";
$course_result = mysqli_query($conn, $course_sql);
?>

<?php  include "dashboard includes/header.php"; ?>
<?php  include 'includes/navbar.php'; ?>

<body>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Instructor Dashboard</h5>
                        <p>Welcome back, <?= ucfirst($profile['firstname']); ?>!</p>
                        <p>Your Active Courses: <?= mysqli_num_rows($course_result); ?></p>
                        <p>Upcoming lecture: Mathematics 101</p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" id="courses-tab" data-bs-toggle="tab" href="#courses">Courses</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="assignments-tab" data-bs-toggle="tab" href="#assignments">Assignments</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="grading-tab" data-bs-toggle="tab" href="#grading">Grading</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="resources-tab" data-bs-toggle="tab" href="#resources">Resources</a>
                            </li>
                        </ul>

                        <!-- Tab Contents -->
                        <div class="tab-content mt-3">
                            <!-- Courses Tab -->
                            <div class="tab-pane fade show active" id="courses">
                                <h5>Your Active Courses</h5>
                                <?php if (mysqli_num_rows($course_result) > 0): ?>
                                    <div class="row">
                                        <?php while ($row = mysqli_fetch_assoc($course_result)): ?>
                                            <div class="col-md-12 mb-4 col-sm-12 col-lg-6">
                                                <div class="card h-100">
                                                    <img src="<?= htmlspecialchars($row['course_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['course_name']) ?>" style="height:200px;">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= htmlspecialchars($row['course_name']) ?></h5>
                                                        <p class="card-text">
                                                            <strong>Category:</strong> <?= htmlspecialchars($row['category_name']) ?><br>
                                                            <strong>Start:</strong> <?= date('M d, Y', strtotime($row['course_start_date'])) ?><br>
                                                            <strong>End:</strong> <?= date('M d, Y', strtotime($row['course_end_date'])) ?>
                                                        </p>
                                                        <a href="course.php?course_id=<?= $row['course_id'] ?>" class="btn btn-primary btn-sm">Manage Course</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">You have no active courses assigned yet.</p>
                                <?php endif; ?>
                            </div>

                            
                            <!-- Assignments Tab -->
                            <!-- Assignments Tab -->
                            <div class="tab-pane fade" id="assignments">
                                <h5>Manage Assignments</h5>
                                <a class="btn btn-primary mb-3" href="assignments.php">Create New Assignment</a>

                                <?php
                                // Fetch assignments for instructor's courses
                                $assignment_query = "
                                    SELECT a.*, c.course_name, 
                                        (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.assignment_id) AS submissions
                                    FROM assignments a
                                    JOIN courses c ON a.course_id = c.course_id
                                    JOIN instructor_courses ic ON c.course_id = ic.course_id
                                    WHERE ic.user_id = $user_id
                                    ORDER BY a.due_date ASC
                                ";
                                $assignment_result = mysqli_query($conn, $assignment_query);
                                ?>

                                <?php if (mysqli_num_rows($assignment_result) > 0): ?>
                                    <div class="list-group">
                                        <?php while ($assignment = mysqli_fetch_assoc($assignment_result)): ?>
                                            <div class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row gap-2">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <?= htmlspecialchars($assignment['assignment_title']) ?>
                                                        <small class="text-muted">(<?= htmlspecialchars($assignment['assignment_type']) ?>)</small>
                                                    </h6>
                                                    <p class="mb-1"><?= htmlspecialchars($assignment['assignment_description']) ?></p>
                                                    <p class="mb-0">
                                                        <strong>Course:</strong> <?= htmlspecialchars($assignment['course_name']) ?><br>
                                                        <strong>Due:</strong> <?= date('M d, Y', strtotime($assignment['due_date'])) ?><br>
                                                        <strong>Submissions:</strong> <?= $assignment['submissions'] ?> student(s)
                                                    </p>
                                                </div>
                                                <div class="d-flex flex-column align-items-end justify-content-center">
                                                    <a href="editassignment.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-warning mb-1">
                                                        <i class="bi bi-pencil-square"></i> Edit
                                                    </a>
                                                    <a href="deleteassignment.php?id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this assignment?');">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                    <a href="viewassignment.php?assignment_id=<?= $assignment['assignment_id'] ?>" class="btn btn-sm btn-info mt-1">
                                                        <i class="bi bi-eye"></i> View Submissions
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">No assignments created yet.</p>
                                <?php endif; ?>
                            </div>



                            <!-- Grading Tab -->
                          
                            <div class="tab-pane fade" id="grading">
                                <h5>Grade Assignments</h5>

                                <?php
                                // Fetch all submissions for instructor's courses
                                $grading_query = "
                                SELECT s.submission_id, s.assignment_id, s.user_id, s.submitted_at, s.file_path, s.answer_text, s.grade,
                                    a.assignment_title, a.assignment_type,
                                    u.firstname, u.lastname, c.course_name
                                FROM assignment_submissions s
                                JOIN assignments a ON s.assignment_id = a.assignment_id
                                JOIN courses c ON a.course_id = c.course_id
                                JOIN instructor_courses ic ON c.course_id = ic.course_id
                                JOIN users u ON s.user_id = u.user_id
                                WHERE ic.user_id = $user_id
                                ORDER BY s.submitted_at DESC";
                                $grading_result = mysqli_query($conn, $grading_query);
                             ?>

                                <?php if (mysqli_num_rows($grading_result) > 0): ?>
                                    <form action="update-grades.php" method="POST">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Student</th>
                                                    <th>Course</th>
                                                    <th>Assignment</th>
                                                    <th>Submitted At</th>
                                                    <th>Answer</th>
                                                    <th>Grade</th>
                                                    <th>Update</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php while ($row = mysqli_fetch_assoc($grading_result)): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($row['firstname'] . " " . $row['lastname']) ?></td>
                                                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                                                        <td><?= htmlspecialchars($row['assignment_title']) ?></td>
                                                        <td><?= htmlspecialchars($row['submitted_at']) ?></td>
                                                        <td>
                                                            <?php if ($row['file_path']): ?>
                                                                <a href="<?= $row['file_path'] ?>" target="_blank">Download</a>
                                                            <?php else: ?>
                                                                <?= nl2br(htmlspecialchars($row['answer_text'])) ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td style="width: 100px;">
                                                            <input type="number" name="grades[<?= $row['submission_id'] ?>]" value="<?= htmlspecialchars($row['grade']) ?>" class="form-control" step="0.1" min="0" max="100">
                                                        </td>
                                                        <td>
                                                            <button type="submit" name="submit_id" value="<?= $row['submission_id'] ?>" class="btn btn-sm btn-success">
                                                                <i class="bi bi-check-circle"></i> Grade
                                                            </button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </form>
                                <?php else: ?>
                                    <p class="text-muted">No submissions available for grading.</p>
                                <?php endif; ?>
                            </div>


                                    <!-- Resources Tab -->
<div class="tab-pane fade" id="resources">
    <h5>Uploaded Resources</h5>

    <!-- Upload Form -->
    <form action="#resources" method="POST" enctype="multipart/form-data" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <label for="course_id" class="form-label">Course</label>
                <select name="course_id" class="form-control" required>
                    <option value="">Select Course</option>
                    <?php
                    $courses_query = "
                        SELECT c.course_id, c.course_name
                        FROM instructor_courses ic
                        JOIN courses c ON ic.course_id = c.course_id
                        WHERE ic.user_id = $user_id
                    ";
                    $courses_result = mysqli_query($conn, $courses_query);
                    while ($course = mysqli_fetch_assoc($courses_result)) {
                        echo "<option value='{$course['course_id']}'>{$course['course_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Title</label>
                <input type="text" name="resource_title" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Upload File</label>
                <input type="file" name="resource_file" class="form-control" required>
            </div>
            <div class="col-12 mt-2">
                <label class="form-label">Description</label>
                <textarea name="resource_description" class="form-control" rows="2" required></textarea>
            </div>
            <div class="col-12 mt-3">
                <button type="submit" name="upload_resource" class="btn btn-success">Upload Resource</button>
            </div>
        </div>
    </form>

    <?php
    // Upload handler
    if (isset($_POST['upload_resource'])) {
        $course_id = $_POST['course_id'];
        $title = mysqli_real_escape_string($conn, $_POST['resource_title']);
        $description = mysqli_real_escape_string($conn, $_POST['resource_description']);

        if ($_FILES['resource_file']['error'] === 0) {
            $file_name = $_FILES['resource_file']['name'];
            $tmp_name = $_FILES['resource_file']['tmp_name'];
            $file_path = 'uploads/lesson_materials/' . time() . '_' . basename($file_name);
            if (move_uploaded_file($tmp_name, $file_path)) {
                $insert_query = "
                    INSERT INTO lesson_materials (course_id, title, description, file_path)
                    VALUES ('$course_id', '$title', '$description', '$file_path')
                ";
                mysqli_query($conn, $insert_query);
                echo '<div class="alert alert-success mt-2">Resource uploaded successfully!</div>';
            } else {
                echo '<div class="alert alert-danger mt-2">File upload failed.</div>';
            }
        }
    }

    // Delete handler
    if (isset($_GET['delete_resource'])) {
        $id = intval($_GET['delete_resource']);
        $get_file = mysqli_query($conn, "SELECT file_path FROM lesson_materials WHERE id = $id");
        if ($file_row = mysqli_fetch_assoc($get_file)) {
            if (file_exists($file_row['file_path'])) {
                unlink($file_row['file_path']);
            }
        }
        mysqli_query($conn, "DELETE FROM lesson_materials WHERE id = $id");
        echo '<div class="alert alert-warning mt-2">Resource deleted.</div>';
    }

    // Fetch resources again after upload/delete
    $resources_query = "
    SELECT lm.*, c.course_name
    FROM lesson_materials lm
    JOIN courses c ON lm.course_id = c.course_id
    JOIN instructor_courses ic ON ic.course_id = c.course_id
    WHERE ic.user_id = $user_id 
    ORDER BY lm.created_at DESC
";

    $resources_result = mysqli_query($conn, $resources_query);
    ?>

    <?php if (mysqli_num_rows($resources_result) > 0): ?>
        <div class="list-group mt-3">
            <?php while ($row = mysqli_fetch_assoc($resources_result)): ?>
                <div class="list-group-item d-flex justify-content-between align-items-start flex-column flex-md-row gap-3">
                    <div>
                        <h6 class="mb-1"><?= htmlspecialchars($row['title']) ?></h6>
                        <p class="mb-1"><?= htmlspecialchars($row['description']) ?></p>
                        <p class="mb-0"><strong>Course:</strong> <?= htmlspecialchars($row['course_name']) ?></p>
                        <small class="text-muted">Uploaded on: <?= date('M d, Y', strtotime($row['uploaded_at'])) ?></small>
                    </div>
                    <div class="d-flex flex-column align-items-end">
                        <a href="<?= $row['file_path'] ?>" class="btn btn-sm btn-primary mb-1" target="_blank">
                            <i class="bi bi-file-earmark-arrow-down"></i> Download
                        </a>
                        <a href="editresource.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning mb-1">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="?delete_resource=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this resource?');">
                            <i class="bi bi-trash"></i> Delete
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No resources uploaded yet.</p>
    <?php endif; ?>
</div>

                        </div>
                        <!-- End Tab Contents -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</main>
