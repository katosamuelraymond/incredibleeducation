




<?php include 'database.php'; ?>

<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>




<!-- <div class="container mt-3"> -->


    <div class="container mt-3">
        <div id="learningCarousel" class="carousel slide" data-bs-ride="carousel">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <?php
                $slideQuery = "SELECT * FROM carousel_slides WHERE status = 'active' ORDER BY id ASC";
                $slideResult = mysqli_query($conn, $slideQuery);
                $totalSlides = mysqli_num_rows($slideResult);
                for ($i = 0; $i < $totalSlides; $i++): ?>
                    <button type="button" data-bs-target="#learningCarousel" data-bs-slide-to="<?= $i ?>" class="<?= $i === 0 ? 'active' : '' ?>" aria-label="Slide <?= $i + 1 ?>"></button>
                <?php endfor; ?>
            </div>

            <!-- Carousel Slides -->
            <div class="carousel-inner">
                <?php
                mysqli_data_seek($slideResult, 0); // reset pointer
                $index = 0;
                while ($slide = mysqli_fetch_assoc($slideResult)):
                ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <img src="<?= htmlspecialchars($slide['image_path']) ?>" class="d-block w-100" style="height:70vh ;object-fit: fit; width:100%;" alt="Slide <?= $index + 1 ?>">
                        <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 p-4 rounded">
                            <h2 class="text-white"><?= htmlspecialchars($slide['title']) ?></h2>
                            <p class="text-light"><?= htmlspecialchars($slide['subtitle']) ?></p>
                        </div>
                    </div>
                <?php $index++; endwhile; ?>
            </div>

            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#learningCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#learningCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
</div>













<div class="container-fluid mb-4 bg-light ">
    <div class="row">
        <div class="col-12 col-md-6 mt-5 ml-md-5 p-5  ">

            <a href="interactive-learning.php" class="text-decoration-none text-dark">
                <div class="card card-hover mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Interactive Learning Experience</h5>
                        <p class="card-text">Master concepts through hands-on projects, interactive quizzes, and real-time feedback powered by smart technology.</p>
                    </div>
                </div>
            </a>

            <a href="certification-prep.php" class="text-decoration-none text-dark">
                <div class="card card-hover mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Career-Boosting Certifications</h5>
                        <p class="card-text">Prepare for globally recognized exams with practical simulations and earn certificates that boost your CV and skills profile.</p>
                    </div>
                </div>
            </a>

            <a href="analytics.php" class="text-decoration-none text-dark">
                <div class="card card-hover mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            Progress Tracking & Analytics
                            <span class="badge bg-primary">Enterprise Plan</span>
                        </h5>
                        <p class="card-text">Monitor learning outcomes with intelligent dashboards—ideal for schools, businesses, and education partners.</p>
                    </div>
                </div>
            </a>

            <a href="custom-learning-paths.php" class="text-decoration-none text-dark">
                <div class="card card-hover mb-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            Personalized Learning Paths
                            <span class="badge bg-primary">Enterprise Plan</span>
                        </h5>
                        <p class="card-text">Design unique journeys for your learners by customizing modules, adding your own content, and aligning with your curriculum.</p>
                    </div>
                </div>
            </a>

        </div>




        <div class="col-12 col-md-6 mt-5 ml-md-5 p-5  ">
        

        </div>
    </div>
   
    
</div>





<div class="container mt-5">
  <div class="row g-4">
    <?php
    $query = "SELECT * FROM courses ORDER BY course_id DESC LIMIT 8";
    $result = mysqli_query($conn, $query);

    while ($course = mysqli_fetch_assoc($result)):
    ?>
    <!-- Card starts here -->
    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
      <div class="card shadow-sm rounded-4 overflow-hidden">
        <div class="row g-0">
          <!-- Course Image -->
          <div class="col-12">
            <img src="<?= htmlspecialchars($course['course_image']); ?>" alt="Course Image" class="img-fluid rounded-top object-fit-cover" style="height: 200px; width: 100%; object-fit: cover;">
          </div>
          <!-- Course Details -->
          <div class="col-12 p-3">
            <h5 class="card-title"><?= htmlspecialchars($course['course_name']); ?></h5>
            <p class="card-text text-muted small mb-3">
              <?php
              // Preview of description with a max of 29 characters
              $descPreview = substr($course['course_description'], 0, 29);
              echo htmlspecialchars($descPreview);

             
              ?>
                
              
            </p>

            <p class="fw-bold text-dark mb-1">UGX <?= htmlspecialchars($course['price']); ?></p>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
  <p class="text-muted mb-0 me-2">
    <del>UGX <?= htmlspecialchars($course['original_price']); ?></del>
  </p>
  <?php if ($course['original_price'] > $course['price']): ?>
    <span class="badge bg-success">
      <?= round(100 - ($course['price'] / $course['original_price']) * 100) ?>% Off
    </span>
    <?php else: ?>
        <span class="badge bg-secondary">
      0% Off
    </span>
  <?php endif; ?>
    </div>
          </div>
        </div>
        <!-- Card Footer with Buttons -->
        <div class="card-footer bg-white text-center border-0 pt-0 pb-3">
          <a href="course-details.php?id=<?= $course['course_id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold" data-bs-toggle="modal" data-bs-target="#courseModal-<?= $course['course_id']; ?>">View Details</a>
          <a href="enrollme.php?course_id=<?= $course['course_id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill fw-semibold">Enroll Me</a>

        </div>
      </div>
    </div>
    <!-- Card ends here -->

        <!-- Modal for Full Course Description -->
<!-- Modal for Full Course Description -->
<div class="modal fade" id="courseModal-<?= $course['course_id']; ?>" tabindex="-1" aria-labelledby="courseModalLabel-<?= $course['course_id']; ?>" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title " id="courseModalLabel-<?= $course['course_id']; ?>"><?= htmlspecialchars($course['course_name']); ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><small class=" fs-4 badge text-dark">category name:</small> <?= htmlspecialchars($course['category_name']); ?></p>
        <p><small class=" fs-4 text-primary badge">Price:</small> <?= htmlspecialchars($course['price']); ?></p>
        <p><?= htmlspecialchars($course['course_description']); ?></p>


      </div>
      
      <div class="modal-footer">
        <?php if (isset($_SESSION['user_id'])): ?>
          <!-- Add to Cart Button for Logged-in Users -->
          <form method="POST" action="add-to-cart.php" class="d-inline">
            <input type="hidden" name="course_id" value="<?= $course['course_id']; ?>">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">
            <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill fw-semibold">Add to Cart</button>
          </form>
        <?php else: ?>
          <!-- Login Prompt Message for Unauthenticated Users -->
          <p class="text-danger mb-0">You need to be logged in to add this course to your cart.</p>
          <a href="login.php" class="btn btn-outline-primary d-block btn-sm rounded-pill">Login</a>
        <?php endif; ?>
        <!-- Enroll Now Button -->


        <a href="coursepage.php?course_id=<?php echo $course['course_id']; ?>" class="btn btn-outline-primary btn-sm">View Course</a>

        <a href="enroll.php?id=<?= $course['course_id']; ?>" class="btn btn-outline-success btn-sm rounded-pill fw-semibold">Enroll Now</a>
      </div>
    </div>
  </div>
</div>




    <?php endwhile; ?>
  </div>
</div>










<section id="courses-category" class="courses-category section">


<div class="container section-title" data-aos="fade-up">
    <div class="section-title-container d-flex align-items-center justify-content-between">
        <h2>O & A Level Courses</h2>
        <p><a href="courses.php">See All Courses</a></p>
    </div>
</div>








<div class="container-fluid" style="background-color:#efebeb;">
    <div class="container py-5 my-5 " >
        <h2 class="text-center fw-bold mb-4">See what others are achieving through Incredible Learning</h2>

        <div id="multiCardCarousel" class="carousel slide position-relative" data-bs-ride="carousel">
            <div class="carousel-inner">

        
            <div class="carousel-item active">
                <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        I improved my science grades at O-Level thanks to the clear and engaging video lessons.
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-4">Explore O-Level Courses</a>
                    </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        This platform helped me understand complex A-Level topics easily. Now I’m confident in every exam!
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-2">Browse A-Level Subjects</a>
                    </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        I joined the university coding program and built my first website within two weeks!
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-4">Start Coding Today</a>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            
            <div class="carousel-item">
                <div class="row g-3">
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        Thanks to this platform, I now solve A-Level math questions with confidence and speed.
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-4">Check Out A-Level Math</a>
                    </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        The Python course on this platform is just like having a personal mentor. Very practical and fun!
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-4">Explore Python Programs</a>
                    </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <p class="fs-6 text-muted">
                        <span class="fs-3 fw-bold">&ldquo;</span>
                        From zero coding skills to building apps — all thanks to Incredible E-Learning!
                        <span class="fs-3 fw-bold">&rdquo;</span>
                        </p>
                        <a href="#" class="btn btn-primary btn-sm mt-4">View All Coding Tracks</a>
                    </div>
                    </div>
                </div>
                </div>
            </div>

            </div>

            
            <button class="carousel-control-prev position-absolute top-50 start-0 translate-middle-y" type="button" data-bs-target="#multiCardCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
            </button>

            <button class="carousel-control-next position-absolute top-50 end-0 translate-middle-y" type="button" data-bs-target="#multiCardCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon bg-dark rounded-circle p-2" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
            </button>
        </div>
    </div>

</div>







<?php
        $query = "SELECT * FROM courses ORDER BY course_id DESC LIMIT 10";
        $course_result = mysqli_query($conn, $query);

       
        ?>

<?php
$colors = ['bg-primary', 'bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-secondary', 'bg-dark'];
$index = 0;
?>
<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold">Courses</h4>
    <a href="courses.php" class="btn btn-outline-dark btn-sm rounded-pill">View All</a>
  </div>

  <div class="row g-3">
    <?php while ($course = mysqli_fetch_assoc($course_result)): ?>
      <?php
        // Get color and move to next
        $bg = $colors[$index % count($colors)];
        $index++;
      ?>
      <div class="col-6 col-md-3 my-4">
        <div class="<?php echo $bg; ?> text-white fw-semibold rounded-3 p-3 d-flex justify-content-between align-items-center" style="height: 100%;">
          <span class="text-truncate" style="max-width: 80%;"><?php echo htmlspecialchars($course['course_name']); ?></span>
          <span class="fs-3 text-white "> <a class="text-light" href="coursepage.php?course_id=<?php echo $course['course_id']; ?>" style="text-decoration:none"> &rarr; </a></span>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>


<div class="container mt-3" data-aos="fade-up" data-aos-delay="100">

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="course-list lg">
                <!-- <a href="course-details"><img src="\assets\img\pexels-emily-ranquist-493228-1205651.jpg" alt="" class="img-fluid"></a> -->
                <div class="course-meta"><span class="category">O Level</span> <span class="mx-1">•</span>
                    <span>Updated 2025</span>
                </div>
                <h2><a href="course-details">Mathematics for O Level: Master the Fundamentals</a></h2>
                <p class="mb-4 d-block">Comprehensive guide to O Level Mathematics with step-by-step solutions.</p>

                <div class="d-flex align-items-center instructor">
                    <div class="photo">
                    <!-- <img src="/assets/img/pexels-emily-ranquist-493228-1205651.jpg" alt="" class="img-fluid"> -->
                    </div>
                    <!-- <div class="name">
                        <h3 class="m-0 p-0">Dr. Jane Doe</h3>
                    </div> -->
                </div>
            </div>

            <div class="course-list border-bottom">
                <div class="course-meta"><span class="category">A Level</span> <span class="mx-1">•</span>
                    <span>Updated 2025</span>
                </div>
                <h2 class="mb-2"><a href="course-details">Advanced Physics for A Level</a></h2>
                <span class="instructor mb-3 d-block">Prof. John Smith</span>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-5">
                <div class="col-lg-4 border-start custom-border">
                    <div class="course-list">
                        <!-- <a href="course-details"><img src="/assets/img/course-html.jpg" alt="" class="img-fluid"></a> -->
                        <div class="course-meta"><span class="category">Web Development</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2><a href="course-details">HTML & CSS: Beginner to Advanced</a></h2>
                    </div>
                    <div class="course-list">
                        <a href="course-details"><img src="/assets/img/course-js.jpg" alt="" class="img-fluid"></a>
                        <div class="course-meta"><span class="category">Web Development</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2><a href="course-details">JavaScript Essentials for Beginners</a></h2>
                    </div>
                </div>
                <div class="col-lg-4 border-start custom-border">
                    <div class="course-list">
                        <!-- <a href="course-details"><img src="/assets/img/course-php.jpg" alt="" class="img-fluid"></a> -->
                        <div class="course-meta"><span class="category">Backend Development</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2><a href="course-details">PHP & MySQL: Dynamic Websites</a></h2>
                    </div>
                    <div class="course-list">
                        <a href="course-details"><img src="/assets/img/course-excel.jpg" alt="" class="img-fluid"></a>
                        <div class="course-meta"><span class="category">Data Analysis</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2><a href="course-details">Mastering Excel: From Basics to Advanced</a></h2>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="course-list border-bottom">
                        <div class="course-meta"><span class="category">O Level</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2 class="mb-2"><a href="course-details">Chemistry for O Level</a></h2>
                        <span class="instructor mb-3 d-block">Dr. Emily Brown</span>
                    </div>
                    <div class="course-list border-bottom">
                        <div class="course-meta"><span class="category">A Level</span> <span class="mx-1">•</span> <span>Updated 2025</span></div>
                        <h2 class="mb-2"><a href="course-details">Economics for A Level: Core Principles</a></h2>
                        <span class="instructor mb-3 d-block">Prof. Mark Wilson</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>








<?php  include "includes/footer.php"; ?>




