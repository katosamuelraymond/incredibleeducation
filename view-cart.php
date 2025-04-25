<?php
session_start();
include('database.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to view your cart.");
}

$userId = $_SESSION['user_id'];
$query = "SELECT c.course_id, co.course_name, co.price, c.quantity 
          FROM cart c
          JOIN courses co ON c.course_id = co.course_id
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

?>
<?php include "dashboard includes/header.php"; ?>


<body>

<div class="container mt-5">
    <h2 class="mb-4">Your Cart</h2>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Course</th>
                    <th>Price (UGX)</th>
                    <th>Quantity</th>
                    <th>Total (UGX)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0;
                while ($row = $result->fetch_assoc()):
                    $rowTotal = $row['price'] * $row['quantity'];
                    $grandTotal += $rowTotal;
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= number_format($row['price']) ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= number_format($rowTotal) ?></td>
                    <td>
                        <a href="view-cart.php?remove=<?= $row['course_id'] ?>" 
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Are you sure you want to remove this course from your cart?');">
                            Remove
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                <tr class="table-secondary">
                    <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                    <td colspan="2"><strong>UGX <?= number_format($grandTotal) ?></strong></td>
                </tr>
            </tbody>
        </table>
        <a href="javascript:history.back()" class="btn btn-secondary ">← Back</a>
        <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
    <?php else: ?>
        <div class="alert alert-info">Your cart is empty. <a href="index.php">Browse courses</a></div>
       

    <?php endif; ?>
    
</div>
