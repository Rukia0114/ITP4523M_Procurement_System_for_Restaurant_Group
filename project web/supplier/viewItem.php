<?php
session_start();
if (isset($_SESSION['userID']) && isset($_SESSION['password'])) {
    include('../conn.php');

    $supplierID = $_SESSION['userID'];

    // //database Items belonging to the current supplier and their related order IDs
//    $query = "SELECT Item.itemID, Item.itemName, Item.ImageFile, Item.itemDescription, Item.price, Item.stockItemQty, GROUP_CONCAT(OrdersItem.orderID) AS itemOrders
//              FROM Item
//              INNER JOIN OrdersItem ON OrdersItem.itemID = Item.itemID
//              WHERE Item.supplierID = '$supplierID'
//              GROUP BY Item.itemID";
    $query = "SELECT Item.itemID, Item.itemName, Item.ImageFile, Item.itemDescription, Item.price, Item.stockItemQty, GROUP_CONCAT(OrdersItem.orderID) AS itemOrders
          FROM Item
          LEFT JOIN OrdersItem ON OrdersItem.itemID = Item.itemID
          WHERE Item.supplierID = '$supplierID'
          GROUP BY Item.itemID";
    $result = mysqli_query($conn, $query);


    // Display item information in table rows
    $tableRows = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $itemID = $row['itemID'];
        $itemName = $row['itemName'];
        $itemImage = $row['ImageFile'];
        $itemDescription = $row['itemDescription'];
        $price = $row['price'];
        $stockQuantity = $row['stockItemQty'];
        $itemOrders = $row['itemOrders'];
        //depend on there any order and show the message
        $showitemOrders = "No related order";
        if ($itemOrders) {
            $showitemOrders = "Order $itemOrders";
        }

        $tableRows .= <<<EOD
        <tr class="item">
            <td class="itemID">$itemID</td>
            <td>$itemName</td>
            <td><img alt="Item image" src="../images/$itemImage"></td>
            <td>$itemDescription</td>
            <td>$price</td>
            <td>$stockQuantity</td>
            <td>$showitemOrders</td>
            <td><input id="edit" type="submit" value="Edit information"></td>
        </tr>
EOD;
    }

    // Free the result variable
    mysqli_free_result($result);

    // Close the database connection
    mysqli_close($conn);
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
    <title>View Item information</title>
    <link href="../stylesheet/supplier/table.css" rel="stylesheet" type="text/css">
    <script src="../script/supplier/sortable.js"></script>
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
</head>

<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>

<form action="edit.php" method="POST" id="editForm" enctype="multipart/form-data">
    <h1>View Item Order information</h1>
    <table class="sortable" id="table">
        <thead>
        <tr>
            <th>Item ID</th>
            <th>Item Name</th>
            <th>Item Image</th>
            <th>Item Description</th>
            <th>Price (HKD$)</th>
            <th>Stock Item Quantity</th>
            <th>Related item's Order</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php echo $tableRows; ?>
        </tbody>
    </table>
    <input type="hidden" name="itemID" id="itemIDInput">
</form>

<script>
    // Get all the "Edit information" buttons
    var editButtons = document.querySelectorAll('.item input[type="submit"]');

    // Attach a click event listener to each button
    editButtons.forEach(function (button) {
        button.addEventListener('click', function (event) {
            // Prevent form submission
            event.preventDefault();

            // Get the itemID from the same row as the clicked button
            var itemID = button.parentNode.parentNode.cells[0].innerText;

            // Set the itemID value in the hidden input field
            document.getElementById('itemIDInput').value = itemID;

            // Submit the form
            document.getElementById('editForm').submit();
        });
    });
</script>
</body>

</html>

