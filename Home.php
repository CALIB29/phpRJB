<?php
session_start();
require('./Read1.php');
require('./Database.php');

if (!isset($_SESSION['userId']) || !isset($_SESSION['Username'])) {
    header("Location: Login.php");
    exit();
}

$username = $_SESSION['Username'];
$userId = $_SESSION['userId'];

// Fetch user data to get the role
$sql = "SELECT * FROM tbl4 WHERE Id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $_SESSION['Role'] = $row['Role']; // Store the role in session
}

// Define target directory for profile images
$targetDir = 'uploads/'; // Define your target directory
$ProfilePicturePath = $targetDir . $userId; // Path without extension
$extensions = ['jpg', 'png', 'jpeg', 'gif'];
$ProfilePicture = null;

foreach ($extensions as $extension) {
    // Check if the profile picture exists
    if (file_exists($ProfilePicturePath . '.' . $extension)) {
        $ProfilePicture = $ProfilePicturePath . '.' . $extension;
        break; // Stop searching once we find the first existing picture
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
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
        
        nav {
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 15px 30px;
            background-color: rgba(30, 30, 30, 0.8);
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            margin-top: 1rem;
        }

        .flex-logo a {
            text-decoration: none;
            color: #64b5f6;
            font-size: 1.7em;
            font-weight: 700;
        }

        ul {
            list-style-type: none;
            padding: 0;
            margin: 0;
            display: flex;
            gap: 20px;
        }

        ul li a {
            text-decoration: none;
            color: #B0BEC5;
            transition: color 0.3s, transform 0.3s;
            padding: 5px 10px;
            border-radius: 5px;
        }

        ul li a:hover {
            color: #64b5f6;
            transform: scale(1.05);
        }
        
        h1 {
            color: white;
            margin: 0;
            font-size: 2em;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="flex-logo">
                <a href="Home.php">Raph Webpage</a>
            </div>
            <ul>
                <li><a href="Data.php">Database</a></li>
                <li><a href="Email.php">Email</a></li>
                <li><a href="T&C.php">Terms & Conditions</a></li>
                <li><a href="Update_Profile.php">Update Profile</a></li>
                <li><a href="Login.php">Log Out</a></li>
                <?php if ($_SESSION['Role'] === 'admin'): ?>
                    <li><a href="Add_Account.php">Add Account</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <div class="flex-container">
        <h1>Welcome Master, <?php echo htmlspecialchars($username); ?>!</h1>

        <?php
         $select = mysqli_query($connection, "SELECT * FROM `tbl4` WHERE Id = '$userId'") or die('query failed');
         if(mysqli_num_rows($select) > 0){
            $fetch = mysqli_fetch_assoc($select);
         }
         if($fetch['ProfilePicture'] == ''){
            echo '<img src="images/default-avatar.png" alt="Default Avatar">';
         }else{
            echo '<img src="uploads/'.$fetch['ProfilePicture'].'" alt="Profile Image" style="width:200px; height:auto; border-radius: 10px;">';
         }
      ?>
    </div>
</body>
</html>
