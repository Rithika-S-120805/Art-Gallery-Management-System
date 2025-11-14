<?php
session_start();
require_once 'db.php';

// Ensure only artists can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'artist') {
    header("Location: login.php");
    exit();
}

$artist_id = $_SESSION['user_id'];

// Get the artwork ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_art.php"); // Redirect to artist's manage artworks page
    exit();
}

$artwork_id = $_GET['id'];
$success_msg = "";
$error_msg = "";

// Fetch the artwork but only if it belongs to the logged-in artist
$stmt = $conn->prepare("SELECT * FROM artworks WHERE artwork_id = ? AND artist_id = ?");
$stmt->bind_param("ii", $artwork_id, $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artwork = $result->fetch_assoc();

if (!$artwork) {
    header("Location: manage_art.php"); // Redirect if unauthorized
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = trim($_POST['title']);
    $type = trim($_POST['type']);
    $bio = trim($_POST['bio']);
    $price = trim($_POST['price']);

    // Handle image upload
    $image = $artwork['artwork_image']; // Keep existing image by default
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "image/Artworks/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    // Update artwork
    $update_stmt = $conn->prepare("UPDATE artworks SET artwork_title = ?, artwork_type = ?, artwork_bio = ?, artwork_price = ?, artwork_image = ? WHERE artwork_id = ? AND artist_id = ?");
    $update_stmt->bind_param("ssssssi", $title, $type, $bio, $price, $image, $artwork_id, $artist_id);

    if ($update_stmt->execute()) {
        $success_msg = "Artwork updated successfully!";
    } else {
        $error_msg = "Error updating artwork.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Artwork</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Artwork</h2>
    <hr>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title:</label>
            <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($artwork['artwork_title']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type:</label>
            <input type="text" name="type" id="type" class="form-control" value="<?= htmlspecialchars($artwork['artwork_type']) ?>">
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Description:</label>
            <textarea name="bio" id="bio" class="form-control" rows="4"><?= htmlspecialchars($artwork['artwork_bio']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price:</label>
            <input type="text" name="price" id="price" class="form-control" value="<?= htmlspecialchars($artwork['artwork_price']) ?>">
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Artwork Image:</label>
            <input type="file" name="image" id="image" class="form-control">
            <?php if (!empty($artwork['artwork_image'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($artwork['artwork_image']) ?>" alt="Artwork Image" width="100">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="manage_art.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
