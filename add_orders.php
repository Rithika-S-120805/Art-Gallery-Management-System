<?php
session_start();
include 'db.php'; // Ensure this contains connection details

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $artwork_id = $_POST['artwork_id'] ?? null;
    $artwork_title = $_POST['artwork_title'] ?? null;
    $price = str_replace(',', '', $_POST['artwork_price']); // Ensure numeric format
    $status = "Pending";

    if (!$artwork_id || !$artwork_title || !$price) {
        echo json_encode(["status" => "error", "message" => "Invalid data received."]);
        exit();
    }

    // Insert order into database
    $sql = "INSERT INTO orders (user_id, artwork_id, artwork_title, price, status) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "SQL Error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("iisss", $user_id, $artwork_id, $artwork_title, $price, $status);

    if ($stmt->execute()) {
        $order_total = number_format($price, 2);
        $commission = number_format($price * 0.2, 2);
        $take_home = number_format($price - ($price * 0.2), 2);

        // Return JSON if it's an AJAX request
        if (isset($_POST['ajax'])) {
            echo json_encode(["status" => "success", "message" => "Order placed successfully!", "redirect" => "orders.php"]);
            exit();
        }

        // Otherwise, show the confirmation page
    } else {
        echo json_encode(["status" => "error", "message" => "Error placing order: " . $stmt->error]);
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background-color: #f9f9f9;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: inline-block;
        }
        .success-icon {
            font-size: 80px;
            color: #28a745;
        }
        h2 {
            color: #28a745;
            margin-top: 10px;
        }
        p {
            color: #555;
        }
        .summary {
            text-align: left;
            margin-top: 20px;
        }
        .summary table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary th, .summary td {
            padding: 8px;
            text-align: left;
        }
        .summary th {
            font-weight: bold;
        }
        .btn {
            display: block;
            width: 80%;
            padding: 10px;
            margin: 10px auto;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            text-align: center;
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
            border: none;
        }
        .btn-secondary {
            border: 2px solid #28a745;
            color: #28a745;
            background: white;
        }
        .amount {
            font-weight: bold;
            color: #28a745;
            font-size: 24px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="success-icon">✅</div>
        <h2>Order Placed Successfully</h2>
        <p>Your order for <strong><?= htmlspecialchars($artwork_title) ?></strong> has been successfully placed!</p>

        <div class="summary">
            <h3>Order Summary</h3>
            <table>
                <tr>
                    <th>Artwork Title</th>
                    <td><?= htmlspecialchars($artwork_title) ?></td>
                </tr>
                <tr>
                    <th>Total Price</th>
                    <td>$<?= $order_total ?></td>
                </tr>
                <tr>
                    <th>Offer (20%)</th>
                    <td>-$<?= $commission ?></td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td class="amount">$<?= $take_home ?></td>
                </tr>
            </table>
        </div>

        <a href="orders.php" class="btn btn-primary">View Orders</a>
        <a href="user_dashboard.php" class="btn btn-secondary">Continue Shopping</a>
    </div>

</body>
</html>
