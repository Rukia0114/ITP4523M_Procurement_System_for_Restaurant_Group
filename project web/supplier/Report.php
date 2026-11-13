<?php
session_start();
if (isset($_SESSION['userID']) && isset($_SESSION['password'])) {
    include('../conn.php');

    $supplierID = $_SESSION['userID'];

    // Query to item report
    $sql = "SELECT item.itemID, itemName, ImageFile, OrdersItem.itemPrice, SUM(IFNULL(orderQty, 0)) AS totalQuantity, SUM(IFNULL(orderQty, 0) * OrdersItem.itemPrice) AS totalSalesAmount
            FROM item
            LEFT JOIN OrdersItem ON item.itemID = OrdersItem.itemID
            WHERE item.supplierID = '$supplierID'
            GROUP BY item.itemID";

    $result = mysqli_query($conn, $sql);

    // If Query successfully
    if ($result) {
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Item Report</title>
            <link href="../stylesheet/supplier/table.css" rel="stylesheet" type="text/css">
            <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
            <script src="../script/supplier/sortable.js"></script>

        </head>

        <body onload="w3.includeHTML();">
        <div w3-include-html="nav.php"></div>

        <tr>
            <th colspan="5">
                <h1>Report</h1>
            </th>
        </tr>

        <table class="sortable" id="table">
            <tr>
                <th>Item ID</th>
                <th>Item Name</th>
                <th>Item Image</th>
                <th>Total Number Sold</th>
                <th>Total Sales Amount ($HKD)</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr class="item">
                    <td class="itemID"><?php echo $row['itemID']; ?></td>
                    <td><?php echo $row['itemName']; ?></td>
                    <td><img alt="<?php echo $row['itemName']; ?>"
                             src="../images/<?php echo $row['ImageFile']; ?>"></td>
                    <td><?php echo $row['totalQuantity']; ?></td>
                    <td>$<?php echo number_format($row['totalSalesAmount'], 2); ?></td>
                </tr>
            <?php } ?>

        </table>

        <tr>
            <th colspan="5">
                <h1>Order item</h1>
            </th>
        </tr>

        <table class="sortable" id="table">
            <tr>
                <th>Item ID</th>
                <th>Order ID</th>
                <th>Number of Order Items Sold</th>
                <th>Order Sales Amount ($HKD)</th>
            </tr>

            <?php
            $query = "SELECT oi.orderID, oi.itemID, SUM(oi.orderQty) AS totalQuantity, SUM(oi.orderQty * oi.itemPrice) AS totalSalesAmount
            FROM OrdersItem AS oi
            INNER JOIN Item AS i ON oi.itemID = i.itemID
            WHERE i.supplierID = '$supplierID'
            GROUP BY oi.orderID, oi.itemID";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr class="item">
                    <td class="itemID"><?php echo $row['itemID']; ?></td>
                    <td class="orderID"><?php echo $row['orderID']; ?></td>
                    <td><?php echo $row['totalQuantity']; ?></td>
                    <td>$<?php echo number_format($row['totalSalesAmount'], 2); ?></td>
                </tr>
            <?php } ?>
        </table>


        </body>

        </html>

        <?php
    } else {
        // if error query
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Redirect the user to the login page if not logged in
    header("Location:../index.php");
    exit();
}
?>
