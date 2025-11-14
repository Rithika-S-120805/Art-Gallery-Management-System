<?php
session_start();
include 'db.php';

$error = ""; // Initialize error message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = null;
    $role = "";

    // Check in Users Table
    $stmt = $conn->prepare("SELECT user_id AS id, user_name AS name, user_password AS password FROM users WHERE user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $role = "user";
    }

    // Check in Administrators Table
    if (!$user) { // Only check if the user is not found
        $stmt = $conn->prepare("SELECT admin_id AS id, admin_name AS name, admin_password AS password FROM administrators WHERE admin_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $role = "admin";
        }
    }

    // Check in Artists Table
    if (!$user) { // Only check if still not found
        $stmt = $conn->prepare("SELECT artist_id AS id, artist_name AS name, artist_password AS password FROM artists WHERE artist_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $role = "artist";

            // Store artist ID in session correctly
            $_SESSION['artist_id'] = $user['id'];  
        }
    }

    // If user exists, verify the password
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $role;

        // Redirect based on role
        if ($role == 'admin') {
            header("Location: admin_dashboard.php");
        } elseif ($role == 'user') {
            header("Location: user_dashboard.php");
        } elseif ($role == 'artist') {
            header("Location: artist_dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Art Gallery - Login</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px #ccc; width: 300px; text-align: center; }
        input { width: 100%; padding: 5px; margin: 10px 0; border: 1px solid #ccc; border-radius: 5px; }
        button { width: 100%; padding: 10px; background: #ff0080; color: white; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: blue; }
        .create { background: #ff0080; margin-top: 10px; }
        .error { color: red; margin-bottom: 10px; }

        /* Home Button Styling */
        .home-btn { 
            position: absolute; 
            bottom: 15px; 
            right: 0px; 
            padding: 8px 1px; 
            background: #ff0080; 
            color: white; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 20px; 
        }
        .home-btn:hover { background: blue; }
    </style>
</head>
<body>

<div class="container">
    <form method="POST">
        <h2>Log in</h2>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
        <input type="email" name="email" placeholder="Email address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Log in</button>
       
        <button class="create" onclick="window.location.href='register.php'">Create new account</button>
    </form>
</div>

<!-- Return to Home Button --> 
<div>
    <button class="home-btn" onclick="window.location.href='index.php'">Home</button>   
</div>

</body>
</html>
