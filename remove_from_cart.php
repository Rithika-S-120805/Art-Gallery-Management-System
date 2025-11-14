<?php
session_start();
include 'db.php'; // Ensure correct database connection

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Please log in first."]);
        exit;
    }

    if (!isset($_POST['artwork_id'])) {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $artwork_id = intval($_POST['artwork_id']); // Ensure it's an integer

    // Remove from cart
    $deleteQuery = "DELETE FROM cart WHERE user_id = ? AND artwork_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("ii", $user_id, $artwork_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Removed from cart!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to remove from cart."]);
    }

    $stmt->close();
    $conn->close();
}
?>
