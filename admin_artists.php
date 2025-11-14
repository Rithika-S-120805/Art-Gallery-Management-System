<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch all artists
$artists = $conn->query("SELECT * FROM artists");

// Handle deletion
if (isset($_GET['delete'])) {
    $artist_id = $_GET['delete'];

    // Fetch artist image path before deletion
    $result = $conn->query("SELECT artist_image FROM artists WHERE artist_id = $artist_id");
    $artist = $result->fetch_assoc();
    if ($artist['artist_image']) {
        unlink($artist['artist_image']); // Delete image from server
    }

    // Delete artist from database
    $conn->query("DELETE FROM artists WHERE artist_id = $artist_id");
    header("Location: admin_artists.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artists</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Artists</h2>

    <a href="add_artist.php" class="btn btn-success mb-3">Add New Artist</a>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a> 

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Bio</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($artist = $artists->fetch_assoc()): ?>
                <tr>
                    <td><?= $artist['artist_id'] ?></td>
                    <td><?= htmlspecialchars($artist['artist_name']) ?></td>
                    <td><?= htmlspecialchars($artist['artist_email']) ?></td>
                    <td><?= nl2br(htmlspecialchars($artist['artist_bio'])) ?></td>
                    <td>
                        <?php if (!empty($artist['artist_image'])): ?>
                            <img src="<?= $artist['artist_image'] ?>" alt="Artist Image" width="70" height="70">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_artist.php?id=<?= $artist['artist_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="admin_artists.php?delete=<?= $artist['artist_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
