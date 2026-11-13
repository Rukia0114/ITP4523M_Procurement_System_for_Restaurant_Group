<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
    include('../conn.php');

    $itemID = $_POST['itemId'];
    $itemDescription = $_POST['itemDescription'];
    $stockQuantity = $_POST['stockQuantity'];
    $price = $_POST['price'];

    // Check if a file was uploaded
    if (!empty($_FILES["itemImage"]["name"])) {
        $itemImage = $_FILES['itemImage'];

        $target_dir = "../images/";
        $target_file = $target_dir . $_FILES["itemImage"]["name"];
        $uploadOk = 1;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "png");

        if (!in_array($imageFileType, $allowedExtensions)) {
            echo "Sorry, only JPG, PNG files are allowed.<br>";
            $uploadOk = 0;
        }

        $newFileName = $itemID . "." . "png";
        $target_file = $target_dir . $newFileName;

        move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file);

        $sql = "UPDATE Item SET itemDescription = '$itemDescription', stockItemQty = '$stockQuantity', price = '$price', imageFile ='$newFileName' WHERE itemID = '$itemID'";
    } else {
        // No file uploaded, update the item information without changing the imageFile column
        $sql = "UPDATE Item SET itemDescription = '$itemDescription', stockItemQty = '$stockQuantity', price = '$price' WHERE itemID = '$itemID'";
    }

    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);

    header("Location: viewItem.php");
    exit();
} else {
    // Redirect the user to the login page if not logged in
    header("Location:../index.php");
    exit();
}
