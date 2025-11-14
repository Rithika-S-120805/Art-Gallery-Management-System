<?php
session_start();
require_once 'db.php';

// Ensure artist is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'artist') {
    header("Location: login.php");
    exit();
}

$artist_id = $_SESSION['user_id'];

// Fetch only artworks belonging to the logged-in artist
$artworks = $conn->prepare("
    SELECT * FROM artworks WHERE artist_id = ?
");
$artworks->bind_param("i", $artist_id);
$artworks->execute();
$result = $artworks->get_result();

// Handle deletion
if (isset($_GET['delete'])) {
    $artwork_id = $_GET['delete'];

    // Fetch artwork image path before deletion
    $stmt = $conn->prepare("SELECT artwork_image FROM artworks WHERE artwork_id = ? AND artist_id = ?");
    $stmt->bind_param("ii", $artwork_id, $artist_id);
    $stmt->execute();
    $artwork = $stmt->get_result()->fetch_assoc();

    if ($artwork) {
        if (!empty($artwork['artwork_image'])) {
            unlink($artwork['artwork_image']); // Delete image from server
        }

        // Delete artwork from database
        $delete_stmt = $conn->prepare("DELETE FROM artworks WHERE artwork_id = ? AND artist_id = ?");
        $delete_stmt->bind_param("ii", $artwork_id, $artist_id);
        $delete_stmt->execute();
    }

    header("Location: manage_art.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Your Artworks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Your Artworks</h2>

    <a href="add_artwork.php" class="btn btn-success mb-3">Add New Artwork</a>
    <a href="artist_dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a> 

    <table class="table table-bordered text-center">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Type</th>
                <th>Bio</th>
                <th>Price ($)</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($artwork = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $artwork['artwork_id'] ?></td>
                    <td><?= htmlspecialchars($artwork['artwork_title']) ?></td>
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
                        <a href="edit_art.php?id=<?= $artwork['artwork_id'] ?>" class="btn btn-warning btn-sm">Edit</a>

                        <a href="manage_art.php?delete=<?= $artwork['artwork_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
