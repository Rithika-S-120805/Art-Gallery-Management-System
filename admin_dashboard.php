<?php
session_start();
require_once 'db.php';

// Fetch data counts
$total_artists = $conn->query("SELECT COUNT(*) as count FROM artists")->fetch_assoc()['count'];
$total_artworks = $conn->query("SELECT COUNT(*) as count FROM artworks")->fetch_assoc()['count'];
$total_art_types = $conn->query("SELECT COUNT(DISTINCT artwork_type) as count FROM artworks")->fetch_assoc()['count'];
$avg_artwork_price = $conn->query("SELECT AVG(artwork_price) as avg_price FROM artworks WHERE artwork_price IS NOT NULL")->fetch_assoc()['avg_price'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders")->fetch_assoc()['count'];
$avg_order_price = $conn->query("SELECT AVG(price) as avg_price FROM orders WHERE status!='Cancelled'")->fetch_assoc()['avg_price'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            justify-content: space-between; /* Push logout button to the bottom */
        }
        .sidebar a { 
            color: white; 
            text-decoration: none; 
            display: block; 
            padding: 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background: #333;
        }
        .content { 
            margin-left: 270px; 
            padding: 20px; 
            text-align: center; 
        }
        .card { 
            border-radius: 10px; 
            width: 300px; 
            height: 150px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
        }
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
        .logout-btn:hover {
            background-color: blue;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h3 style="text-align:center;">ART GALLERY ADMIN</h3>
    <hr>
    <nav>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="admin_artists.php">Artists</a>
        <a href="admin_artworks.php">Artworks</a>
        <a href="admin_orders.php">Orders</a>
    </nav>
    <hr>
    <a href="login.php" class="logout-btn">Logout</a>
</div>

<div class="content">
    <h2>----------- DASHBOARD -----------</h2>

    <div class="row justify-content-center mt-4">
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Artists</h5>
                    <h2><?= $total_artists ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Artworks</h5>
                    <h2><?= $total_artworks ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Art Types</h5>
                    <h2><?= $total_art_types ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Average Artwork Price</h5>
                    <h2>$<?= number_format($avg_artwork_price, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Total Orders</h5>
                    <h2><?= $total_orders ?></h2>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 d-flex justify-content-center">
            <div class="card text-white mb-3" style="background-color: #ff0080;">
                <div class="card-body text-center">
                    <h5 class="card-title">Average Order Amount</h5>
                    <h2>$<?= number_format($avg_order_price, 2) ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
