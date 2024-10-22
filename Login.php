<?php
session_start();
require('./Database.php'); // Ensure this file returns a valid $connection

if (isset($_POST['LogIn'])) {
    $Username = trim($_POST['Username']);
    $Password = trim($_POST['Password']);

    // Check if username or password is empty
    if (empty($Username) || empty($Password)) {
        echo "Username or Password cannot be empty.";
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

    // Check if the user exists and verify password
    if ($user && password_verify($Password, $user['Password'])) {
        // Store user details in the session
        $_SESSION['userId'] = $user['Id'];
        $_SESSION['Username'] = $user['Username'];
        $_SESSION['role'] = $user['Role'];

        // Redirect based on the user's role
        if ($user['Role'] === 'admin') {
            echo '<script>alert("Successfully Logged In as Admin!");</script>';
            echo '<script>window.location.href = "Home.php";</script>';
        } else {
            echo '<script>alert("Successfully Logged In!");</script>';
            echo '<script>window.location.href = "user_dashboard.php";</script>';
        }
        exit;
    } else {
        echo '<script>alert("Invalid username or password.");</script>';
    }

    // Close the statement
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styles.css"> 
</head>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        background-color: black;
        background-image: url('BP2.jpg');
        background-size: cover;
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: auto;
    }

    * {
        box-sizing: border-box;
    }

    .container {
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.1);
        max-width: 400px;
        backdrop-filter: blur(10px);
        margin: auto;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        position: center;
    }

    input[type=text],
    input[type=password] {
        width: 100%;
        padding: 10px;
        margin: 5px 0 15px 0;
        display: inline-block;
        border-radius: 20px;
        background: #f1f1f1;
    }

    input[type=text]:focus,
    input[type=password]:focus {
        background-color: #ddd;
        outline: none;
    }

    hr {
        border: 1px solid #f1f1f1;
        margin-bottom: 15px;
    }

    .registerbtn {
        background-color: #04AA6D;
        color: white;
        padding: 12px 16px;
        margin: 8px 0;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        width: 100%;
        opacity: 0.9;
    }

    h1, p, label {
        color: white;
    }

    a {
        color: blue;
    }

    .signin {
        background-color: #f1f1f1;
        text-align: center;
    }
</style>
<body>
<form action="" method="post">
    <div class="container">
        <h1>Log In</h1>
        <p>Please fill in this form.</p>
        <hr>
        <label for="Username"><b>Username</b></label>
        <input type="text" placeholder="Enter Username" name="Username" id="Username" required>

        <label for="Password"><b>Password</b></label>
        <input type="password" placeholder="Enter Password" name="Password" id="password" required>
        <input type="checkbox" onclick="togglePassword()"> Show Password
        <hr>
        <p>By registering, you agree to our <a href="#">Terms & Privacy</a>.</p>
        <br>
        <a href="Changepass.php">Forget Password?</a>
        <br>
        <p>Don't have an account?<a href="index.php">Sign Up</a>.</p>
        <button type="submit" name="LogIn" class="registerbtn">Log In</button>
    </div>
</form>

<script>
    function togglePassword() {
        var passwordInput = document.getElementById("password");
        passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
</script>
</body>
</html>