<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
    include('../conn.php');

    //var_dump($_POST);

    if (isset($_POST['itemName'])) {
        $supplierID = $_POST['supplierId'];
        $itemName = $_POST['itemName'];
        $itemDescription = $_POST['itemDescription'];
        $stockQuantity = $_POST['stockQuantity'];
        $price = $_POST['price'];

        // Find the MAX itemID and auto increment by 1 as new itemID
        $sql = "SELECT MAX(itemID) AS maxItemId FROM Item";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        $maxItemId = $row['maxItemId'];
        $itemId = $maxItemId + 1;

        // Check if a file was uploaded successfully
        if (!is_dir('../images/')) {
            mkdir('../images/', 0755, true);
        }

        if ($_FILES['itemImage']['size'] > 500000) {
            header("Location: insert.php?message=error&error_message=The picture is too big, please stay within the size limit (500KB).");
            exit();
        }

        $target_dir = "../images/";
        $target_file = $target_dir . $_FILES["itemImage"]["name"];
        $uploadOk = 1;

        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedExtensions = array("jpg", "png");

        if (!in_array($imageFileType, $allowedExtensions)) {
            echo "Sorry, only JPG, PNG files are allowed.<br>";
            $uploadOk = 0;
        }

        // Rename the file with the itemID and file extension
        $newFileName = $itemId . "." . "png";
        $target_file = $target_dir . $newFileName;

        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.<br>";
        } else {
            if (move_uploaded_file($_FILES["itemImage"]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES["itemImage"]["name"]) . " has been uploaded.<br>";
                echo "The file name is: " . $newFileName . "<br>";
            } else {
                echo "Sorry, there was an error uploading your file.<br>";
            }
        }

        // Prepare and execute the SQL query to insert item information
        $sql = "INSERT INTO Item (itemID, supplierID, itemName, ImageFile, itemDescription, stockItemQty, price)
            VALUES ($itemId, '$supplierID', '$itemName', '$newFileName', '$itemDescription', $stockQuantity, $price);";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            // Item information inserted successfully
            header("Location: insert.php?message=success");
            exit();
        } else {
            // Failed to insert item information
            header("Location: insert.php?message=error&error_message=unsucessful");
            exit();
        }
    }
} else {
    // Redirect the user to the login page if he or she is not logged in
    header("Location:../index.php");
    exit();
}
?>
