<?php
session_start();
require_once 'db.php';


if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'artist'])) 
 {
    header("Location: login.php");
    exit();
}


$artist_id = $_SESSION['user_id'];

// Handle artwork deletion
if (isset($_GET['delete'])) {
    $artwork_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM artworks WHERE artwork_id = ? AND artist_id = ?");
    $stmt->bind_param("ii", $artwork_id, $artist_id);
    if ($stmt->execute()) {
        $success_msg = "Artwork deleted successfully.";
    } else {
        $error_msg = "Error deleting artwork.";
    }
}

// Fetch artist's artworks
$stmt = $conn->prepare("SELECT artwork_id, artwork_title, artwork_type, artwork_price FROM artworks WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$artworks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artworks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            background: black;
            padding: 20px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover { background: #ff0080; }
        .content { margin-left: 270px; padding: 20px; }
        .logout-btn {
            background-color: #ff0080;
            color: white;
            padding: 10px;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            display: block;
            margin-top: auto;
        }
        .logout-btn:hover { background-color: blue; }
    </style>
</head>
<body>

<div class="sidebar">
    <div>
        <h3 style="text-align:center;">ARTIST DASHBOARD</h3>
        <hr>
        <div style="text-align:center;">
            <a href="artist_dashboard.php">Dashboard</a>
            <a href="add_artwork.php">Upload Artwork</a>
            <a href="manage_artworks.php">Manage Artworks</a>
        </div>
        <hr>
    </div>
    <a href="login.php" class="logout-btn">Logout</a>
</div>

<div class="content">
    <h2>🎨 Manage Your Artworks</h2>
    <hr>
    
    <?php if (isset($success_msg)): ?>
        <div class="alert alert-success"> <?= $success_msg ?> </div>
    <?php elseif (isset($error_msg)): ?>
        <div class="alert alert-danger"> <?= $error_msg ?> </div>
    <?php endif; ?>
    
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Price ($)</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($artwork = $artworks->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($artwork['artwork_title']) ?></td>
                    <td><?= htmlspecialchars($artwork['artwork_type']) ?></td>
                    <td>$<?= number_format($artwork['artwork_price'], 2) ?></td>
                    <td>
                        <a href="edit_artwork.php?id=<?= $artwork['artwork_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="manage_artworks.php?delete=<?= $artwork['artwork_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this artwork?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
