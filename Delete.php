<?php
require('./Database.php');

if(isset($_POST['delete'])){
    $deleteId = $_POST['deleteId'];

    $querryDelete = "DELETE FROM tbl3a WHERE Id = $deleteId";
    $sqldelete = mysqli_query($connection, $querryDelete);

    echo '<script>alert("Successfully Delete!")</script>';
    echo '<script>window.location.href = "/phpRJB/index.php"</script>';

}
?>