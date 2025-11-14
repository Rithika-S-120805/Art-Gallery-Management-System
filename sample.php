<?php
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery Walls - ArtGallery</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/script.js" defer></script>
</head>
<body>

    <header>
        <h1>Gallery Walls</h1>
        <p>Curated collections for your space</p>
    </header>

    <section class="gallery-filters">
        <button class="filter-btn" data-category="all">All</button>
        <button class="filter-btn" data-category="modern">Modern</button>
        <button class="filter-btn" data-category="classic">Classic</button>
        <button class="filter-btn" data-category="abstract">Abstract</button>
    </section>

    <section class="gallery-grid">
        <?php
        $query = "SELECT * FROM artworks WHERE category='gallery-wall'";
        $result = mysqli_query($conn, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='gallery-item' data-category='{$row['artwork_type']}'>";
            echo "<img src='assets/images/{$row['artwork_image']}' alt='{$row['artwork_title']}'>";
            echo "<h3>{$row['artwork_title']}</h3>";
            echo "<p>{$row['artwork_bio']}</p>";
            echo "<span>\$ {$row['artwork_price']}</span>";
            echo "</div>";
        }
        ?>
    </section>

</body>
</html>
