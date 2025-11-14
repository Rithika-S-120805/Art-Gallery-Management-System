<?php
session_start();
require_once 'db.php';

// Ensure either admin or artist is logged in
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'artist'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];
$success_msg = "";
$error_msg = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $artwork_title = trim($_POST['artwork_title']);
    $artwork_type = trim($_POST['artwork_type']);
    $artwork_price = floatval($_POST['artwork_price']);
    $artwork_bio = trim($_POST['artwork_bio']); // New bio field
    $image_path = "";

    // Assign artist_id based on user role
    $artist_id = ($user_role === 'admin') ? $_POST['artist_id'] : $user_id;

    // Handle image upload
    if (isset($_FILES['artwork_image']) && $_FILES['artwork_image']['error'] === 0) {
        $target_dir = "image/Artworks/"; // Updated target directory
        $image_name = basename($_FILES["artwork_image"]["name"]);
        $image_file_type = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

        // Validate image type
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_file_type, $allowed_types)) {
            $error_msg = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        $error_msg = "Error uploading image.";
    }

    // Insert artwork if no error
    if (empty($error_msg)) {
        $stmt = $conn->prepare("INSERT INTO artworks (artist_id, artwork_title, artwork_type, artwork_price, artwork_bio) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issds", $artist_id, $artwork_title, $artwork_type, $artwork_price, $artwork_bio); // Added bio to bind_param

        if ($stmt->execute()) {
            $artwork_id = $conn->insert_id; // Get the auto-incremented artwork_id

            // Set the final image path
            $image_path = $target_dir . $artwork_id . "." . $image_file_type;

            // Move the uploaded image to the final destination
            if (move_uploaded_file($_FILES["artwork_image"]["tmp_name"], $image_path)) {
                // Update the artwork record with the new image path
                $update_stmt = $conn->prepare("UPDATE artworks SET artwork_image = ? WHERE artwork_id = ?");
                $update_stmt->bind_param("si", $image_path, $artwork_id);
                $update_stmt->execute();
                $update_stmt->close();

                $success_msg = "Artwork added successfully!";
            } else {
                $error_msg = "Error moving uploaded image.";
            }
        } else {
            $error_msg = "Database error: " . $conn->error;
        }

        $stmt->close(); // Close the insert statement
    }
}

// Fetch artists for admin dropdown
$artists = $conn->query("SELECT artist_id, artist_name FROM artists");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Artwork</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Add New Artwork</h2>
    <hr>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="artwork_title" class="form-label">Title:</label>
            <input type="text" name="artwork_title" id="artwork_title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="artwork_type" class="form-label">Type:</label>
            <input type="text" name="artwork_type" id="artwork_type" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="artwork_price" class="form-label">Price ($):</label>
            <input type="number" name="artwork_price" id="artwork_price" class="form-control" step="0.01" required>
        </div>

        <div class="mb-3">
            <label for="artwork_bio" class="form-label">Bio:</label>
            <textarea name="artwork_bio" id="artwork_bio" class="form-control" rows="3" required></textarea>
        </div>

        <?php if ($user_role === 'admin'): ?>
            <div class="mb-3">
                <label for="artist_id" class="form-label">Select Artist:</label>
                <select name="artist_id" id="artist_id" class="form-control" required>
                    <option value="">-- Select Artist --</option>
                    <?php while ($artist = $artists->fetch_assoc()): ?>
                        <option value="<?= $artist['artist_id'] ?>"><?= htmlspecialchars($artist['artist_name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
        <?php endif; ?>

        <div class="mb-3">
            <label for="artwork_image" class="form-label">Upload Image:</label>
            <input type="file" name="artwork_image" id="artwork_image" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Artwork</button>
        <a href="artist_dashboard.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>
