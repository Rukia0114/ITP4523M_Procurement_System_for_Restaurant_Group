<?php
session_start();
if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
    include('../conn.php');

    //var_dump($_POST);
    $ID = $_POST['itemID'];
    $supplierID = $_SESSION['userID']; // Get the logged-in supplier's ID

    //var_dump($_POST);
    $sql = "SELECT * FROM Item WHERE supplierID = '$supplierID' AND itemID = $ID";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $itemID = $row['itemID'];
        $itemName = $row['itemName'];
        $itemImage = $row['ImageFile'];
        $itemDescription = $row['itemDescription'];
        $price = $row['price'];
        $stockQuantity = $row['stockItemQty'];
    } else {
        // Item not found, handle the error
        // Redirect or display an error message
        header("Location: viewItem.php");
        exit();
    }
} else {
    // Redirect the user to the login page if not logged in
    header("Location:../index.php");
    exit();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Item Information</title>
    <link href="../stylesheet/supplier/form.css" rel="stylesheet" type="text/css">
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
    <script type="text/javascript">
        function confirmMessage() {
            var itemId = document.getElementById('itemId').value;
            var stockQuantity = document.getElementById('stockQuantity').value;
            var price = document.getElementById('price').value;
            var itemDescription = document.getElementById('itemDescription').value;

            if (itemDescription.trim() != "" && stockQuantity >= 0 && price >= 0) {
                if (itemId == '<?php echo $ID; ?>') {
                    var choice = window.confirm("Are you sure you want to edit the item information?");
                    return choice;
                } else {
                    alert("Please don't change the itemID here!\nIf you want to edit another item's information,\nplease go to the view item information page.");
                    return false;
                }
            } else {
                alert("Please enter correct information");
            }
            return false;
        }
    </script>
    <script type="text/javascript">

        function readPic(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('previewImage').style.display = 'inline';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Trigger the function when a file is selected
        document.getElementById('itemImage').addEventListener('change', function() {
            readPic(this);
        });

    </script>
</head>

<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>
<form action="editProcess.php" method="post" enctype="multipart/form-data">

    <h1 class="edit">Edit Item Information</h1>
    <!-- Displaying the Item ID -->
    <label for="itemId">Item ID:</label>
    <input id="itemId" name="itemId" readonly type="text" value="<?php echo $itemID; ?>" style="background-color: #f2f2f2;">

    <label for="itemImage">Item Image:</label>
    <input id="itemImage" name="itemImage" type="file" onchange="readPic(this)">

    <label for="previewImage">Preview Image:</label>
    <img id="previewImage" alt="" src="../images/<?php echo $itemImage; ?>" style="max-width: 200px; max-height: 200px; display: <?php echo $itemImage ? 'inline' : 'none'; ?>">

    <label for="itemDescription">Item Description:</label>
    <textarea id="itemDescription" name="itemDescription"><?php echo $itemDescription; ?></textarea>

    <label for="stockQuantity">Stock Item Quantity:</label>
    <input id="stockQuantity" min="0" name="stockQuantity" title="Range should be at least 0" type="number" value="<?php echo $stockQuantity; ?>">

    <label for="price">Price:</label>
    <input id="price" name="price" title="Range should be at least 1" type="number" value="<?php echo $price; ?>">

    <input type="submit" onclick="return confirmMessage();" value="Confirm Update Item Information">
    <input type="reset" value="Reset">

</form>
</body>
</html>
