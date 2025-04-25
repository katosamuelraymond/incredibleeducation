<?php
session_start();
include 'database.php';

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$course_id = $_GET['course_id'] ?? $_POST['course_id'] ?? null;
if (!$course_id) die("No course selected.");

$edit_mode = false;
$edit_lesson_data = null;
$edit_material_data = null;

if (isset($_GET['edit_lesson'])) {
    $edit_id = mysqli_real_escape_string($conn, $_GET['edit_lesson']);
    $edit_query = mysqli_query($conn, "SELECT * FROM lessons WHERE lesson_id = '$edit_id' AND course_id = '$course_id' LIMIT 1");
    if (mysqli_num_rows($edit_query) > 0) {
        $edit_mode = true;
        $edit_lesson_data = mysqli_fetch_assoc($edit_query);
        $material_query = mysqli_query($conn, "SELECT * FROM lesson_materials WHERE lesson_id = '$edit_id' LIMIT 1");
        $edit_material_data = mysqli_fetch_assoc($material_query);
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lesson_id = $_POST['lesson_id'] ?? null;
    $lesson_title = mysqli_real_escape_string($conn, $_POST['lesson_title']);
    // $lesson_content = mysqli_real_escape_string($conn, $_POST['content_html'] ?? '');
    $section_id = mysqli_real_escape_string($conn, $_POST['section_id']);
    
    $lesson_date = $_POST['lesson_date'] ? "'".$_POST['lesson_date']."'" : "NULL";
    $video_link = mysqli_real_escape_string($conn, $_POST['video_link'] ?? '');
    $content_html = mysqli_real_escape_string($conn, $_POST['content_html'] ?? '');
    $video_file_path = $download_file_path = null;

    mysqli_begin_transaction($conn);
    try {
        if ($lesson_id) {
            // EDIT
            $update_query = "UPDATE lessons SET section_id='$section_id',lesson_title='$lesson_title',  lesson_date=$lesson_date WHERE lesson_id='$lesson_id'";
            if (!mysqli_query($conn, $update_query)) throw new Exception("Lesson update failed: " . mysqli_error($conn));

            if (!empty($_FILES['video_file']['name'])) {
                $video_file_name = basename($_FILES['video_file']['name']);
                $video_file_path = 'uploads/lessons/videos/' . $video_file_name;
                if (!move_uploaded_file($_FILES['video_file']['tmp_name'], $video_file_path)) throw new Exception("Failed to upload video file");
            }

            if (!empty($_FILES['download_file']['name'])) {
                $download_file_name = basename($_FILES['download_file']['name']);
                $download_file_path = 'uploads/lessons/files/' . $download_file_name;
                if (!move_uploaded_file($_FILES['download_file']['tmp_name'], $download_file_path)) throw new Exception("Failed to upload download file");
            }

            $update_materials = "
                UPDATE lesson_materials SET
                video_link = " . ($video_link ? "'$video_link'" : "NULL") . ",
                content_html = " . ($content_html ? "'$content_html'" : "NULL") . ",
                video_file = " . ($video_file_path ? "'$video_file_path'" : "video_file") . ",
                download_file = " . ($download_file_path ? "'$download_file_path'" : "download_file") . "
                WHERE lesson_id = '$lesson_id'
            ";
            mysqli_query($conn, $update_materials);

        } else {
            // ADD
            $insert_lesson = "INSERT INTO lessons (course_id, section_id,  lesson_title,  lesson_date)
                              VALUES ('$course_id', '$section_id',  '$lesson_title',  $lesson_date)";
            if (!mysqli_query($conn, $insert_lesson)) throw new Exception("Lesson insert failed: " . mysqli_error($conn));
            $lesson_id = mysqli_insert_id($conn);

            if (!empty($_FILES['video_file']['name'])) {
                $video_file_name = basename($_FILES['video_file']['name']);
                $video_file_path = 'uploads/lessons/videos/' . $video_file_name;
                move_uploaded_file($_FILES['video_file']['tmp_name'], $video_file_path);
            }

            if (!empty($_FILES['download_file']['name'])) {
                $download_file_name = basename($_FILES['download_file']['name']);
                $download_file_path = 'uploads/lessons/files/' . $download_file_name;
                move_uploaded_file($_FILES['download_file']['tmp_name'], $download_file_path);
            }

            $insert_materials = "INSERT INTO lesson_materials (course_id, section_id, lesson_id, video_link, video_file, content_html, download_file)
                                 VALUES ('$course_id', '$section_id', '$lesson_id', " .
                                 ($video_link ? "'$video_link'" : "NULL") . ", " .
                                 ($video_file_path ? "'$video_file_path'" : "NULL") . ", " .
                                 ($content_html ? "'$content_html'" : "NULL") . ", " .
                                 ($download_file_path ? "'$download_file_path'" : "NULL") . ")";
            mysqli_query($conn, $insert_materials);
        }

        mysqli_commit($conn);
        $_SESSION['message'] = $lesson_id ? "Lesson updated successfully!" : "Lesson added successfully!";
        header("Location: addcoursecontent.php?course_id=$course_id");
        exit;
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $_SESSION['message'] = $e->getMessage();
    }
}

// DELETE
if (isset($_GET['delete_lesson'])) {
    $lesson_id = mysqli_real_escape_string($conn, $_GET['delete_lesson']);
    mysqli_query($conn, "DELETE FROM lesson_materials WHERE lesson_id = '$lesson_id'");
    mysqli_query($conn, "DELETE FROM lessons WHERE lesson_id = '$lesson_id'");
    $_SESSION['message'] = "Lesson deleted successfully!";
    header("Location: addcoursecontent.php?course_id=$course_id");
    exit;
}

// Fetch for display
$sections = mysqli_query($conn, "SELECT * FROM course_sections WHERE course_id='$course_id' ORDER BY section_order");
$lessons_query = mysqli_query($conn, "
    SELECT l.*, s.section_title, s.section_order 
    FROM lessons l
    JOIN course_sections s ON l.section_id = s.id
    WHERE l.course_id = '$course_id'
    ORDER BY s.section_order ASC, l.lesson_id ASC
");

if (!$lessons_query) {
    die("Query failed: " . mysqli_error($conn));
}

$lessons = mysqli_fetch_all($lessons_query, MYSQLI_ASSOC);
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Add Course Content</h2>
        <a href="addcourse.php" class="btn btn-primary">+ Add New Course</a>
    </div>

<form method="POST" enctype="multipart/form-data">
    <?php if ($edit_mode): ?>
        <input type="hidden" name="lesson_id" value="<?= $edit_lesson_data['lesson_id'] ?>">
    <?php endif; ?>
    <input type="hidden" name="course_id" value="<?= $course_id ?>">

    <div class="mb-3">
        <label>Section</label>
        <select name="section_id" class="form-select" required>
            <option value="">Select Section</option>
            <?php while ($sec = mysqli_fetch_assoc($sections)): ?>
                <option value="<?= $sec['id']; ?>" <?= $edit_mode && $edit_lesson_data['section_id'] == $sec['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sec['section_title']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Lesson Title</label>
        <input type="text" name="lesson_title" class="form-control" required value="<?= $edit_lesson_data['lesson_title'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label>Lesson Date</label>
        <input type="datetime-local" name="lesson_date" class="form-control"
               value="<?= $edit_mode && $edit_lesson_data['lesson_date'] ? date('Y-m-d\TH:i', strtotime($edit_lesson_data['lesson_date'])) : '' ?>">
    </div>

    <div class="mb-3">
        <label>Video Link</label>
        <input type="url" name="video_link" class="form-control" value="<?= $edit_material_data['video_link'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label>Video Upload 
            <?= isset($edit_material_data['video_file']) && $edit_material_data['video_file'] ? "(Current: " . basename($edit_material_data['video_file']) . ")" : "" ?>
        </label>
        <input type="file" name="video_file" class="form-control">
    </div>

    <div class="mb-3">
        <label>HTML Content</label>
        <textarea name="content_html" class="form-control" rows="5"><?= $edit_material_data['content_html'] ?? '' ?></textarea>
    </div>

    <div class="mb-3">
        <label>Downloadable File 
            <?= isset($edit_material_data['download_file']) && $edit_material_data['download_file'] ? "(Current: " . basename($edit_material_data['download_file']) . ")" : "" ?>
        </label>
        <input type="file" name="download_file" class="form-control">
    </div>

    <button type="submit" class="btn btn-<?= $edit_mode ? 'warning' : 'primary' ?>">
        <?= $edit_mode ? 'Update Lesson' : 'Add Lesson' ?>
    </button>
</form>

<?php if (mysqli_num_rows($lessons_query) > 0): ?>
    <table class="table table-bordered mt-4">
        <thead>
        <tr>
            <th>Title</th>
            <th>Section</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lessons as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['lesson_title']) ?></td>
                <td><?= htmlspecialchars($row['section_title']) ?></td>
                <td><?= $row['lesson_date'] ? date('d M Y H:i', strtotime($row['lesson_date'])) : '—' ?></td>
                <td>
                    <a href="?course_id=<?= $course_id ?>&edit_lesson=<?= $row['lesson_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="?course_id=<?= $course_id ?>&delete_lesson=<?= $row['lesson_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete lesson?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
</main>



