<?php
session_start();

if (isset($_SESSION['userID']) && isset($_SESSION['password'])) {
    include('../conn.php');

    $supplierID = $_SESSION['userID']; // Get the logged-in supplier's ID

    //database find item
    $sql = "SELECT * FROM `Item` WHERE `supplierID` = '$supplierID'";
    $result = mysqli_query($conn, $sql);
    $itemData = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
    <title>Delete Item infomation</title>
    <link href="../stylesheet/supplier/table.css" rel="stylesheet" type="text/css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../script/supplier/sortable.js"></script>
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
</head>

<script>
    function reminds(index, itemID) {
        var choice = window.confirm("Are you sure you want to delete the record?");

        if (choice) {
            // Redirect to the delete_item.php script with the itemID as a parameter
            window.location.href = "delete_item.php?itemID=" + itemID;
            alert("Record sccessfully deleted in database");
            let table = document.querySelector("table");
            table.deleteRow(index);
        }
    }

</script>


<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>
<tr>
    <th colspan="7" id="title1"><h1>Delete Item</h1></th>
</tr>
<table class="sortable" id="table">
    <thead>

    <tr>
        <th>Item ID</th>
        <th>Item Name</th>
        <th>Item Image</th>
        <th>Current Status</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($itemData as $index => $item) : ?>
        <tr class="item">
            <td><?php echo $item['itemID']; ?></td>
            <td><?php echo $item['itemName']; ?></td>
            <td><img alt="Item image" src="../images/<?php echo $item['ImageFile']; ?>"></td>
            <td>
                <?php
                $stockItemQty = $item['stockItemQty'];
                $itemID = $item['itemID'];


                //oi = OrdersItem , s = supplier , i = item
                //                $sql = "SELECT oi.orderID, oi.itemID, oi.orderQty, oi.itemPrice, i.itemName, i.ImageFile, i.stockItemQty, i.price, s.supplierID, s.companyName, s.contactName, s.contactNumber, s.address
                //                FROM OrdersItem oi
                //                INNER JOIN Item i ON oi.itemID = i.itemID
                //                INNER JOIN Supplier s ON i.supplierID = s.supplierID
                //                WHERE s.supplierID = '$supplierID'";

                $sql = "SELECT * FROM OrdersItem WHERE itemID = '$itemID'";
                $result = mysqli_query($conn, $sql);
                $orderCount = mysqli_num_rows($result);

                if ($stockItemQty <= 0) { // no stock
                    echo "Out of stock";
                    if ($orderCount > 0) { // no stock but have related order
                        echo " .(Have related order)";
                    }
                } else if ($orderCount > 0) { // have related order
                    echo "Have related order";
                } else { // no related order
                    echo "No related order";
                }
                ?>
            </td>
            <td>
                <?php
                if ($stockItemQty <= 0 && $orderCount <= 0) {
                    echo '<input onclick="reminds(' . $index . ', ' . $itemID . ');" type="submit" value="Delete">';
                } else if ($orderCount > 0) {
                    // Disable the delete button
                    echo '<input disabled type="submit" value="Delete">';
                } else {
                    // Enable the delete button
                    echo '<input onclick="reminds(' . $index . ', ' . $itemID . ');" type="submit" value="Delete">';
                }
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>



</table>

</body>
</html>