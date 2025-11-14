<?php
session_start();
require_once 'db.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: add_artworks.php");
    exit();
}

// Fetch all artworks with artist name
$artworks = $conn->query("
    SELECT artworks.*, artists.artist_name 
    FROM artworks 
    JOIN artists ON artworks.artist_id = artists.artist_id
");

// Handle deletion
if (isset($_GET['delete'])) {
    $artwork_id = $_GET['delete'];

    // Fetch artwork image path before deletion
    $result = $conn->query("SELECT artwork_image FROM artworks WHERE artwork_id = $artwork_id");
    $artwork = $result->fetch_assoc();
    if ($artwork['artwork_image']) {
        unlink($artwork['artwork_image']); // Delete image from server
    }

    // Delete artwork from database
    $conn->query("DELETE FROM artworks WHERE artwork_id = $artwork_id");
    header("Location: admin_artworks.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artworks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Manage Artworks</h2>

    <a href="add_artwork.php" class="btn btn-success mb-3">Add New Artwork</a>
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a> 

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Artist</th>
                <th>Type</th>
                <th>Bio</th>
                <th>Price ($)</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($artwork = $artworks->fetch_assoc()): ?>
                <tr>
                    <td><?= $artwork['artwork_id'] ?></td>
                    <td><?= htmlspecialchars($artwork['artwork_title']) ?></td>
                    <td><?= htmlspecialchars($artwork['artist_name']) ?></td>
                    <td><?= nl2br(htmlspecialchars($artwork['artwork_type'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($artwork['artwork_bio'])) ?></td>
                    <td><?= number_format($artwork['artwork_price'], 2) ?></td>
                    <td>
                        <?php if (!empty($artwork['artwork_image'])): ?>
                            <img src="<?= $artwork['artwork_image'] ?>" alt="Artwork Image" width="70" height="70">
                        <?php else: ?>
                            No Image
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_artwork.php?id=<?= $artwork['artwork_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="admin_artworks.php?delete=<?= $artwork['artwork_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>