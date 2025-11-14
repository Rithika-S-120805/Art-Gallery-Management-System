<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $bio = trim($_POST['bio']);
    $password = $_POST['password']; // Store password as plain text

    // Handle Image Upload
    $image_path = "";
    if (!empty($_FILES['image']['name'])) {
        // Temporary path for the image
        $temp_image_path = "image/Artists/temp.jpg";
        
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $temp_image_path)) {
            // Insert artist into database first to get artist_id
            $stmt = $conn->prepare("INSERT INTO artists (artist_name, artist_email, artist_bio, artist_password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $bio, $password);

            if ($stmt->execute()) {
                $new_artist_id = $conn->insert_id; // Get the auto-incremented artist_id
                
                // Set the final image path
                $image_path = "image/Artists/" . $new_artist_id . ".jpg";
                
                // Move the uploaded image to the final destination
                rename($temp_image_path, $image_path);
                
                // Update the artist record with the image path
                $update_stmt = $conn->prepare("UPDATE artists SET artist_image = ? WHERE artist_id = ?");
                $update_stmt->bind_param("si", $image_path, $new_artist_id);
                $update_stmt->execute();
                $update_stmt->close();

                $success_msg = "Artist added successfully! Artist ID: " . $new_artist_id;
            } else {
                $error_msg = "Error: " . $conn->error;
            }

            $stmt->close(); // Close the insert statement
        } else {
            $error_msg = "Error uploading image.";
        }
    } else {
        $error_msg = "Image file is required.";
    }
} // <-- Closing brace for the if ($_SERVER["REQUEST_METHOD"] === "POST")
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Artist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2>Add New Artist</h2>
    <hr>

    <?php if ($success_msg): ?>
        <div class="alert alert-success"><?= $success_msg ?></div>
    <?php elseif ($error_msg): ?>
        <div class="alert alert-danger"><?= $error_msg ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="name" class="form-label">Artist Name:</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Bio:</label>
            <textarea name="bio" id="bio" class="form-control" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Upload Image:</label>
            <input type="file" name="image" id="image" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Artist</button>
        <a href="admin_artists.php" class="btn btn-secondary">Back</a>
    </form>
</div>

</body>
</html>