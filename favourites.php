<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Secure SQL query using prepared statements
$stmt = $conn->prepare("SELECT artworks.artwork_id, artworks.artwork_title, artworks.artwork_image, 
                        artworks.artwork_price, artworks.artwork_type, artworks.artwork_bio
                        FROM favourites 
                        JOIN artworks ON favourites.artwork_id = artworks.artwork_id 
                        WHERE favourites.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Handle Remove from Favourites securely
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['remove_id'])) {
    $remove_id = intval($_POST['remove_id']);
    $stmt = $conn->prepare("DELETE FROM favourites WHERE artwork_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $remove_id, $user_id);
    $stmt->execute();
    header("Location: favourites.php");
    exit();
}

// Handle Add to Cart securely
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart_id'])) {
    $artwork_id = intval($_POST['add_to_cart_id']);
    $stmt = $conn->prepare("INSERT INTO cart (user_id, artwork_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $artwork_id);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}

// Handle Buy Now securely
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['buy_now_id'])) {
    $artwork_id = intval($_POST['buy_now_id']);
    header("Location: checkout.php?artwork_id=" . urlencode($artwork_id));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favourites</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .card-img-top {
            height: 250px;
            object-fit: cover;
            width: 100%;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .btn-remove {
            background: red;
            color: white;
            border: none;
        }
        .btn-remove:hover {
            background: darkred;
        }
        .row {
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 20px;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            align-items: center;
            justify-content: center;
        }
        .modal-content-container {
            display: flex;
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 70%;
            max-height: 80%;
            overflow-y: auto;
        }
        .modal-img {
            flex: 1;
            max-width: 50%;
            height: 400px;
            object-fit: cover;
            border-radius: 8px;
        }
        .modal-details {
            flex: 1;
            padding: 20px;
            text-align: left;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">My Favourites</h2>
    
    <a href="user_dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>

    <?php if ($result->num_rows > 0) { ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= htmlspecialchars($row['artwork_image']); ?>" class="card-img-top">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['artwork_title']); ?></h5>
                            <p class="card-text">Price: $<?= number_format($row['artwork_price'], 2); ?></p>

                            <button class="btn btn-info" onclick="openModal('<?= htmlspecialchars($row['artwork_image']); ?>', '<?= htmlspecialchars($row['artwork_title']); ?>', '<?= htmlspecialchars($row['artwork_type']); ?>', '<?= number_format($row['artwork_price'], 2); ?>', '<?= htmlspecialchars($row['artwork_bio']); ?>', <?= $row['artwork_id']; ?>)">
                                View Details
                            </button>

                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="remove_id" value="<?= $row['artwork_id']; ?>">
                                <button type="submit" class="btn btn-remove">Remove</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <!-- No Favourites Message -->
        <div class="text-center mt-5">
                <h4>No Favourite Artworks Yet</h4>
            <p>Browse the gallery and add your favorite artworks!</p>
            <a href="user_dashboard.php" class="btn btn-primary">Explore Gallery</a>
        </div>
    <?php } ?>
</div>

<!-- Modal for Artwork Details -->
<div id="artworkModal" class="modal">
    <div class="modal-content-container">
        <img id="modalImage" class="modal-img">
        <div class="modal-details">
            <h3 id="modalTitle"></h3>
            <p><strong>Type:</strong> <span id="modalType"></span></p>
            <p class="price">Price: $<span id="modalPrice"></span></p>
            <p><strong>Bio:</strong> <span id="modalBio"></span></p>

            <form method="POST">
                <input type="hidden" name="add_to_cart_id" id="addToCartId">
                <button type="submit" class="btn btn-success">Add to Cart</button>
            </form>

            <form method="POST">
                <input type="hidden" name="buy_now_id" id="buyNowId">
                <button type="submit" class="btn btn-primary">Buy Now</button>
            </form>

            <button class="btn btn-danger mt-2" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<script>
function openModal(imageSrc, title, type, price, bio, artworkId) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalType').textContent = type;
    document.getElementById('modalPrice').textContent = price;
    document.getElementById('modalBio').textContent = bio;
    document.getElementById('addToCartId').value = artworkId;
    document.getElementById('buyNowId').value = artworkId;

    document.getElementById('artworkModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('artworkModal').style.display = "none";
}
</script>

</body>
</html>

<?php $conn->close(); ?>
