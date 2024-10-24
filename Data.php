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
    <title>User Dashboard</title>
    <style>
        body {
            background-color: #f4f4f9;
            font-family: 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        h1, h3 {
            color: #333;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            text-align: center;
        }

        h3 {
            margin-top: 20px;
            margin-bottom: 15px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            transition: border 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="file"]:focus {
            border-color: #007BFF;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn-primary {
            background-color: #007BFF;
            border-color: #007BFF;
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th {
            background-color: #007BFF;
            color: white;
            padding: 10px;
        }

        td {
            background-color: #f9f9f9;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .printButton {
            display: inline-block;
            width: 140px;
            height: 40px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(to right, #77530a, #ffd277);
            color: #ffd277;
            cursor: pointer;
            text-align: center;
            line-height: 40px;
            transition: background-position 0.5s;
            position: relative;
            overflow: hidden;
        }

        .printButton:hover {
            background-position: right;
        }

        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #007BFF;
            text-decoration: none;
        }

        @media print {
            #printButton {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }

            h1 {
                font-size: 2rem;
            }
        }
    </style>
    <script>
        function printUserData(id, firstName, middleName, lastName, profilePicture) {
            const printWindow = window.open('', '_blank', 'width=600,height=400');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>User Info</title>
                        <style>
                            body { font-family: Arial, sans-serif; }
                            h1 { text-align: center; }
                            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                            th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
                        </style>
                    </head>
                    <body>
                        <h1>User Information</h1>
                        <table>
                            <tr><th>ID</th><td>${id}</td></tr>
                            <tr><th>Firstname</th><td>${firstName}</td></tr>
                            <tr><th>Middlename</th><td>${middleName}</td></tr>
                            <tr><th>Lastname</th><td>${lastName}</td></tr>
                            <tr><th>Profile Picture</th><td><img src="${profilePicture}" style="width: 100px; height: auto;"></td></tr>
                        </table>
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    </script>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>

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
                            <button class="printButton" onclick="printUserData('<?php echo $results['Id'] ?>', '<?php echo htmlspecialchars($results['Firstname']) ?>', '<?php echo htmlspecialchars($results['Middlename']) ?>', '<?php echo htmlspecialchars($results['Lastname']) ?>', '<?php echo htmlspecialchars($results['ProfilePicture']) ?>')">PRINT</button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php } ?>
        </table>

        <a href="Home.php" class="back-button">Back</a>
    </div>
</body>
</html>
