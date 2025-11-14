<?php
session_start();
include 'db.php'; // Ensure this contains connection details

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch artworks from the database
$sql = "SELECT * FROM artworks ORDER BY artwork_id DESC";
$result = $conn->query($sql);
?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $(".add-to-fav").click(function() {
            let artworkId = $(this).data("artwork-id");
            let heartIcon = $(this);

            $.post("add_fav.php", { artwork_id: artworkId }, function(response) {
                let data = JSON.parse(response);
                if (data.status === "success") {
                    heartIcon.css("color", "red");
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            }).fail(function(xhr, status, error) {
                console.log("Error: " + error);
            });
        });

        $(".add-to-cart").click(function() {
            let artworkId = $(this).data("artwork-id");

            $.post("add_to_cart.php", { artwork_id: artworkId }, function(response) {
                let data = JSON.parse(response);
                alert(data.message);
            }).fail(function(xhr, status, error) {
                console.log("Error: " + error);
            });
        });
    });

</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f5f5f5; font-size: 20px; }
        .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #000;
            color: #fff;
            padding: 20px 30px;
        }
        .user-name { font-weight: bold; font-size: 18px; color: white; }
        .top-buttons a { margin-left: 10px; }
        .row { padding: 20px; }
        .card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            position: relative;
        }
        .card:hover { background: #ff0080; transform: translateY(-5px); }
        .card img { width: 100%; height: 250px; object-fit: cover; }
        .heart-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            color: grey;
            cursor: pointer;
        }
        .heart-icon:hover { color: red; }
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

<header>
    <div class="container-header">
        <span class="user-name">
            <?php echo isset($_SESSION['user_name']) ? "Welcome, " . htmlspecialchars($_SESSION['user_name']) : "Guest"; ?>
        </span>
        <div class="top-buttons">
            <a href="view_cart.php" class="btn btn-success"><i class="fas fa-shopping-cart"></i> Cart</a>
            <a href="favourites.php" class="btn btn-danger"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="orders.php" class="btn btn-primary"><i class="fas fa-box"></i> Orders</a>
            <a href="login.php" class="btn btn-dark"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</header>

<body>
    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php while ($row = $result->fetch_assoc()) { ?>
            <div class="col">
                <div class="card">
                    <i class="fas fa-heart heart-icon add-to-fav" data-artwork-id="<?php echo $row['artwork_id']; ?>"></i>
                    <div class="img-container">
                        <img src="<?php echo $row['artwork_image']; ?>" alt="Artwork Image">
                    </div>
                    <div class="p-3 text-center">
                        <h5><?php echo htmlspecialchars($row['artwork_title']); ?></h5>
                        <p class="price">$<?php echo number_format($row['artwork_price'], 2); ?></p>
                        <button class="btn btn-info" onclick="openModal('<?php echo $row['artwork_image']; ?>', '<?php echo htmlspecialchars($row['artwork_title']); ?>', '<?php echo htmlspecialchars($row['artwork_type']); ?>', '<?php echo number_format($row['artwork_price'], 2); ?>', '<?php echo htmlspecialchars($row['artwork_bio']); ?>', <?php echo $row['artwork_id']; ?>)">View Details</button>

<button type="button" class="btn btn-primary mt-2 buy-now" 
    data-artwork-id="<?php echo $row['artwork_id']; ?>"
    data-artwork-title="<?php echo htmlspecialchars($row['artwork_title']); ?>"
    data-artwork-price="<?php echo floatval($row['artwork_price']); ?>"> <!-- Remove commas -->
    Buy Now
</button>


                        <button type="button" class="btn btn-primary mt-2 add-to-cart" data-artwork-id="<?php echo $row['artwork_id']; ?>">Add to Cart</button>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<!-- Modal -->
<div id="artworkModal" class="modal">
    <div class="modal-content-container">
        <img id="modalImage" class="modal-img">
        <div class="modal-details">
            <h3 id="modalTitle"></h3>
            <p><strong>Type:</strong> <span id="modalType"></span></p>
            <p class="price">Price: $<span id="modalPrice"></span></p>
            <p><strong>Bio:</strong> <span id="modalBio"></span></p>


<form method="POST" action="add_orders.php">
    <input type="hidden" name="artwork_id" id="buyNowId">
    <input type="hidden" name="artwork_title" id="buyNowTitle">
    <input type="hidden" name="artwork_price" id="buyNowPrice">
    <button type="submit" class="btn btn-primary mt-2">Buy Now</button>
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
    
    // Pass artwork details to the hidden form fields
    document.getElementById('buyNowId').value = artworkId;
    document.getElementById('buyNowTitle').value = title;
    document.getElementById('buyNowPrice').value = price;

    document.getElementById('artworkModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('artworkModal').style.display = "none";
}
</script>
<script>
$(document).ready(function() {
    $(".buy-now, #modalBuyNow").click(function() {
        let artworkId = $(this).data("artwork-id") || $("#buyNowId").val();
        let artworkTitle = $(this).data("artwork-title") || $("#buyNowTitle").val();
        let artworkPrice = $(this).data("artwork-price") || $("#buyNowPrice").val();

        $.ajax({
            type: "POST",
            url: "add_orders.php",
            data: {
                artwork_id: artworkId,
                artwork_title: artworkTitle,
                artwork_price: artworkPrice,
                ajax: true // Identify AJAX request
            },
            dataType: "json",
            success: function(response) {
                if (response.status === "success") {
                    alert(response.message);
                    window.location.href = response.redirect; // Redirect to add_orders.php
                } else {
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log("AJAX Error:", error);
                alert("An error occurred. Please try again.");
            }
        });
    });
});



</script>

</body>
</html>
<?php $conn->close(); ?>
