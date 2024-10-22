<?php
session_start();
require('./Read.php'); // Ensure this file includes database connection

// Check if the user is logged in
if (!isset($_SESSION['userId'])) {
    header("Location: Home.php");
    exit();
}

$username = $_SESSION['Username'];
$userId = $_SESSION['userId']; // Assuming you stored userId in session after login

// Fetch user data to get the role
$sql = "SELECT * FROM tbl4 WHERE Id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$results = $stmt->get_result();

if ($results->num_rows > 0) {
    $row = $results->fetch_assoc();
    $_SESSION['Role'] = $row['Role']; // Store the role in session
} else {
    echo "User data not found.";
    exit();
}

$role = $_SESSION['Role']; // Store role in a variable for easier access

// Check if the form was submitted and the user role is admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $role === 'admin') {
    $firstName = $_POST['Fname'];
    $middleName = $_POST['Mname'];
    $lastName = $_POST['Lname'];

    // Handle file upload
    if (isset($_FILES['ProfilePicture']) && $_FILES['ProfilePicture']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploaded_img/'; // Ensure this directory exists and is writable
        $fileTmpPath = $_FILES['ProfilePicture']['tmp_name'];
        $fileName = $_FILES['ProfilePicture']['name'];
        $fileSize = $_FILES['ProfilePicture']['size'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        // Allowed file formats
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < 2000000) { // Limit to 2MB
            // Create a unique file name
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = $uploadDir . $newFileName;

            // Move the uploaded file
            if (move_uploaded_file($fileTmpPath, $uploadFileDir)) {
                // Use prepared statement to insert data into the database
                $stmt = $connection->prepare("INSERT INTO tbln (Firstname, Middlename, Lastname, ProfilePicture) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $firstName, $middleName, $lastName, $newFileName); // 'ssss' indicates string type
                if ($stmt->execute()) {
                    // Redirect or show success message
                    header("Location: Data.php");
                    exit();
                } else {
                    echo "Database insert failed: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "There was an error moving the uploaded file.";
            }
        } else {
            echo "Upload failed. Allowed file types: jpg, gif, png, jpeg. Max size: 2MB.";
        }
    } else {
        echo "No file uploaded or an upload error occurred.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <title>Document</title>
    <style>
        @media print {
            #printButton {
                display: none;
            }
        }

        .printButton {
            width: 140px;
            height: 40px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(to right, #77530a, #ffd277, #77530a, #77530a, #ffd277, #77530a);
            background-size: 250%;
            background-position: left;
            color: #ffd277;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition-duration: 1s;
            overflow: hidden;
        }

        .printButton::before {
            position: absolute;
            content: "Print";
            color: #ffd277;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 97%;
            height: 90%;
            border-radius: 8px;
            transition-duration: 1s;
            background-color: rgba(0, 0, 0, 0.842);
            background-size: 200%;
        }

        .printButton:hover {
            background-position: right;
            transition-duration: 1s;
        }

        .printButton:hover::before {
            background-position: right;
            transition-duration: 1s;
        }

        .printButton:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body>
    <div class="container">

        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <br>

        <?php if ($role === 'admin'): ?>
            <!-- Admin-specific content (CRUD form) -->
            <form action="Create.php" method="post" enctype="multipart/form-data">
                <h3>Create your Info</h3>
                <input type="text" name="Fname" placeholder="Enter your Firstname" required />
                <input type="text" name="Mname" placeholder="Enter your Middlename" required />
                <input type="text" name="Lname" placeholder="Enter your Lastname" required />
                <label for="ProfilePicture">Upload Profile Picture</label>
                <input type="file" name="ProfilePicture" id="ProfilePicture" accept="image/*" required />
                <input type="submit" name="create" value="CREATE" class="btn btn-primary" />
            </form>
        <?php endif; ?>

        <table class="table">
            <tr class="success">
                <th>Id</th>
                <th>Firstname</th>
                <th>Middlename</th>
                <th>Lastname</th>
                <th>Profile Picture</th>
                <?php if ($role === 'admin'): ?>
                    <th>Action</th>
                <?php endif; ?>
            </tr>

            <?php
            // Make sure to fetch $sqlAccount correctly from the database
            while ($results = mysqli_fetch_array($sqlAccount)) {
            ?>
                <tr class="info">
                    <td><?php echo $results['Id'] ?></td>
                    <td><?php echo $results['Firstname'] ?></td>
                    <td><?php echo $results['Middlename'] ?></td>
                    <td><?php echo $results['Lastname'] ?></td>
                    <td><img src="<?php echo htmlspecialchars($results['ProfilePicture']); ?>" alt="Profile Image" style="width:100px; height:auto;"></td>
                    <?php if ($role === 'admin'): ?>
                        <td>
                            <form action="Edit.php" method="post" style="display:inline;">
                                <input type="submit" name="edit" value="EDIT" class="btn btn-info" style="width: 80px">
                                <input type="hidden" name="editId" value="<?php echo $results['Id'] ?>">
                                <input type="hidden" name="editF" value="<?php echo $results['Firstname'] ?>">
                                <input type="hidden" name="editM" value="<?php echo $results['Middlename'] ?>">
                                <input type="hidden" name="editL" value="<?php echo $results['Lastname'] ?>">
                            </form>
                            <form action="Delete.php" method="post" style="display:inline;">
                                <input type="submit" name="delete" value="DELETE" class="btn btn-danger" style="width: 80px">
                                <input type="hidden" name="deleteId" value="<?php echo $results['Id'] ?>">
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php } ?>
        </table>

        <button id="printButton" class="printButton" onclick="window.print()">Print</button>
        <a href="Home.php">Back</a>
    </div>
</body>

</html>
