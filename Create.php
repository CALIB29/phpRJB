<?php 
require('./Database.php');

if (isset($_POST['create'])) {
    $Fname = $_POST['Fname'];
    $Mname = $_POST['Mname'];
    $Lname = $_POST['Lname'];

    $targetDir = "uploads/"; 
    $profilePic = basename($_FILES["profilePic"]["name"]);
    $targetFilePath = $targetDir . $profilePic;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $uploadOk = 1;

    $check = getimagesize($_FILES["profilePic"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo '<script>alert("File is not an image.");</script>';
        $uploadOk = 0;
    }

    if ($_FILES["profilePic"]["size"] > 5000000) {
        echo '<script>alert("Sorry, your file is too large.");</script>';
        $uploadOk = 0;
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo '<script>alert("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");</script>';
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo '<script>alert("Sorry, your file was not uploaded.");</script>';
    } else {
        if (move_uploaded_file($_FILES["profilePic"]["tmp_name"], $targetFilePath)) {
            $queryCreate = "INSERT INTO tbl3a (Firstname, Middlename, Lastname, ProfilePic) VALUES ('$Fname', '$Mname', '$Lname', '$targetFilePath')";
            $sqlcreate = mysqli_query($connection, $queryCreate);

            if ($sqlcreate) {
                echo '<script>alert("Successfully Created!");</script>';
                echo '<script>window.location.href = "/phpRJB/index.php";</script>';
            } else {
                echo '<script>alert("Database insert failed.");</script>';
            }
        } else {
            echo '<script>alert("Sorry, there was an error uploading your file.");</script>';
        }
    }
}
?>
