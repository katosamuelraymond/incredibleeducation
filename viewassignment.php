<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['assignment_id'] ?? null;

if (!$assignment_id) {
    echo "Invalid assignment.";
    exit();
}

$query = "
    SELECT s.*, u.firstname, u.lastname 
    FROM assignment_submissions s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.assignment_id = $assignment_id
";
$result = mysqli_query($conn, $query);
?>

<?php include 'dashboard includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>


<body>


<?php include 'dashboard includes/aside.php'; ?>


<main class="main-content" id="mainContent">
<button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
    <h3>Submissions for Assignment <?= $assignment_id ?></h3>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted On</th>
                    <th>Answer/Link</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($submission = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $submission['firstname'] . " " . $submission['lastname'] ?></td>
                        <td><?= $submission['submitted_at'] ?></td>
                        <td>
                            <?php if ($submission['file_path']): ?>
                                <a href="<?= $submission['file_path'] ?>" target="_blank">Download</a>
                            <?php elseif ($submission['answer_text']): ?>
                                <?= nl2br(htmlspecialchars($submission['answer_text'])) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= $submission['status'] ?? 'Pending' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No submissions yet.</p>
    <?php endif; ?>

    <?php include 'includes/footer.php';?>
</main>
<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$assignment_id = $_GET['assignment_id'] ?? null;

if (!$assignment_id) {
    echo "Invalid assignment.";
    exit();
}

$query = "
    SELECT s.*, u.firstname, u.lastname 
    FROM assignment_submissions s
    JOIN users u ON s.user_id = u.user_id
    WHERE s.assignment_id = $assignment_id
";
$result = mysqli_query($conn, $query);
?>

<?php include 'dashboard includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>


<body>


<?php include 'dashboard includes/aside.php'; ?>


<main class="main-content" id="mainContent">
<button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>
    <h3>Submissions for Assignment <?= $assignment_id ?></h3>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Submitted On</th>
                    <th>Answer/Link</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($submission = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $submission['firstname'] . " " . $submission['lastname'] ?></td>
                        <td><?= $submission['submitted_at'] ?></td>
                        <td>
                            <?php if ($submission['file_path']): ?>
                                <a href="<?= $submission['file_path'] ?>" target="_blank">Download</a>
                            <?php elseif ($submission['answer_text']): ?>
                                <?= nl2br(htmlspecialchars($submission['answer_text'])) ?>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                        <td><?= $submission['status'] ?? 'Pending' ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted">No submissions yet.</p>
    <?php endif; ?>

    <?php include 'includes/footer.php';?>
</main>
