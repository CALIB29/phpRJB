<?php
require('./Database.php');

$editId = $editF = $editM = $editL = "";

if (isset($_POST['edit'])) {
    $editId = $_POST['editId'];

    $query = "SELECT Firstname, Middlename, Lastname FROM tbl3a WHERE Id = $editId";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $editF = $row['Firstname'];
        $editM = $row['Middlename'];
        $editL = $row['Lastname'];
    }
}

if (isset($_POST['update'])) {
    $updateId = $_POST['updateId'];
    $updateF = $_POST['updateF'];
    $updateM = $_POST['updateM'];
    $updateL = $_POST['updateL'];

    // Update the record
    $querryUpdate = "UPDATE tbl3a SET Firstname='$updateF', Middlename='$updateM', Lastname='$updateL' WHERE Id=$updateId";
    $sqlupdate = mysqli_query($connection, $querryUpdate);

    echo '<script>alert("Successfully Update!")</script>';
    echo '<script>window.location.href = "/phpRJB/index.php"</script>';
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

        input[type=text] {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px 0;
            display: inline-block;
            border-radius: 15px;
            background: #f1f1f1;
        }

        input[type=text]:focus{
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

        .update:hover .stars {
            display: block;
            filter: drop-shadow(0 0 10px #fffdef);
        }

        a {
            color: dodgerblue;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Edit Info</h1>
        <br>

        <form action="" method="post">

            <input type="text" name="updateF" placeholder="Enter your Firstname" value="<?php echo ($editF) ?>" required />
            <br></br>
            <input type="text" name="updateM" placeholder="Enter your Middlename" value="<?php echo ($editM) ?>" required />
            <br></br>
            <input type="text" name="updateL" placeholder="Enter your Lastname" value="<?php echo ($editL) ?>" required />
            <br></br>
            <input type="submit" name="update" value="EDIT" id="update" class="btn btn-primary" /><br />
            <input type="hidden" name="updateId" value="<?php echo ($editId) ?>">
        </form>
    </div>

</body>

</html>