<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
    <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">Frequently Asked Questions (FAQs)</h2>
        </div>

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">
                <h4 class="card-title text-primary">General Questions</h4>
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                What is the purpose of this e-learning platform?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Our platform is designed to provide accessible and interactive learning resources for students in Uganda, offering a variety of courses across different levels (O-Level, A-Level, and university programs).
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                How do I register for a course?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To register, simply log in to your account, browse the available courses, and click on the 'Enroll' button next to your chosen course. After enrollment, you will have access to course materials.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Can I access the course materials after the course ends?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Yes, you can access the course materials anytime even after the course ends, as long as your account is active on the platform.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                How do I become an instructor on the platform?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                To become an instructor, you must apply through the 'Become an Instructor' page on the platform. After your application is reviewed and approved, you will gain access to the instructor dashboard where you can add and manage your courses.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Can I get a refund if I am not satisfied with a course?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Refund policies vary depending on the course provider. Please refer to the specific course's terms and conditions for more details regarding refunds.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php include "includes/footer.php"; ?>

</main>

