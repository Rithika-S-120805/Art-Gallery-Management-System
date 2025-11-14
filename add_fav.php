<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "error", "message" => "Login required"]);
    exit();
}

if (!isset($_POST['artwork_id']) || empty($_POST['artwork_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
    exit();
}

$user_id = $_SESSION['user_id'];
$artwork_id = intval($_POST['artwork_id']);

// Check if already in favourites
$check = $conn->prepare("SELECT * FROM favourites WHERE user_id = ? AND artwork_id = ?");
$check->bind_param("ii", $user_id, $artwork_id);
$check->execute();
$check_result = $check->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Already in favourites"]);
    exit();
}

// Insert into favourites
$stmt = $conn->prepare("INSERT INTO favourites (user_id, artwork_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $artwork_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Added to favourites"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error"]);
}

$stmt->close();
$conn->close();
?>
