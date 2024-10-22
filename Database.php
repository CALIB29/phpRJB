<?php
$host = 'sql306.infinityfree.com';
$user = 'if0_37483383';
$password = 'j9zPQPR5CE5Mr';
$database = 'if0_37483383_bsis3a';

$connection = new mysqli($host, $user, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
// Check
//To check connection
//else {
//    echo"Successfully Connect your Database! ";
//}
?>