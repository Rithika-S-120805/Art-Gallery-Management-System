<?php
session_start();
include 'db.php'; // Ensure this contains database connection details

// Fetch artists from the database
$sql = "SELECT * FROM artists";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Gallery</title>
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
            width: 350px;
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
            height: 250px;
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
            max-width: 60%;
            max-height: 80%;
            align-items: center;
        }

        .modal img {
            max-width: 40%;
            height: auto;
            border-radius: 8px;
        }

        .modal-details {
            padding: 20px;
            text-align: left;
            max-width: 60%;
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
    <h2>Artist Gallery</h2>

    <div class="grid">
        <?php while ($row = $result->fetch_assoc()) { 
            // Get the artist image path from the database
            $imagePath = !empty($row['artist_image']) ? htmlspecialchars($row['artist_image']) : "default-placeholder.jpg";
        ?>
            <div class="card" onclick="openModal('<?php echo $imagePath; ?>', '<?php echo htmlspecialchars($row['artist_name']); ?>', '<?php echo htmlspecialchars($row['artist_email']); ?>', '<?php echo htmlspecialchars($row['artist_bio']); ?>')">
                <img src="<?php echo $imagePath; ?>" alt="Artist Image">
                <h3><?php echo htmlspecialchars($row['artist_name']); ?></h3>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($row['artist_email']); ?></p>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal for Full Artist View -->
<div id="artistModal" class="modal">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="modal-content-container">
        <img id="artistImage">
        <div class="modal-details">
            <h3 id="modalName"></h3>
            <p><strong>Email:</strong> <span id="modalEmail"></span></p>
            <p><strong>Bio:</strong> <span id="modalBio"></span></p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("artistModal").style.display = "none";
});

function openModal(imageSrc, name, email, bio) {
    document.getElementById('artistImage').src = imageSrc;
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('modalBio').textContent = bio;
    document.getElementById('artistModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('artistModal').style.display = "none";
}
</script>


</body>
</html>

<?php
$conn->close();
?>
