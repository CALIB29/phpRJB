<?php
session_start();
require('./Database.php');

if (!isset($_SESSION['userId']) || !isset($_SESSION['Username'])) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['userId'];
$username = $_SESSION['Username'];

// Fetch user data
$sql = "SELECT * FROM tbl4 WHERE Id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$userData = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newPassword = trim($_POST['password']);
    $profilePicture = $_FILES['profilePicture'] ?? null;

    // Validate input
    if (empty($newUsername)) {
        echo "<script>alert('Username cannot be empty.');</script>";
    } else {
        // Update username
        $updateSql = "UPDATE tbl4 SET Username = ? WHERE Id = ?";
        $updateStmt = $connection->prepare($updateSql);
        $updateStmt->bind_param("si", $newUsername, $userId);
        $updateStmt->execute();

        // Update password if provided
        if (!empty($newPassword)) {
            $updatePasswordSql = "UPDATE tbl4 SET Password = ? WHERE Id = ?";
            $updatePasswordStmt = $connection->prepare($updatePasswordSql);
            $updatePasswordStmt->bind_param("si", $newPassword, $userId);
            $updatePasswordStmt->execute();
        }

        // Handle profile picture upload
        if ($profilePicture && $profilePicture['error'] === UPLOAD_ERR_OK) {
            $targetDir = 'uploads/';
            $targetFile = $targetDir . basename($profilePicture['name']);
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Check file type
            if (in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
                move_uploaded_file($profilePicture['tmp_name'], $targetFile);
                $updatePictureSql = "UPDATE tbl4 SET ProfilePicture = ? WHERE Id = ?";
                $updatePictureStmt = $connection->prepare($updatePictureSql);
                $updatePictureStmt->bind_param("si", $profilePicture['name'], $userId);
                $updatePictureStmt->execute();
            } else {
                echo "<script>alert('Invalid file type for profile picture.');</script>";
            }
        }

        echo "<script>alert('Profile updated successfully.');</script>";
        $_SESSION['Username'] = $newUsername; // Update session variable
        header("Location: Update_Profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #222;
            color: #fff;
            background: url(DLM.jpg);
            background-size: cover;
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
        }

        .flex-container {
            margin: auto;
            padding: 30px;
            background-color: rgba(30, 30, 30, 0.9);
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            text-align: center;
            align-items: center;
        }

        h1 {
            color: white;
            margin: 0 0 20px;
            font-size: 2em;
            font-weight: 700;
        }

        input[type="text"], input[type="password"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            background: #444;
            color: #fff;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus, input[type="password"]:focus {
            border-color: #64b5f6;
            outline: none;
        }

        button {
            background-color: #04AA6D;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: opacity 0.3s;
        }

        button:hover {
            opacity: 0.9;
        }

        .go-back {
            background-color: #FF5722; /* Change this color as needed */
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="flex-container">
        <h1>Update Profile</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="New Username" value="<?php echo htmlspecialchars($userData['Username']); ?>" required>
            <input type="password" name="password" placeholder="New Password (leave blank to keep current)">
            <input type="file" name="profilePicture" accept="image/*">
            <button type="submit">Update</button>
        </form>
        <?php if (!empty($userData['ProfilePicture'])): ?>
            <h2>Current Profile Picture:</h2>
            <img src="uploads/<?php echo htmlspecialchars($userData['ProfilePicture']); ?>" alt="Profile Image" style="width: 100%; border-radius: 10px;">
        <?php else: ?>
            <h2>Current Profile Picture:</h2>
            <img src="images/default-avatar.png" alt="Default Profile Image" style="width: 100%; border-radius: 10px;">
        <?php endif; ?>
        <button class="go-back" onclick="window.location.href='Home.php';">Go Back</button>
    </div>
</body>
</html>
