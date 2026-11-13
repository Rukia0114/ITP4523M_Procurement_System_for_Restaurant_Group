<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Close the database connection
include('../conn.php');
mysqli_close($conn);

// Redirect the user to the login page
header("Location: ../index.php");
exit();
?>
