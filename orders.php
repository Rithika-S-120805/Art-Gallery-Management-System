<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch orders
$sql = "SELECT orders.order_id, orders.artwork_id, artworks.artwork_title, artworks.artwork_image, 
               orders.order_date, orders.status, orders.price 
        FROM orders
        JOIN artworks ON orders.artwork_id = artworks.artwork_id
        WHERE orders.user_id = ?";
        
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle order removal
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_order_id'])) {
    $remove_order_id = intval($_POST['remove_order_id']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $remove_order_id, $user_id);
    $stmt->execute();
    header("Location: orders.php"); // Refresh the page
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card-img-top {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .btn-remove {
            background: red;
            color: white;
            border: none;
        }
        .btn-remove:hover {
            background: darkred;
        }
        .row {
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">My Orders</h2>
    
    <a href="user_dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <?php if ($result->num_rows > 0) { ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($row['artwork_image']); ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['artwork_title']); ?></h5>
                            <p class="card-text">Price: $<?= number_format($row['price'], 2); ?></p>
                            <p class="card-text"><strong>Status:</strong> <?= htmlspecialchars($row['status']); ?></p>
                            <p class="card-text"><strong>Order Date:</strong> <?= htmlspecialchars($row['order_date']); ?></p>

                            <!-- Remove Order Form -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="remove_order_id" value="<?= $row['order_id']; ?>">
                                <button type="submit" class="btn btn-remove">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <!-- No Orders Message -->
        <div class="text-center mt-5">
            
            <h4>No Orders Yet</h4>
            <p>You haven't placed any orders. Browse the gallery to find artworks you'll love!</p>
            <a href="user_dashboard.php" class="btn btn-primary">Go to Shop</a>
        </div>
    <?php } ?>
</div>


</body>
</html>

<?php $conn->close(); ?>
