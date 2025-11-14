<?php
session_start();
include 'db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = ""; // Variable to store error messages

// Check database connection
if ($conn->connect_error) {
    $error = "Connection failed: " . $conn->connect_error;
    die($error); // Stop execution if connection fails
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 

    if (!isset($_POST['email'], $_POST['password'], $_POST['role']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['role'])) {
        $error = "Please fill in all required fields!";
    } else {
        $email = trim($_POST['email']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        // Validate email contains '@'
        if (strpos($email, '@') === false) {
            $error = "Invalid email! Please make sure the email contains '@'.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email format
            $error = "Invalid email format!";
        } else {
            // Check if email already exists in the database
            $email_exists = false;

            // Check in users table
            $check_user_sql = "SELECT user_email FROM users WHERE user_email = ?";
            $stmt_user = $conn->prepare($check_user_sql);
            if (!$stmt_user) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt_user->bind_param("s", $email);
            $stmt_user->execute();
            $stmt_user->store_result();
            if ($stmt_user->num_rows > 0) {
                $email_exists = true;
            }
            $stmt_user->close();

            // Check in administrators table
            if (!$email_exists) {
                $check_admin_sql = "SELECT admin_email FROM administrators WHERE admin_email = ?";
                $stmt_admin = $conn->prepare($check_admin_sql);
                if (!$stmt_admin) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt_admin->bind_param("s", $email);
                $stmt_admin->execute();
                $stmt_admin->store_result();
                if ($stmt_admin->num_rows > 0) {
                    $email_exists = true;
                }
                $stmt_admin->close();
            }

            // Check in artists table
            if (!$email_exists) {
                $check_artist_sql = "SELECT artist_email FROM artists WHERE artist_email = ?";
                $stmt_artist = $conn->prepare($check_artist_sql);
                if (!$stmt_artist) {
                    die("Prepare failed: " . $conn->error);
                }
                $stmt_artist->bind_param("s", $email);
                $stmt_artist->execute();
                $stmt_artist->store_result();
                if ($stmt_artist->num_rows > 0) {
                    $email_exists = true;
                }
                $stmt_artist->close();
            }

            if ($email_exists) {
                $error = $role. " already exists!!";
            } else {
                // Proceed with registration if email is not registered
                if ($role == "admin" && empty($_POST['admin_name'])) {
                    $error = "Admin name is required!";
                } elseif ($role == "user") {
                    if (!isset($_POST['user_name'], $_POST['dob'], $_POST['gender'], $_POST['phone']) || empty($_POST['user_name']) || empty($_POST['dob']) || empty($_POST['gender']) || empty($_POST['phone'])) {
                        $error = "All user fields are required!";
                    } else {
                        $name = trim($_POST['user_name']);
                        $dob = $_POST['dob'];
                        $gender = $_POST['gender'];
                        $phone = trim($_POST['phone']);

                        // Validate phone number (must be 10 digits)
                        if (!preg_match('/^\d{10}$/', $phone)) {
                            $error = "Phone number must be exactly 10 digits!";
                        }
                    }
                } elseif ($role == "artist" && (empty($_POST['artist_name']) || empty($_POST['bio']))) {
                    $error = "Artist name and bio are required!";
                }

                // If no errors, proceed with database insertion
                if (empty($error)) {
                    if ($role == "admin") {
                        $name = trim($_POST['admin_name']);
                        $stmt = $conn->prepare("INSERT INTO administrators (admin_name, admin_email, admin_password) VALUES (?, ?, ?)");
                        if (!$stmt) {
                            die("Prepare failed: " . $conn->error);
                        }
                        $stmt->bind_param("sss", $name, $email, $password);
                    } elseif ($role == "user") {
                        $stmt = $conn->prepare("INSERT INTO users (user_name, user_dob, user_gender, user_ph_no, user_email, user_password) VALUES (?, ?, ?, ?, ?, ?)");
                        if (!$stmt) {
                            die("Prepare failed: " . $conn->error);
                        }
                        $stmt->bind_param("ssssss", $name, $dob, $gender, $phone, $email, $password);
                    } elseif ($role == "artist") {
                        $artist_name = trim($_POST['artist_name']);
                        $bio = trim($_POST['bio']);
                        $stmt = $conn->prepare("INSERT INTO artists (artist_name, artist_bio, artist_email, artist_password) VALUES (?, ?, ?, ?)");
                        if (!$stmt) {
                            die("Prepare failed: " . $conn->error);
                        }
                        $stmt->bind_param("ssss", $artist_name, $bio, $email, $password);
                    }

                    if ($stmt->execute()) {
                        echo "<script>alert('Registration successful! You can now log in.'); window.location='login.php';</script>";
                    } else {
                        error_log("SQL Error: " . $stmt->error);
                        $error = "Error: " . $stmt->error;
                    }

                    $stmt->close();
                }
            }
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body { font-family: Arial, sans-serif; 
	background: #f0f2f5; 
	display: flex; 
	justify-content: center; 
	align-items: center; 
	height: 100vh; }

        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 15px;
            font-family: "Courier New", monospace;
            font-size: 25px;
            font-weight: bold;
        }

        /* Make all input fields, select, textarea, and button the same width */
        input, select, textarea, button {
            width: 100%; /* Match the width of the select box */
            padding: 7px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            text-align: left;
            box-sizing: border-box; /* Ensure padding and border are included in the width */
        }

        button {
            background: #ff0080;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
            text-align: center; /* Center text inside the button */
        }

        button:hover {
            background: blue;
        }

        .home-btn {
            position: absolute;
            bottom: 20px;
            padding: 8px 12px;
            background: #ff0080;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 20px;
        }

        .home-btn:hover {
            background: blue;
        }

        .hidden {
            display: none;
        }

        /* Add this CSS for error messages */
        .error {
            color: red;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>

    <script>
 function showFields() {
    var role = document.getElementById("role").value;
    
    // Hide all fields initially
    document.getElementById("adminFields").style.display = "none";
    document.getElementById("userFields").style.display = "none";
    document.getElementById("artistFields").style.display = "none";

    // Remove required attribute from all fields
    document.querySelectorAll("input, select, textarea").forEach(function (field) {
        field.removeAttribute("required");
    });

    // Show fields based on selected role and set required attributes
    if (role === "admin") {
        document.getElementById("adminFields").style.display = "block";
        document.querySelector("[name='admin_name']").setAttribute("required", true);
    } else if (role === "user") {
        document.getElementById("userFields").style.display = "block";
        document.querySelector("[name='user_name']").setAttribute("required", true);
        document.querySelector("[name='dob']").setAttribute("required", true);
        document.querySelector("[name='gender']").setAttribute("required", true);
        document.querySelector("[name='phone']").setAttribute("required", true); // Add required for phone
    } else if (role === "artist") {
        document.getElementById("artistFields").style.display = "block";
        document.querySelector("[name='artist_name']").setAttribute("required", true);
        document.querySelector("[name='bio']").setAttribute("required", true);
    }

    // Common fields (email and password) are always required
    document.querySelector("[name='email']").setAttribute("required", true);
    document.querySelector("[name='password']").setAttribute("required", true);
}

function validateForm() {
    var role = document.getElementById("role").value;
    var email = document.querySelector("[name='email']");
    var phone = document.querySelector("[name='phone']");

    // Reset custom validity messages
    email.setCustomValidity("");
    if (phone) phone.setCustomValidity("");

    // Validate email
    if (email.value.indexOf("@") === -1) {
        email.setCustomValidity("Invalid email! Please make sure the email contains '@'.");
        email.reportValidity(); // Show the validation message
        return false;
    }

    // Validate phone number (if applicable)
    if (role === "user" && phone) {
        // Remove non-digit characters (like spaces, hyphens, etc.)
        var cleanedPhone = phone.value.replace(/\D/g, '');

        // Check if the cleaned phone number is exactly 10 digits
        if (!/^\d{10}$/.test(cleanedPhone)) {
            phone.setCustomValidity("Phone number must be exactly 10 digits!");
            phone.reportValidity(); // Show the validation message
            return false;
        }
    }

    return true;
}
    </script>
</head>
<body>

<div class="container">
    <h2>Create New Account</h2>

    <!-- Display error message if email is already registered -->
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?><a href="login.php"></a></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm()">
        <tt><label for="role">Register as:</label><tt>
        <select id="role" name="role" onchange="showFields()" required>
            <option value="">Select Role</option>
            <option value="admin">Admin</option>
            <option value="user">User</option>
            <option value="artist">Artist</option>
        </select>

<!-- Admin Fields -->
<div id="adminFields" class="hidden">
    <input type="text" name="admin_name" placeholder="Admin Name">
</div>

<!-- User Fields -->
<div id="userFields" class="hidden">
    <input type="text" name="user_name" placeholder="Full Name">
    <label for="dob">Date of Birth:</label>
    <input type="date" name="dob">
    <label for="gender">Gender:</label>
    <select name="gender">
        <option value="Female">Female</option>
        <option value="Male">Male</option>
        <option value="Custom">Custom</option>
    </select>
    <input type="tel" name="phone" placeholder="Phone Number" pattern="[0-9]{10}" title="Phone number must be exactly 10 digits">
</div>

<!-- Artist Fields -->
<div id="artistFields" class="hidden">
    <input type="text" name="artist_name" placeholder="Artist Name">
    <textarea name="bio" placeholder="Short Bio"></textarea>
</div>

        <!-- Common Fields (Email and Password at the end) -->
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Sign Up</button>
    </form>

    <p>Already have an account? <a href="login.php">Log in</a></p>
</div>

</body>
</html>