<?php
session_start();
require_once 'db.php';

// Ensure artist is logged in
if (!isset($_SESSION['artist_id']) || $_SESSION['role'] !== 'artist') {
    header("Location: login.php");
    exit();
}

$artist_id = $_SESSION['artist_id'];

// Fetch artist's artworks
$stmt = $conn->prepare("SELECT artwork_id, artwork_title, artwork_type, artwork_price FROM artworks WHERE artist_id = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$artworks = $stmt->get_result();

// Fetch artist's total artwork count
$count_stmt = $conn->prepare("SELECT COUNT(*) as count FROM artworks WHERE artist_id = ?");
$count_stmt->bind_param("i", $artist_id);
$count_stmt->execute();
$total_artworks = $count_stmt->get_result()->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Dashboard</title>
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
        .content { margin-left: 270px; padding: 20px; text-align: center; }
        .card { border-radius: 10px; width: 300px; height: 150px; display: block; }
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
        .table th, .table td { text-align: center; }
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
            <a href="manage_art.php">Manage Artworks</a>
        </div>
        <hr>
    </div>
    <a href="login.php" class="logout-btn">Logout</a>
</div>

<div class="content">
    <h2>🎨 ARTIST DASHBOARD 🎨</h2>
    <hr>

    <div class="row justify-content-center">
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body">
                    <h5 class="card-title">Your Total Artworks</h5>
                    <h2><?= $total_artworks ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <hr>

    <h3>Your Artworks</h3>
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Name</th>
                <th>Type</th>
                <th>Price ($)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($artwork = $artworks->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($artwork['artwork_title']) ?></td>
                    <td><?= htmlspecialchars($artwork['artwork_type']) ?></td>
                    <td>$<?= number_format($artwork['artwork_price'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
