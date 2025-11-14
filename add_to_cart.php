<?php
session_start();
include 'db.php'; // Ensure this contains the correct connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Please log in first."]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $artwork_id = $_POST['artwork_id'];

    // Check if the artwork is already in the cart
    $checkQuery = "SELECT * FROM cart WHERE user_id = ? AND artwork_id = ?";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param("ii", $user_id, $artwork_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Item already in cart."]);
    } else {
        // Insert into the cart
        $insertQuery = "INSERT INTO cart (user_id, artwork_id, quantity) VALUES (?, ?, 1)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ii", $user_id, $artwork_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Added to cart!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to add to cart."]);
        }
    }
    $stmt->close();
    $conn->close();
}
?>
