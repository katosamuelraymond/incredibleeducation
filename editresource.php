<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get resource ID from query string
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Invalid resource ID.";
    exit();
}

$resource_id = intval($_GET['id']);

// Fetch the resource details
$sql = "
    SELECT lm.*, c.course_name
    FROM lesson_materials lm
    JOIN courses c ON lm.course_id = c.course_id
    JOIN instructor_courses ic ON ic.course_id = c.course_id
    WHERE lm.id = $resource_id AND ic.user_id = $user_id AND (lm.lesson_date IS NULL OR lm.lesson_date = '')
";

$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) !== 1) {
    echo "Resource not found or access denied.";
    exit();
}

$resource = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $updated_at = date('Y-m-d H:i:s');

    // File update logic
    $file_path = $resource['file_path']; // keep current file by default

    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === 0) {
        $upload_dir = 'uploads/resources/';
        $filename = time() . "_" . basename($_FILES['resource_file']['name']);
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($_FILES['resource_file']['tmp_name'], $target_path)) {
            $file_path = $target_path;
        }
    }

    // Update the resource
    $update_sql = "
        UPDATE lesson_materials 
        SET title = '$title', description = '$description', file_path = '$file_path', uploaded_at = '$updated_at'
        WHERE id = $resource_id
    ";

    if (mysqli_query($conn, $update_sql)) {
        header("Location: instructordashboard.php#resources");
        exit();
    } else {
        $error = "Error updating resource.";
    }
}
?>

<?php include 'dashboard includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<body>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="container mt-4">
        <h4>Edit Resource</h4>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Course</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($resource['course_name']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" required value="<?= htmlspecialchars($resource['title']) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" required><?= htmlspecialchars($resource['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Current File</label><br>
                <a href="<?= $resource['file_path'] ?>" target="_blank"><?= basename($resource['file_path']) ?></a>
            </div>
            <div class="mb-3">
                <label class="form-label">Replace File (optional)</label>
                <input type="file" name="resource_file" class="form-control">
            </div>

            <button type="submit" class="btn btn-success">Update Resource</button>
            <a href="instructor.php#resources" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
<?php include 'includes/footer.php'; ?>

</main>

