<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
    include('../conn.php');

    $supplierID = $_SESSION['userID']; // Get the logged-in supplier's ID
    $supplierName = $_SESSION['companyName'];

    //database
    $sql = "SELECT * FROM supplier WHERE supplierID = '$supplierID'";
    $result = mysqli_query($conn, $sql);
    $supplierData = mysqli_fetch_assoc($result);

    //Insert successfully
    if (isset($_GET['message'])) {
        $message = $_GET['message'];
        if ($message == 'success') {
            echo "<script>alert('Item information inserted successfully.');</script>"; //show message
            echo "<script>window.location.href = 'insert.php';</script>"; // Redirect to the insert page
            exit();
        } else if ($message == 'error') {
            $error_message = isset($_GET['error_message']) ? $_GET['error_message'] : 'Unknown error occurred.';
            echo "<script>alert('Error occurred);</script>"; //show message
        }
    }
    // ...
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
    <title>Insert a item information</title>
    <link href="../stylesheet/supplier/form.css" rel="stylesheet" type="text/css">
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
    <script type="text/javascript">
        //checking
        function checkingInfo() {
            var supplierID = document.getElementById('supplierId').value;
            var supplierName = document.getElementById('supplierName').value;
            var itemName = document.getElementById('itemName').value;
            var itemDescription = document.getElementById('itemDescription').value;
            var stockQuantity = document.getElementById('stockQuantity').value;
            var price = document.getElementById('price').value;

            if (itemName.trim() != "" && itemDescription.trim() != "" && stockQuantity > 0 && price > 0) {
                if (supplierID == '<?php echo $supplierID; ?>' && supplierName == '<?php echo $supplierName; ?>') {
                    var choice = window.confirm("Are you sure you want to insert the item?");
                    return choice;
                } else {
                    alert("Please enter correct supplier information, dont change supplierID and the supplier Name!");
                    return false;
                }
            } else {
                alert("Please enter correct information");
            }
            return false;
        }
    </script>
    <script type="text/javascript">
        // Function to display the selected image preview
        function readPic(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    document.getElementById('previewImage').src = e.target.result;
                    document.getElementById('previewImage').style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Trigger the function when a file is selected if anything change in the itemImage
        document.getElementById('itemImage').addEventListener('change', function () {
            readURL(this);
        });
    </script>

</head>

<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>

<form action="insertProcess.php" method="post" enctype="multipart/form-data">

    <h1>Insert Item Information</h1>
    <!-- for clicking the text to the box -->
    <label for="supplierId">Supplier ID:</label>
    <input id="supplierId" name="supplierId" value="<?php echo $supplierID ?>" readonly type="text"
           style="background-color: CadetBlue;">

    <label for="supplierId">Company Name:</label>
    <input id="supplierName" name="supplierName" value="<?php echo $supplierName ?>" readonly type="text"
           style="background-color: CadetBlue;">

    <label for="itemName">Item Name:</label>
    <!--    prevent whitespace itemName for checking-->
    <input id="itemName" name="itemName" required type="text" onblur="this.value = this.value.trim()">

    <span>
    <label for="itemImage">Item Image:</label>
    <input id="itemImage" name="itemImage" type="file" onchange="readPic(this)">
    <label for="previewImage">Preview Image:</label>
    <img id="previewImage" src="#" alt="Preview" style="max-width: 200px; max-height: 200px; display: none;">
</span>


    <label for="itemDescription">Item Description:</label>
    <textarea id="itemDescription" name="itemDescription" placeholder="please enter the item description here"
              onblur="this.value = this.value.trim()" required></textarea>

    <label for=" stockQuantity">Stock Item Quantity:</label>
    <input id="stockQuantity" min="1" name="stockQuantity" required title="Quantity should be at least 1"
           type="number">

    <label for="price">Price:</label>
    <input id="price"  name="price" title="Range should be at least 0.01" type="number">

    <input type="submit" onclick="return checkingInfo();" value="Submit Item Information">
    <input type="reset" value="Reset">
</form>
</body>
</html>