<?php
session_start();
require('./Database.php');

// Check if user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['userId'];

if (isset($_POST['changePassword'])) {
    $username = $_POST['username'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Fetch current password and username from the database
    $stmt = $connection->prepare("SELECT Password, Username FROM tbl4 WHERE Id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $dbPassword = $row['Password']; // Remove hashing
        $dbUsername = $row['Username'];

        // Verify the current password
        if ($currentPassword === $dbPassword) { // Direct comparison
            // Check if the username matches the one in the database
            if ($username === $dbUsername) {
                // Check if the new password and confirm password match
                if ($newPassword === $confirmPassword) {
                    // Update the password in the database without hashing
                    $updateStmt = $connection->prepare("UPDATE tbl4 SET Password = ? WHERE Id = ?");
                    $updateStmt->bind_param("si", $newPassword, $userId);

                    if ($updateStmt->execute()) {
                        echo '<script>alert("Password successfully changed!");</script>';
                        echo '<script>window.location.href = "Login.php";</script>';
                    } else {
                        echo "Error updating the password.";
                    }

                    $updateStmt->close();
                } else {
                    echo "New password and confirm password do not match.";
                }
            } else {
                echo "Username does not match.";
            }
        } else {
            echo "Incorrect current password.";
        }
    } else {
        echo "User not found.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: black;
            background-image: url('BP.jpg');
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            max-width: 400px;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }

        h1 {
            color: white;
        }

        label {
            color: white;
        }

        .password-container {
            position: relative;
            margin-bottom: 15px;
        }

        input[type=password], input[type=text] {
            width: 100%;
            padding: 10px 40px 10px 10px;
            margin: 5px 0;
            display: inline-block;
            background: #f1f1f1;
            border-radius: 20px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .emoji {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-size: 20px;
        }

        .change-password-btn {
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

        .change-password-btn:hover {
            opacity: 1;
        }
    </style>
    <script>
        function togglePasswordVisibility(inputId, emojiId) {
            const inputField = document.getElementById(inputId);
            const emoji = document.getElementById(emojiId);
            if (inputField.type === "password") {
                inputField.type = "text";
                emoji.textContent = "😃"; // Happy emoji when password is shown
            } else {
                inputField.type = "password";
                emoji.textContent = "😐"; // Neutral emoji when password is hidden
            }
        }
    </script>
</head>
<body>

<div class="container">
    <h1>Change Password</h1>
    <form method="POST" action="">
        <label for="username"><b>Username</b></label>
        <input type="text" name="username" id="username" placeholder="Enter your username" required>

        <label for="currentPassword"><b>Current Password</b></label>
        <div class="password-container">
            <input type="password" name="currentPassword" id="currentPassword" placeholder="Enter current password" required>
            <span id="emojiCurrent" class="emoji" onclick="togglePasswordVisibility('currentPassword', 'emojiCurrent')">😐</span>
        </div>

        <label for="newPassword"><b>New Password</b></label>
        <div class="password-container">
            <input type="password" name="newPassword" id="newPassword" placeholder="Enter new password" required>
            <span id="emojiNew" class="emoji" onclick="togglePasswordVisibility('newPassword', 'emojiNew')">😐</span>
        </div>

        <label for="confirmPassword"><b>Confirm New Password</b></label>
        <div class="password-container">
            <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm new password" required>
            <span id="emojiConfirm" class="emoji" onclick="togglePasswordVisibility('confirmPassword', 'emojiConfirm')">😐</span>
        </div>

        <button type="submit" name="changePassword" class="change-password-btn">Change Password</button>
        <a href="Login.php">Go back</a>
        
    </form>
</div>

</body>
</html>
