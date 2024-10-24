<?php
session_start();
require('./Database.php'); // Ensure this file returns a valid $connection

if (isset($_POST['LogIn'])) {
    $Username = trim($_POST['Username']);
    $Password = trim($_POST['Password']);

    // Check if username or password is empty
    if (empty($Username) || empty($Password)) {
        echo "<script>alert('Username or Password cannot be empty.');</script>";
        exit;
    }

    // Prepared statement to prevent SQL injection
    $stmt = $connection->prepare("SELECT * FROM tbl4 WHERE Username = ?");
    if (!$stmt) {
        die('Query preparation failed: ' . mysqli_error($connection));
    }

    $stmt->bind_param("s", $Username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc(); // Fetch user data

    // Check if the user exists and directly compare passwords (no hash)
    if ($user && $Password === $user['Password']) {
        // Store user details in the session
        $_SESSION['userId'] = $user['Id'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];

        // Redirect based on the user's role
        $redirectUrl = $user['Role'] === 'admin' ? "Home.php" : "user_dashboard.php";
        echo "<script>alert('Successfully Logged In as {$user['Role']}!'); window.location.href = '$redirectUrl';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid username or password.');</script>";
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: url('BP.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: white;
        }

        * {
            box-sizing: border-box;
        }

        .container {
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            max-width: 400px;
            border-radius: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        h1 {
            margin: 0 0 20px;
            text-align: center;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type=text], input[type=password] {
            width: 100%;
            padding: 10px 40px 10px 10px;
            margin: 5px 0 15px;
            border-radius: 20px;
            border: 1px solid #ccc;
            background: #f1f1f1;
            transition: border-color 0.3s;
        }

        input[type=text]:focus, input[type=password]:focus {
            border-color: #04AA6D;
            outline: none;
        }

        .emoji {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 20px;
        }

        .registerbtn {
            background-color: #04AA6D;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            width: 100%;
            transition: opacity 0.3s;
        }

        .registerbtn:hover {
            opacity: 0.9;
        }

        a {
            color: blue;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<form action="" method="post">
    <div class="container">
        <h1>Log In</h1>
        <p>Please fill in this form.</p>
        <hr>
        <div class="input-wrapper">
            <label for="Username"><b>Username</b></label>
            <input type="text" placeholder="Enter Username" name="Username" id="Username" required>

            <label for="Password"><b>Password</b></label>
            <div class="password-container" style="position: relative;">
                <input type="password" placeholder="Enter Password" name="Password" id="password" required>
                <span class="emoji" id="togglePasswordEmoji" onclick="togglePasswordVisibility()">🤫</span>
            </div>
        </div>
        <hr>
        <p>By registering, you agree to our <a href="#">Terms & Privacy</a>.</p>
        <br>
        <a href="Changepass.php">Forget Password?</a>
        <br>
        <p>Don't have an account? <a href="index.php">Sign Up</a>.</p>
        <button type="submit" name="LogIn" class="registerbtn">Log In</button>
    </div>
</form>

<script>
    function togglePasswordVisibility() {
        const inputField = document.getElementById('password');
        const emoji = document.getElementById('togglePasswordEmoji');
        
        const isPasswordVisible = inputField.type === "text";
        inputField.type = isPasswordVisible ? "password" : "text";
        emoji.textContent = isPasswordVisible ? "🤫" : "😃"; // Toggle emoji
    }
</script>
</body>
</html>
