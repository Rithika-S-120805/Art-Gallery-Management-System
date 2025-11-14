<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_artists.php");
    exit();
}

$artist_id = $_GET['id'];
$success_msg = "";
$error_msg = "";

// Fetch artist details
$stmt = $conn->prepare("SELECT * FROM artists WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$result = $stmt->get_result();
$artist = $result->fetch_assoc();

if (!$artist) {
    header("Location: manage_artists.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);

    // Handle image upload
    $image = $artist['artist_image']; // Keep old image by default
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "image/Artists/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
    }

    // Update artist
    $update_stmt = $conn->prepare("UPDATE artists SET artist_name = ?, artist_email = ?, artist_bio = ?, artist_image = ? WHERE artist_id = ?");
    $update_stmt->bind_param("ssssi", $name, $email, $bio, $image, $artist_id);

    if ($update_stmt->execute()) {
        $success_msg = "Artist updated successfully!";
    } else {
        $error_msg = "Error updating artist.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Artist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Edit Artist</h2>
    <hr>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($artist['artist_name']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($artist['artist_email']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio:</label>
            <textarea name="bio" id="bio" class="form-control" rows="4"><?= htmlspecialchars($artist['artist_bio']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Artist Image:</label>
            <input type="file" name="image" id="image" class="form-control">
            <?php if (!empty($artist['artist_image'])): ?>
                <div class="mt-2">
                    <img src="<?= htmlspecialchars($artist['artist_image']) ?>" alt="Artist Image" width="100">
                </div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="manage_artists.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
