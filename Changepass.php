<?php
require('./Database.php');

$editId = $editU = "";

if (isset($_POST['edit'])) {
    $editId = $_POST['editId'];
    
    // Select the record for the given ID
    $stmt = $connection->prepare("SELECT Username, Password FROM tbl WHERE Id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $row = $result->fetch_assoc();
        $editU = $row['Username'];
    }
}

if (isset($_POST['update'])) {
    $updateId = $_POST['updateId'];
    $updateU = $_POST['updateU'];
    $newPassword = $_POST['newPassword'];

    // Hash the new password before saving it to the database
    $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

    // Update the record in the database
    $stmtUpdate = $connection->prepare("UPDATE tbl SET Username = ?, Password = ? WHERE Id = ?");
    $stmtUpdate->bind_param("ssi", $updateU, $hashedPassword, $updateId);
    $sqlupdate = $stmtUpdate->execute();

    if ($sqlupdate) {
        echo '<script>alert("Successfully Updated!")</script>';
        echo '<script>window.location.href = "/phpRJB/Login.php"</script>'; // Redirect after update
    } else {
        echo '<script>alert("Update failed. Please try again.")</script>';
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
    <title>Edit Record</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: black;
            background-image: url('BG.png');
            background-size: cover;
            width: 100%;
            height: 10vh;
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

        input[type=text], input[type=email], input[type=password] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            display: inline-block;
            border-radius: 15px;
            background: #f1f1f1;
        }

        input[type=text]:focus, input[type=email]:focus, input[type=password]:focus {
            background-color: #ddd;
            outline: none;
        }

        hr {
            border: 1px solid #f1f1f1;
            margin-bottom: 15px;
        }

        .update {
            background-color: #04AA6D;
            color: white;
            padding: 12px 16px;
            margin: 8px 0;
            border: none;
            cursor: pointer;
            width: 100%;
            opacity: 0.9;
        }

        .update:hover {
            opacity: 1;
        }

        h1{
            color: black;
        }

        p{
            color: black;
        }

        a {
            color: dodgerblue;
        }

        .signin {
            background-color: #f1f1f1;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Info</h1>
        <br>
        <form action="" method="post">
            <input type="text" name="updateU" placeholder="Enter your Username" value="<?php echo htmlentities($editU) ?>" required/>
            <br><br>
            <input type="password" name="newPassword" placeholder="Enter new Password" required/>
            <br><br>
            <input type="submit" name="update" value="Update" id="update" class="btn btn-primary" /><br/>
            <input type="hidden" name="updateId" value="<?php echo htmlentities($editId) ?>">
        </form>
    </div>
</body>
</html>
