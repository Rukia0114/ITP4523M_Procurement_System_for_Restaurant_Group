<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['password'])) {
    include('../conn.php');

    $supplierID = $_SESSION['userID'];
    $itemID = $_GET['itemID'];


    $sql = "DELETE FROM Item WHERE itemID = $itemID";

    if (mysqli_query($conn, $sql)) {
        // Deletion successful
        header("Location: Delete.php");
        exit();
    } else {
        // Deletion failed
        echo "Error deleting item: " . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    // Redirect the user to the login page if not logged in
    header("Location:../index.php");
    exit();
}
?>
