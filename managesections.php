<?php
session_start();
include 'database.php';

// Check course_id
if (!isset($_GET['course_id'])) {
    die("Course ID is missing!");
}
$course_id = (int) $_GET['course_id'];

// Handle deletion
if (isset($_GET['delete_section'])) {
    $section_id = (int) $_GET['delete_section'];
    mysqli_query($conn, "DELETE FROM course_sections WHERE id = $section_id OR parent_id = $section_id");
}

// Insert multiple sections
if (isset($_POST['bulk_add_sections'])) {
    $lines = explode("\n", trim($_POST['bulk_sections']));
    $inserted = 0;
    $stack = []; // Tracks parent IDs per level

    foreach ($lines as $line) {
        $originalLine = rtrim($line);
        if ($originalLine === '') continue;

        // Detect indentation
        preg_match('/^( *)/', $line, $indentMatch);
        $indent = strlen($indentMatch[1]);
        $level = floor($indent / 2); // 2 spaces per level

        $title = trim($originalLine);
        if ($title === '') continue;

        // Determine parent
        $parent_id = $stack[$level - 1] ?? 'NULL';

        // Section order logic: number within current level
        $order = count(array_filter($stack, fn($k) => $k !== NULL)) + 1;

        $safe_title = mysqli_real_escape_string($conn, $title);
        $sql = "INSERT INTO course_sections (course_id, section_title, section_order, parent_id)
                VALUES ($course_id, '$safe_title', $order, $parent_id)";
        if (mysqli_query($conn, $sql)) {
            $new_id = mysqli_insert_id($conn);
            $stack[$level] = $new_id;

            // Clear deeper levels
            $stack = array_slice($stack, 0, $level + 1);

            $inserted++;
        }
    }

    $message = "$inserted section(s) added successfully from outline.";
}
if (isset($_GET['delete_all_sections'])) {
    mysqli_query($conn, "DELETE FROM course_sections WHERE course_id = $course_id");
    $message = "All sections deleted successfully!";
}

if (isset($_POST['update_section'])) {
    // Get the updated values
    $section_title = mysqli_real_escape_string($conn, $_POST['section_title']);
    $section_order = (int) $_POST['section_order'];
    $section_id = (int) $_GET['edit_section']; // Get the section ID from the query string

    // Update the section in the database
    $sql = "UPDATE course_sections SET section_title = '$section_title', section_order = $section_order WHERE id = $section_id";
    if (mysqli_query($conn, $sql)) {
        $message = "Section updated successfully!";
    } else {
        $message = "Error updating section!";
    }
}


// Function to display sections hierarchically
// Function to display sections hierarchically with edit functionality
function renderSections($conn, $course_id, $parent_id = NULL, $prefix = '') {
    $sql = "SELECT * FROM course_sections WHERE course_id = $course_id AND parent_id " . 
           ($parent_id === NULL ? "IS NULL" : "= $parent_id") . 
           " ORDER BY section_order";

    $result = mysqli_query($conn, $sql);
    $count = 1;

    while ($row = mysqli_fetch_assoc($result)) {
        $numbering = $prefix === '' ? $count : $prefix . '.' . $count;
        $indent = substr_count($numbering, '.') * 20;

        echo "<div style='margin-left: {$indent}px;' class='d-flex justify-content-between mb-2'>";
        
        if (isset($_GET['edit_section']) && $_GET['edit_section'] == $row['id']) {
            // If we're editing this section, show the edit form
            ?>
            <form method="POST" action="">
                <div class="d-flex mb-2">
                    <input type="text" name="section_title" value="<?php echo htmlspecialchars($row['section_title']); ?>" class="form-control me-2" required>
                    <input type="number" name="section_order" value="<?php echo $row['section_order']; ?>" class="form-control me-2" required>
                    <button type="submit" name="update_section" class="btn btn-success btn-sm">Save</button>
                    <a href="?course_id=<?php echo $course_id; ?>" class="btn btn-danger btn-sm ms-2">Cancel</a>
                </div>
            </form>
            <?php
        } else {
            // Display section details
            echo "<div><strong>$numbering</strong> " . htmlspecialchars($row['section_title']) . "</div>";
            echo "<div class='btn-group btn-group-sm'>
                    <a href='?course_id={$course_id}&edit_section={$row['id']}' class='btn btn-outline-warning btn-sm'>Edit</a>
                    <a href='?course_id={$course_id}&delete_section={$row['id']}' class='btn btn-outline-danger btn-sm' onclick=\"return confirm('Delete this section?');\">Delete</a>
                  </div>";
        }

        echo "</div>";

        // Recursively render child sections
        renderSections($conn, $course_id, $row['id'], $numbering);
        $count++;
    }
}


?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Manage Course Sections</h2>
        </div>

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            
            <div class="card-body">
                <h4 class="card-title text-primary">Bulk Add Sections</h4>
                <?php if (isset($message)): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="bulk_sections" class="form-label">Add Sections (Format: order|title|parent_id)</label>
                        <textarea name="bulk_sections" class="form-control" rows="8" placeholder="Example:&#10;1|Introduction|&#10;2|Chapter 1|&#10;3|Advanced|2" required></textarea>
                    </div>
                    <button type="submit" name="bulk_add_sections" class="btn btn-primary">Add Sections</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
        <div class="d-flex justify-content-end mb-3">
    <a href="?course_id=<?= $course_id ?>&delete_all_sections=1" 
       class="btn btn-outline-danger" 
       onclick="return confirm('Are you sure you want to delete ALL sections for this course?');">
       Delete All Sections
    </a>
</div>
            <div class="card-body">
                <h4 class="card-title">Current Course Sections</h4>
                <?php renderSections($conn, $course_id); ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="addcoursecontent.php?course_id=<?php echo $course_id; ?>" class="btn btn-success btn-lg">
                Proceed to Add Course Content
            </a>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
</main>
</body>
</html>
