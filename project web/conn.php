<?php
$hostname = "127.0.0.1";
$username = "root";
$password = "";
$database = "projectdb";
$conn = mysqli_connect($hostname, $username, $password, $database) or die("Failed to connect to MySQL: ". mysqli_connect_error());
?>

