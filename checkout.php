<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO orders (user_id, artwork_id, quantity)
                            SELECT user_id, artwork_id, quantity FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();
    $message = "Checkout successful!";
} catch (Exception $e) {
    $conn->rollback();
    $message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5 text-center">
    <h2>Checkout</h2>
    <p class="alert alert-success"><?= $message; ?></p>
    <a href="user_dashboard.php" class="btn btn-primary">Continue Shopping</a>
</div>
</body>
</html>
<?php $conn->close(); ?>
