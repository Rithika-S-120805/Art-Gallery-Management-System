<?php
session_start();
require_once 'db.php';

// Fetch all orders
$orders = $conn->query("SELECT order_id, user_id, artwork_title, price, order_date, status FROM orders");

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);

    $conn->query("UPDATE orders SET status = '$status' WHERE order_id = $order_id");
    header("Location: admin_orders.php");
    exit();
}

// Handle order removal
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['remove_order_id'])) {
    $remove_order_id = intval($_POST['remove_order_id']);
    $conn->query("DELETE FROM orders WHERE order_id = $remove_order_id");
    header("Location: admin_orders.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Manage Orders</h2>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a> 
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>User ID</th>
                <th>Artwork Title</th>
                <th>Price ($)</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Update Status</th>
                <th>Remove Order</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $orders->fetch_assoc()) { ?>
                <tr>
                    <td><?= $row['order_id'] ?></td>
                    <td><?= $row['user_id'] ?></td>
                    <td><?= $row['artwork_title'] ?></td>
                    <td><?= number_format($row['price'], 2) ?></td>
                    <td><?= $row['order_date'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                            <select name="status" class="form-select" required>
                                <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Shipped" <?= $row['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="Delivered" <?= $row['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="Cancelled" <?= $row['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary mt-2">Update</button>
                        </form>
                    </td>
                    <td>
                        <form method="POST" onsubmit="return confirm('Are you sure you want to remove this order?');">
                            <input type="hidden" name="remove_order_id" value="<?= $row['order_id'] ?>">
                            <button type="submit" class="btn btn-danger">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>
</body>
</html>
