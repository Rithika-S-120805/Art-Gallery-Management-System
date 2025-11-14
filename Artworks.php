<?php
session_start();
include 'db.php'; // Ensure this contains connection details

// Fetch artworks from the database
$sql = "SELECT * FROM artworks";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artwork Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            text-align: center;
        }

        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        h2 {
            color: #333;
        }

        .grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 15px;
            width: 400px;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
            color: white;
            background: #ff0080;
        }

        .card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
            border-radius: 8px;
        }

        .card h3 {
            margin: 10px 0;
        }

        .card p {
            font-size: 14px;
            color: black;
        }

        .price {
            font-size: 18px;
            color: #ff0080;
            font-weight: bold;
        }

        /* Modal Styles */
        .modal {
    display: none; /* Ensure it's hidden by default */
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
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
            align-items: center;
        }

        .modal img {
            max-width: 50%;
            height: auto;
            border-radius: 8px;
        }

        .modal-details {
            padding: 20px;
            text-align: left;
            max-width: 50%;
        }

        .modal-details h3 {
            margin-top: 0;
        }

        .modal-details p {
            font-size: 18px;
            color: black;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 25px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Artwork Gallery</h2>

    <div class="grid">
        <?php while ($row = $result->fetch_assoc()) { 
            // Ensure valid image path
            $imagePath = !empty($row['artwork_image']) ? $row['artwork_image'] : "image/no-image.png";
        ?>
            <div class="card" onclick="openModal('<?php echo $imagePath; ?>', '<?php echo htmlspecialchars($row['artwork_title']); ?>', '<?php echo htmlspecialchars($row['artwork_type']); ?>', '<?php echo number_format($row['artwork_price'], 2); ?>', '<?php echo htmlspecialchars($row['artwork_bio']); ?>')">
                <img src="<?php echo $imagePath; ?>" alt="Artwork Image" onerror="this.src='image/no-image.png'">
                <h3><?php echo htmlspecialchars($row['artwork_title']); ?></h3>
                <p><strong>Type:</strong> <?php echo htmlspecialchars($row['artwork_type']); ?></p>
                <p class="price">$<?php echo number_format($row['artwork_price'], 2); ?></p>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal for Full Image View -->
<div id="imageModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="modal-content-container">
        <img id="fullImage">
        <div class="modal-details">
            <h3 id="modalTitle"></h3>
            <p><strong>Type:</strong> <span id="modalType"></span></p>
            <p class="price">Price: $<span id="modalPrice"></span></p>
            <p><strong>Bio:</strong> <span id="modalBio"></span></p>
        </div>
    </div>
</div>

<script>
function openModal(imageSrc, title, type, price, bio) {
    document.getElementById('fullImage').src = imageSrc;
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalType').textContent = type;
    document.getElementById('modalPrice').textContent = price;
    document.getElementById('modalBio').textContent = bio;
    document.getElementById('imageModal').style.display = "flex";
    document.getElementById('imageModal').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function closeModal() {
    document.getElementById('imageModal').style.display = "none";
}
</script>

</body>
</html>

<?php
$conn->close();
?>
