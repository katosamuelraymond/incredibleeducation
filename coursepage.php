<?php
session_start();
if (!isset($_SESSION['user_id']) ) {
    header("Location: login.php");
    exit();
}
include 'database.php';

// Get course_id
$course_id = $_GET['course_id'] ?? null;
if (!$course_id) {
    die("No course selected.");
}

// Fetch course details
$courseQuery = "SELECT * FROM courses WHERE course_id = $course_id";
$courseResult = mysqli_query($conn, $courseQuery);
$course = mysqli_fetch_assoc($courseResult);
if (!$course) {
    die("Course not found.");
}

// Fetch selected lesson if available
$selected_lesson_id = $_GET['lesson_id'] ?? null;
$selectedLesson = null;
if ($selected_lesson_id) {
    $lessonQuery = "SELECT * FROM lessons WHERE lesson_id = $selected_lesson_id AND course_id = $course_id";
    $lessonResult = mysqli_query($conn, $lessonQuery);
    if ($lessonResult && mysqli_num_rows($lessonResult) > 0) {
        $selectedLesson = mysqli_fetch_assoc($lessonResult);
    }
}

?>

<?php include "includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>

<main class="container mt-5">
    <div class="row">
        <!-- Left Column: Course Details or Lesson Content -->
        <div class="col-md-8">
            <?php if ($selectedLesson): ?>
                <!-- Show Selected Lesson -->
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h4><?php echo htmlspecialchars($selectedLesson['lesson_title']); ?></h4>
                    </div>
                    <div class="card-body">
                        <?php
                        $lesson_id = $selectedLesson['lesson_id'];
                        $materialQuery = "SELECT * FROM lesson_materials WHERE lesson_id = $lesson_id";
                        $materialResult = mysqli_query($conn, $materialQuery);
                        if ($materialResult && mysqli_num_rows($materialResult) > 0) {
                            while ($material = mysqli_fetch_assoc($materialResult)) {
                                if (!empty($material['video_link'])) {
                                    echo "<div class='mb-3'>
                                            <iframe width='100%' height='315' src='" . $material['video_link'] . "' frameborder='0' allowfullscreen></iframe>
                                          </div>";
                                }

                                if (!empty($material['content_html'])) {
                                    echo "<div class='mb-3'>" . $material['content_html'] . "</div>";
                                }
                                if (!empty($material['video_file'])) {
                                    echo "<div class='mb-3'>
                                            <video controls   src='".$material['video_file']."' style='width:100%; height:75vh;'></video>
                                        </div>";
                                }

                                if (!empty($material['download_file'])) {
                                    echo "<div>
                                            <a href='" . $material['download_file'] . "' class='btn btn-success' download>Download File</a>
                                          </div>";
                                }
                            }
                        } 
                        else {
                            echo "<p>No lesson materials available for this lesson.</p>";
                        }
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Show Course Overview -->
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h2><?php echo htmlspecialchars($course['course_name']); ?></h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($course['category_id']); ?></p>
                                <p><strong>Start Date:</strong> <?php echo $course['course_start_date']; ?></p>
                                <p><strong>End Date:</strong> <?php echo $course['course_end_date']; ?></p>
                            </div>
                            <div class="col-md-6">
                                <img src="<?php echo $course['course_image']; ?>" class="img-fluid rounded" alt="Course Image" style="height:200px; width:90%">
                            </div>
                        </div>
                        <p class="mt-3"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($course['course_description'])); ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Table of Contents Grouped by Section -->
       
        <div class="col-md-4">
            <div class="card mb-4" style="position: sticky; top: 90px; z-index: 1;">
                <div class="card-header bg-secondary text-white">
                    <h4 class="mb-0">Table of Contents</h4>
                </div>
                <div class="card-body p-0" style="max-height: 75vh; overflow-y: auto;">

                    <div class="accordion" id="courseAccordion">
                        <?php
                        $sectionQuery = "SELECT * FROM course_sections WHERE course_id = $course_id ORDER BY id ASC";
                        $sectionResult = mysqli_query($conn, $sectionQuery);
                        $accordionIndex = 0;
                        $sectionNumber = 1;

                        if ($sectionResult && mysqli_num_rows($sectionResult) > 0) {
                            while ($section = mysqli_fetch_assoc($sectionResult)) {
                                $section_id = $section['id'];
                                $section_title = htmlspecialchars($section['section_title']);
                                $accordionId = "section_" . $accordionIndex;
                                $isOpen = false;

                                // Check if this section is related to selected lesson
                                $lessonCheckQuery = "SELECT lesson_id FROM lessons WHERE section_id = $section_id";
                                $lessonCheckResult = mysqli_query($conn, $lessonCheckQuery);
                                if ($lessonCheckResult) {
                                    while ($lessonCheck = mysqli_fetch_assoc($lessonCheckResult)) {
                                        if (isset($selected_lesson_id) && $selected_lesson_id == $lessonCheck['lesson_id']) {
                                            $isOpen = true;
                                            break;
                                        }
                                    }
                                }

                                $showClass = $isOpen ? 'show' : '';
                                $buttonClass = $isOpen ? 'bg-white text-dark' : '';
                                ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $accordionId ?>">
                                        <button class="accordion-button collapsed <?= $buttonClass ?>" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#collapse<?= $accordionId ?>"
                                                aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                                                aria-controls="collapse<?= $accordionId ?>"
                                                style="background-color: <?= $isOpen ? '#f8f9fa' : '#6c757d' ?>; color: <?= $isOpen ? '#000' : '#fff' ?>;">
                                            <?= "$sectionNumber. $section_title" ?>
                                        </button>
                                    </h2>
                                    <div id="collapse<?= $accordionId ?>"
                                        class="accordion-collapse collapse <?= $showClass ?>"
                                        aria-labelledby="heading<?= $accordionId ?>">
                                        <div class="accordion-body p-0">
                                            <ul class="list-group list-group-flush">
                                                <?php
                                                $itemNumber = 1;

                                                // Fetch lessons
                                                $lessonQuery = "SELECT * FROM lessons WHERE course_id = $course_id AND section_id = $section_id ORDER BY lesson_date ASC";
                                                $lessonResult = mysqli_query($conn, $lessonQuery);
                                                if ($lessonResult && mysqli_num_rows($lessonResult) > 0) {
                                                    while ($lesson = mysqli_fetch_assoc($lessonResult)) {
                                                        $isActive = (isset($selected_lesson_id) && $selected_lesson_id == $lesson['lesson_id']);
                                                        $activeClass = $isActive ? 'active' : '';
                                                        $lessonBg = $isActive ? '#d1e7dd' : '#f8f9fa';

                                                        echo "<li class='list-group-item $activeClass' style='background-color: $lessonBg;'>
                                                                <a href='coursepage.php?course_id=$course_id&lesson_id={$lesson['lesson_id']}'
                                                                class='text-decoration-none d-block text-dark'>
                                                                {$sectionNumber}.{$itemNumber} " . htmlspecialchars($lesson['lesson_title']) . "
                                                                </a>
                                                            </li>";
                                                        $itemNumber++;
                                                    }
                                                }

                                                // Fetch assignments under the section
                                                $assignmentQuery = "SELECT * FROM assignments WHERE course_id = $course_id AND section_id = $section_id ORDER BY due_date ASC";
                                                $assignmentResult = mysqli_query($conn, $assignmentQuery);
                                                if ($assignmentResult && mysqli_num_rows($assignmentResult) > 0) {
                                                    while ($assignment = mysqli_fetch_assoc($assignmentResult)) {
                                                        $assignmentTitle = htmlspecialchars($assignment['assignment_title']);
                                                        $dueDate = date("M d, Y", strtotime($assignment['due_date']));
                                                        $assignmentID = $assignment['assignment_id'];

                                                        echo "<li class='list-group-item' style='background-color: #fffaf0;'>
                                                                <a href='view_assignment.php?assignment_id=$assignmentID'
                                                                class='text-decoration-none d-block text-danger'>
                                                                {$sectionNumber}.{$itemNumber} 📄 $assignmentTitle
                                                                <br><small class='text-muted'>Due: $dueDate</small>
                                                                </a>
                                                            </li>";
                                                        $itemNumber++;
                                                    }
                                                }

                                                if ($itemNumber == 1) {
                                                    echo "<li class='list-group-item text-muted'>No lessons or assignments.</li>";
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $accordionIndex++;
                                $sectionNumber++;
                            }
                        } else {
                            echo "<p class='p-3'>No sections found for this course.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        
    </div>


</main>

<?php include "includes/footer.php"; ?>
