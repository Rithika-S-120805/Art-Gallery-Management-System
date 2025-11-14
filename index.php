<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Art Gallery</title>

  <style>
    /* Reset styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      color: #333;
    }

    /* Top contact bar */
    .top-bar {
      background-color: #222;
      color: #fff;
      padding: 10px 20px;
      text-align: center;
      font-size: 1.5rem;
    }

    /* Navigation bar */
    header {
     top: 5;
    left: 0;
    width: 100%;
    background-color: #000;
    color: #fff;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 30px;
    }

    .logo {
      font-size: 2.5rem;
      font-weight: bold;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 30px;
    }

    nav ul li a {
      color: #fff;
      text-decoration: none;
      font-size: 1.5rem;
      padding: 2px;
    }

    nav ul li a:hover {
      color: #ffc107;
    }

    /* Hero Section */
    .hero {
      background: url('images/gallery1.jpg') no-repeat center center/cover;
      height: 91vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: white;
      padding: 200px;
    }

    .hero-content {
      background: rgba(0, 0, 0, 0.6);
      padding: 30px;
      border-radius: 10px;
      max-width: 600px;
    }

    .hero h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
    }

    .hero p {
      font-size: 1.0rem;
      margin-bottom: 20px;
    }

    .hero a {
      display: inline-block;
      padding: 12px 25px;
      background: #ff0080;
      color: white;
      text-decoration: none;
      font-size: 1.0rem;
      border-radius: 5px;
      transition: 0.3s;
    }

    .hero a:hover {
      background: green;
    }

    /* About Section */
    .about {
      text-align: center;
      padding: 80px 20px;
      max-width: 900px;
      margin: auto;
    }

    .about h2 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: #222;
    }

    .about p {
      font-size: 1.2rem;
      color: #555;
      line-height: 1.6;
    }

    /* Gallery Section */

    .gallery {

      display: flex;
      justify-content: center;
      flex-wrap: wrap;
      gap: 30px;
      padding: 80px 50px;
    }

    .gallery-card {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 300px;
      transition: 0.3s;
    }

    .gallery-card:hover {
      transform: translateY(-5px);
    }

    .gallery-card img {
      width: 100%;
      border-radius: 5px;
      margin-bottom: 15px;
    }

    .gallery-card h3 {
      text align: center;
      font-size: 1.5rem;
      color: #333;
      margin-bottom: 10px;
    }

    .gallery-card p {
      font-size: 1rem;
      color: #666;
    }
.artist {
  display: flex;
  justify-content: center;
  flex-wrap: wrap;
  gap: 30px;
  padding: 80px 50px;
  text-align: center;
}

.view-more {
  display: inline-block;
align-items: center;
  margin-top: 10px;
  padding: 8px 15px;
  background: #ff0080;
  color: white;
  text-decoration: none;
text-align: center;
  font-size: 1rem;
  border-radius: 5px;
  transition: 0.3s;
}

.view-more:hover {
  background: blue;
}

.view-more1 {
    position: relative;
    top: -30px; /* Moves it up */
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    width: 100%;
}

.read-more-btn {
	
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ff0080; /* Pink Color */
    color: white;
    font-size: 1rem;
    font-weight: bold;
    border: none;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    cursor: pointer;
    transition: background 0.3s;
}

.read-more-btn:hover {
    background-color: blue; 
}

.read-more-btn i {
    margin-left: 8px; /* Space between text and arrow */
	font-size: 12px;
}

    /* Footer */
    footer {
      background-color: #000;
      color: #fff;
      text-align: center;
      padding: 30px;
      font-size: 1rem;
    }

  </style>

</head>
<body>


  <!-- Header and navigation -->
  <header>
    <div class="logo">Art Gallery</div>
    <nav>
      <ul>
        <li><a href="">Home</a></li>
	<li><a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#hyperlink2">Artists</a></li>
        <li><a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#hyperlink1">Artworks</a></li>
	<li><a href='login.php'>Login/Sign-up</a></li>
        <li><a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>#about">About Us</a></li>
        
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1>Welcome to Our Art Gallery</h1>
      <p>Discover an exquisite collection of timeless artworks and paintings.</p>
      <a href="login.php">Explore Now</a>
    </div>
  </section>



<section class = "read-more-btn" id="hyperlink1">
    <h2 style="text-align: center; font-size: 2.5rem; color: #222; margin-top: 0px;">Featured Artworks</h2>
</section>

  <!-- Gallery Section -->
  <section class="gallery" id="gallery">

    <div class="gallery-card">
      <img src="images/art1.jpg" alt="Art 1">
      <h3>Starry Night</h3>
      <p>A masterpiece by Vincent van Gogh, filled with swirling blue tones.</p>
	
    </div>
    <div class="gallery-card">
      <img src="images/art2.jpg" alt="Art 2">
      <h3>Guernica</h3>
      <p>A powerful anti-war painting by Pablo Picasso.</p>

    </div>
    <div class="gallery-card">
      <img src="images/art3.jpg" alt="Art 3">
      <h3>Water Lilies</h3>
      <p>Claude Monet’s beautiful depiction of nature and tranquility.</p>
  
</div>
   <div class="gallery-card">
      <img src="images/b1.jpg" alt="Art 1">
      <h3>Starry Night</h3>
      <p>A masterpiece by Vincent van Gogh, filled with swirling blue tones.</p>

    </div>
    
      
  </section>

<section class = "view-more1">
    <a href="Artworks.php" class="read-more-btn">View More Artworks</a>
</section>

<section class = none>
	<h>              </h>	
</section>

<section class = "read-more-btn" id="hyperlink2">
  <h2 style="text-align: center; font-size: 2.5rem; color: #222;  margin-top: 0px;">Featured Artists</h2>
</section>


<!-- Artist Section -->
<section class="artist" id="Artists">
  <div class="gallery-card">
    <img src="images/artist1.jpg" alt="Artist 1">
    <h3>Vincent van Gogh</h3>
    <p>A post-impressionist artist known for expressive color and movement.</p>

  </div>
  <div class="gallery-card">
    <img src="images/artist2.jpg" alt="Artist 2">
    <h3>Pablo Picasso</h3>
    <p>A legendary Spanish artist, co-founder of Cubism and modern art.</p>

  </div>
  <div class="gallery-card">
    <img src="images/artist3.jpg" alt="Artist 3">
    <h3>Claude Monet</h3>
    <p>Impressionist painter famous for capturing light and atmosphere.</p>

  </div>

 <div class="gallery-card">
    <img src="images/artist1.jpg" alt="Artist 1">
    <h3>Vincent van Gogh</h3>
    <p>A post-impressionist artist known for expressive color and movement.</p>

  </div>
</section>

<section class = "view-more1">
   <a href="Artists.php" class="read-more-btn" align = center> View More Artists</a>
</section>


 <!-- About Section -->
  <section class="about" id="about">
    <h2>About Our Gallery</h2>
    <p>
      Our art gallery showcases a diverse collection of paintings and sculptures by renowned and emerging artists.
      Experience the world of art like never before.
    </p>
  </section>

<!-- Bottom contact bar -->
  <div class="top-bar">
    📞 1234567890 &nbsp; ✉ info@gmail.com
  </div>

  <!-- Footer -->
  <footer>
    <p>&copy; 2025 Art Gallery. All Rights Reserved.</p>
  </footer>

</body>
</html>
