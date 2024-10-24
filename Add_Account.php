<?php
require('./Database.php');

if (isset($_POST['Registration'])) {
    $Email = $_POST['Email'];
    $Username = $_POST['Username'];
    $Password = $_POST['Password'];
    $Role = isset($_POST['Role']) ? $_POST['Role'] : 'user';  // Default role is 'user'

    if (!empty($_FILES['ProfilePicture']['tmp_name']) || !empty($_POST['CapturedImage'])) {
        $uploadDir = 'uploads/';
        $fileName = '';

        if (!empty($_POST['CapturedImage'])) {
            $imageData = $_POST['CapturedImage'];
            $decodedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imageData));
            $fileName = md5(time()) . '.png';
            $filePath = $uploadDir . $fileName;
            file_put_contents($filePath, $decodedImage);
        } else if (isset($_FILES['ProfilePicture']) && $_FILES['ProfilePicture']['error'] == UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['ProfilePicture']['tmp_name'];
            $fileName = $_FILES['ProfilePicture']['name'];
            $fileSize = $_FILES['ProfilePicture']['size'];
            $allowedfileExtensions = ['jpg', 'jpeg', 'gif', 'png', 'bmp', 'webp'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (in_array($fileExtension, $allowedfileExtensions) && $fileSize < 10000000) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $uploadFileDir = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $uploadFileDir)) {
                    $fileName = $newFileName;
                } else {
                    echo "Error moving the uploaded file.";
                }
            } else {
                echo "Invalid file type or file size too large.";
            }
        }

        // Storing the plain password (consider hashing for security)
        $stmt = $connection->prepare("INSERT INTO tbl4 (Email, Username, Password, ProfilePicture, Role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $Email, $Username, $Password, $fileName, $Role);

        if ($stmt->execute()) {
            echo '<script>alert("Successfully Registered!");</script>';
            echo '<script>window.location.href = "Add_Account.php";</script>';
        } else {
            echo "Error saving your data.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Account</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background-color: black;
            background-image: url('BP.jpg');
            background-size: cover;
            width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: auto;
        }

        .container {
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.1);
            max-width: 400px;
            align-items: auto;
            backdrop-filter: blur(10px);
            margin: auto;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
        }

        input[type=text],
        input[type=email],
        input[type=password] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            display: inline-block;
            border-radius: 20px;
            background: #f1f1f1;
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

        .flex-registerbtn {
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

        .flex-registerbtn:hover {
            opacity: 1;
        }

        h1,
        p,
        label {
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add Account</h1>
        <p>Please fill in this form.</p>
        <hr>
        <form method="POST" enctype="multipart/form-data" id="registrationForm">
            <label for="ProfilePicture"><b>Profile Picture (Upload or Take a Photo)</b></label>
            <input type="file" name="ProfilePicture" id="ProfilePicture" accept="image/*" capture="camera">
            <br><br>
            <video id="video" autoplay style="width:100%; height:auto;"></video>
            <canvas id="canvas" style="display:none;"></canvas>
            <img id="photo" alt="Captured image will appear here" style="width:100%; height:auto;">
            <br><br>
            <button type="button" class="capture-btn" id="capture">Capture Photo</button>
            <input type="hidden" name="CapturedImage" id="CapturedImage">
            <br><br>

            <label for="Email"><b>Email</b></label>
            <input type="email" placeholder="zxy@gmail.com" name="Email" id="Email" required>

            <label for="Username"><b>Username</b></label>
            <input type="text" placeholder="Username" name="Username" id="Username" required>

            <label for="Password"><b>Password</b></label>
            <input type="password" placeholder="Password" name="Password" id="password" required>
        
            <label for="Role"><b>User Role</b></label>
            <select name="Role" id="Role">
              <option value="user">User</option>
              <option value="admin">Admin</option>
            </select>

            <input type="checkbox" onclick="togglePassword()">Show Password
            <hr>

            <button type="submit" name="Registration" class="flex-registerbtn">Register</button>
            <button class="go-back" onclick="window.location.href='Home.php';">Go Back</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
            } else {
                passwordInput.type = "password";
            }
        }

        // Camera and capture logic
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const photo = document.getElementById('photo');
        const capturedImageInput = document.getElementById('CapturedImage');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => {
                video.srcObject = stream;
            })
            .catch(error => {
                console.error("Error accessing camera: ", error);
            });

        document.getElementById('capture').addEventListener('click', function() {
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);
            const data = canvas.toDataURL('image/png');
            photo.setAttribute('src', data);
            capturedImageInput.value = data;
            video.style.display = 'none';
        });
    </script>
</body>
</html>
