<?php
include 'database.php'; // Include your database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $subtitle = mysqli_real_escape_string($conn, $_POST['subtitle']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = basename($_FILES['image']['name']);
        $imagePath = 'uploads/slider_images/' . $imageName;
        
        // Move the uploaded image to the target directory
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            // Insert the data into the carousel_slides table
            $insertQuery = "INSERT INTO carousel_slides (title, subtitle, image_path, status) 
                            VALUES ('$title', '$subtitle', '$imagePath', '$status')";
            
            if (mysqli_query($conn, $insertQuery)) {
                // Success message and redirect if needed
                echo "Carousel added successfully!";
                header("Location: admin.php"); // Redirect to manage carousel page
                exit();
            } else {
                // Error message if the query fails
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Error uploading image.";
        }
    } else {
        echo "Please select an image.";
    }
}
?>

<?php include "dashboard includes/header.php"; ?>
<?php include 'includes/navbar.php'; ?>
<?php include 'dashboard includes/aside.php'; ?>

<main class="main-content" id="mainContent">
  <button class="toggle-btn-outside" id="toggleSidebar"><i class="bi bi-x"></i></button>

  <div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold">Add Carousel</h2>
    </div>

    <!-- Form for adding carousel details -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
      <div class="card-body">
        <h4 class="fw-bold mb-3">Add Carousel Details</h4>
        <form action="" method="POST" enctype="multipart/form-data">
          <div class="mb-3">
            <label for="title" class="form-label">Carousel Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
          </div>
          <div class="mb-3">
            <label for="subtitle" class="form-label">Carousel Subtitle</label>
            <input type="text" class="form-control" id="subtitle" name="subtitle" required>
          </div>
          <div class="mb-3">
            <label for="image" class="form-label">Carousel Image</label>
            <input type="file" class="form-control" id="image" name="image" required>
          </div>
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Add Carousel</button>
        </form>
      </div>
    </div>
  </div>

  <?php include "includes/footer.php"; ?>
</main>
