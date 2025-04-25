<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['id'] ?? null;

if (!$assignment_id) {
    echo "Invalid assignment ID.";
    exit();
}

// Fetch the existing assignment
$query = "SELECT * FROM assignments WHERE assignment_id = $assignment_id";
$result = mysqli_query($conn, $query);
$assignment = mysqli_fetch_assoc($result);

if (!$assignment) {
    echo "Assignment not found.";
    exit();
}

// Update on POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['assignment_title'];
    $description = $_POST['assignment_description'];
    $due_date = $_POST['due_date'];
    $type = $_POST['assignment_type'];

    $update_query = "
        UPDATE assignments SET 
            assignment_title = '$title',
            assignment_description = '$description',
            due_date = '$due_date',
            assignment_type = '$type'
        WHERE assignment_id = $assignment_id
    ";

    if (mysqli_query($conn, $update_query)) {
        header("Location: instructorresources.php");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<?php include 'dashboard includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<body>


<?php include 'dashboard includes/aside.php'; ?>


<main class="main-content" id="mainContent">
<button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
    <h3>Edit Assignment</h3>

    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" class="form-control" name="assignment_title" value="<?= $assignment['assignment_title'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="assignment_description" class="form-control" rows="3" required><?= $assignment['assignment_description'] ?></textarea>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="assignment_type" class="form-control" required>
                <option value="objective" <?= $assignment['assignment_type'] == 'objective' ? 'selected' : '' ?>>Objective</option>
                <option value="paper" <?= $assignment['assignment_type'] == 'paper' ? 'selected' : '' ?>>Online Paper</option>
                <option value="exercise" <?= $assignment['assignment_type'] == 'exercise' ? 'selected' : '' ?>>Exercise</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" class="form-control" name="due_date" value="<?= $assignment['due_date'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Assignment</button>
    </form>
    <?php include "includes/footer.php"?>
</main>
<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['id'] ?? null;

if (!$assignment_id) {
    echo "Invalid assignment ID.";
    exit();
}

// Fetch the existing assignment
$query = "SELECT * FROM assignments WHERE assignment_id = $assignment_id";
$result = mysqli_query($conn, $query);
$assignment = mysqli_fetch_assoc($result);

if (!$assignment) {
    echo "Assignment not found.";
    exit();
}

// Update on POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['assignment_title'];
    $description = $_POST['assignment_description'];
    $due_date = $_POST['due_date'];
    $type = $_POST['assignment_type'];

    $update_query = "
        UPDATE assignments SET 
            assignment_title = '$title',
            assignment_description = '$description',
            due_date = '$due_date',
            assignment_type = '$type'
        WHERE assignment_id = $assignment_id
    ";

    if (mysqli_query($conn, $update_query)) {
        header("Location: instructorresources.php");
        exit();
    } else {
        $error = "Update failed: " . mysqli_error($conn);
    }
}
?>

<?php include 'dashboard includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<body>


<?php include 'dashboard includes/aside.php'; ?>


<main class="main-content" id="mainContent">
<button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
    <h3>Edit Assignment</h3>

    <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Title</label>
            <input type="text" class="form-control" name="assignment_title" value="<?= $assignment['assignment_title'] ?>" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="assignment_description" class="form-control" rows="3" required><?= $assignment['assignment_description'] ?></textarea>
        </div>
        <div class="mb-3">
            <label>Type</label>
            <select name="assignment_type" class="form-control" required>
                <option value="objective" <?= $assignment['assignment_type'] == 'objective' ? 'selected' : '' ?>>Objective</option>
                <option value="paper" <?= $assignment['assignment_type'] == 'paper' ? 'selected' : '' ?>>Online Paper</option>
                <option value="exercise" <?= $assignment['assignment_type'] == 'exercise' ? 'selected' : '' ?>>Exercise</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Due Date</label>
            <input type="date" class="form-control" name="due_date" value="<?= $assignment['due_date'] ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Assignment</button>
    </form>
    <?php include "includes/footer.php"?>
</main>
