<?php
    session_start();

    function filterUniqueOrders($orders)
    {
        $uniqueOrders = [];
        foreach ($orders as $order) {
            $niddle = $order['orderID'];
            if (array_key_exists($niddle, $uniqueOrders)) {
                // Concatenate the Company Name
                $uniqueOrders[$niddle]['companyName'] .= ', ' . $order['companyName'];
                // Concatenate the supplierID
                $uniqueOrders[$niddle]['supplierID'] .= ', ' . $order['supplierID'];
                continue;
            }
            $uniqueOrders[$niddle] = $order;
        }

        return $uniqueOrders;
    }

    // Checking and testing
    if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
        include('../conn.php');

        $sql = "SELECT Orders.orderID, Orders.orderDateTime, Orders.deliveryAddress, Orders.deliveryDate,
                   Item.supplierID, Supplier.companyName, Supplier.contactName, Supplier.contactNumber,
                   GROUP_CONCAT(DISTINCT Item.itemID ORDER BY Item.itemID SEPARATOR ', ') as itemIDs,
                   GROUP_CONCAT(DISTINCT Item.itemName ORDER BY Item.itemID SEPARATOR ', ') as itemNames,
                   GROUP_CONCAT(DISTINCT Item.ImageFile ORDER BY Item.itemID SEPARATOR ', ') as imageFiles,
                   GROUP_CONCAT(DISTINCT Item.itemDescription ORDER BY Item.itemID SEPARATOR ', ') as itemDescriptions,
                   GROUP_CONCAT(DISTINCT Item.stockItemQty ORDER BY Item.itemID SEPARATOR ', ') as stockItemQtys,
                   GROUP_CONCAT(DISTINCT Item.price ORDER BY Item.itemID SEPARATOR ', ') as prices,
                   GROUP_CONCAT(DISTINCT OrdersItem.orderQty ORDER BY Item.itemID SEPARATOR ', ') as orderQtys,
                   Orders.purchaseManagerID
        FROM Orders
        INNER JOIN OrdersItem ON Orders.orderID = OrdersItem.orderID
        INNER JOIN Item ON OrdersItem.itemID = Item.itemID
        INNER JOIN Supplier ON Item.supplierID = Supplier.supplierID
        WHERE Orders.purchaseManagerID = '{$_SESSION['userID']}'
        GROUP BY Orders.orderID, Orders.orderDateTime, Orders.deliveryAddress, Orders.deliveryDate,
                 Item.supplierID, Supplier.companyName, Supplier.contactName, Supplier.contactNumber,
                 Orders.purchaseManagerID";

        $result = mysqli_query($conn, $sql);

        // Check for query execution errors
        if (!$result) {
            echo "Error executing the query: " . mysqli_error($conn);
            exit;
        }

        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        $suppliers = array();
        $modalCounter = 0; // Counter for generating unique IDs

        // Remove duplicate orders using orderID
        $orders = filterUniqueOrders($orders);

        foreach ($orders as $order) {
            // Remove order if it is not made by the logged in manager
            if ($order['purchaseManagerID'] != $_SESSION['userID']) {
                unset($orders[$order['orderID']]);
                continue;
            }

            $supplierID = $order['supplierID'];
            if (!isset($suppliers[$supplierID])) {
                $suppliers[$supplierID] = array(
                    'supplierID' => $supplierID,
                    'companyName' => $order['companyName'],
                    'contactName' => $order['contactName'],
                    'contactNumber' => $order['contactNumber'],
                    'items' => array()
                );
            }

            // Explode the concatenated strings into arrays
            $itemIDs = explode(', ', $order['itemIDs']);
            $itemNames = explode(', ', $order['itemNames']);
            $imageFiles = explode(', ', $order['imageFiles']);
            $prices = explode(', ', $order['prices']);
            $orderQtys = explode(', ', $order['orderQtys']);

            // Assuming that all arrays have the same length
            for ($i = 0; $i < count($itemIDs); $i++) {
                // Check if the index exists in the $orderQtys array
                $orderQty = isset($orderQtys[$i]) ? $orderQtys[$i] : '';

                $suppliers[$supplierID]['items'][] = array(
                    'itemID' => $itemIDs[$i],
                    'itemName' => $itemNames[$i],
                    'ImageFile' => $imageFiles[$i],
                    'price' => $prices[$i],
                    'orderQty' => $orderQty,
                    'modalID' => $modalCounter // Assigning unique ID for each modal
                );
            }

            $modalCounter++; // Increment the counter
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
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../stylesheet/manager/main.css" rel="stylesheet">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="../script/supplier/sortable.js"></script>
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>

    <title>View Order Records</title>
</head>

<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>
<div class="container">
    <h1 class="my-4">View Order Records</h1>

    <table class="table sortable">
        <thead class="table-light">
        <tr>
            <th>Order ID</th>
            <th>Supplier ID</th>
            <th>Supplier's Company Name</th>
            <th>Order Date</th>
            <th>Order Time</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($orders as $order) { ?>
            <tr>
                <td><?php echo $order['orderID']; ?></td>
                <td><?php echo $order['supplierID']; ?></td>
                <td><?php echo $order['companyName']; ?></td>
                <td><?php echo date('d-m-Y', strtotime($order['orderDateTime'])); ?></td>
                <td><?php echo date('H:i:s', strtotime($order['orderDateTime'])); ?></td>
                <td>
                    <button class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#orderDetailsModal-<?php echo $order['orderID']; ?>">
                        Details
                    </button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<?php foreach ($orders as $order) { ?>
    <div class="modal fade" id="orderDetailsModal-<?php echo $order['orderID']; ?>" tabindex="-1" role="dialog"
         aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details (Order
                        ID: <?php echo $order['orderID']; ?>)</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php
                        $orderID = $order['orderID'];

                        $supplierSql = "SELECT DISTINCT Supplier.supplierID, Supplier.companyName, Supplier.contactName, Supplier.contactNumber
                                    FROM Item
                                    INNER JOIN OrdersItem ON Item.itemID = OrdersItem.itemID
                                    INNER JOIN Supplier ON Item.supplierID = Supplier.supplierID
                                    WHERE OrdersItem.orderID = '$orderID'";
                        $supplierResult = mysqli_query($conn, $supplierSql);

                        if (!$supplierResult) {
                            echo "Error executing the query: " . mysqli_error($conn);
                            exit;
                        }

                        while ($supplier = mysqli_fetch_assoc($supplierResult)) {
                            $supplierID = $supplier['supplierID'];
                            $companyName = $supplier['companyName'];
                            $contactName = $supplier['contactName'];
                            $contactNumber = $supplier['contactNumber'];

                            // Fetch items for the selected supplier
                            $itemSql = "SELECT Item.ImageFile, Item.itemID, Item.itemName, Item.price, OrdersItem.orderQty
                                    FROM Item
                                    INNER JOIN OrdersItem ON Item.itemID = OrdersItem.itemID
                                    INNER JOIN Supplier ON Item.supplierID = Supplier.supplierID
                                    WHERE OrdersItem.orderID = '$orderID' AND Supplier.supplierID = '$supplierID'";
                            $itemResult = mysqli_query($conn, $itemSql);

                            if (!$itemResult) {
                                echo "Error executing the query: " . mysqli_error($conn);
                                exit;
                            }

                            // Calculate order quantity and total order amount specific to the supplier
                            $supplierQty = 0;
                            $supplierTotalPrice = 0;

                            while ($item = mysqli_fetch_assoc($itemResult)) {
                                $orderQty = $item['orderQty'];
                                $price = $item['price'];

                                $supplierQty += $orderQty;
                                $supplierTotalPrice += $orderQty * $price;
                            }
                            ?>
                            <div>
                                <h5>Supplier ID: <?php echo $supplierID; ?></h5>
                                <table class="table table-light">
                                    <tr>
                                        <th>Order Details</th>
                                        <th>Order Information</th>
                                    </tr>
                                    <tr>
                                        <td>Order ID</td>
                                        <td><?php echo $orderID; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Supplier ID</td>
                                        <td><?php echo $supplierID; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Supplier's Company Name</td>
                                        <td><?php echo $companyName; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Supplier's Contact Name</td>
                                        <td><?php echo $contactName; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Supplier's Contact Number</td>
                                        <td><?php echo $contactNumber; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Order Date</td>
                                        <td><?php echo date('Y-m-d', strtotime($order['orderDateTime'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Order Time</td>
                                        <td><?php echo date('H:i:s', strtotime($order['orderDateTime'])); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Address</td>
                                        <td><?php echo $order['deliveryAddress']; ?></td>
                                    </tr>
                                    <tr>
                                        <td>Delivery Date</td>
                                        <td><?php echo $order['deliveryDate']; ?></td>
                                    </tr>
                                    <?php
                                        $supplierQty = 0;
                                        $supplierTotalPrice = 0;

                                        // Fetch items for the selected supplier
                                        $itemSql = "SELECT Item.ImageFile, Item.itemID, Item.itemName, Item.price, OrdersItem.orderQty
                    FROM Item
                    INNER JOIN OrdersItem ON Item.itemID = OrdersItem.itemID
                    INNER JOIN Supplier ON Item.supplierID = Supplier.supplierID
                    WHERE OrdersItem.orderID = '$orderID' AND Supplier.supplierID = '$supplierID'";
                                        $itemResult = mysqli_query($conn, $itemSql);

                                        if (!$itemResult) {
                                            echo "Error executing the query: " . mysqli_error($conn);
                                            exit;
                                        }

                                        while ($item = mysqli_fetch_assoc($itemResult)) {
                                            $orderQty = $item['orderQty'];
                                            $price = $item['price'];

                                            $supplierQty += $orderQty;
                                            $supplierTotalPrice += $orderQty * $price;

                                            ?>
                                            <tr>
                                                <td>Order Quantity</td>
                                                <td><?php echo $supplierQty; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Total Order Amount</td>
                                                <td><?php echo $supplierTotalPrice; ?></td>
                                            </tr>
                                            <?php
                                        }
                                    ?>
                                </table>
                                <div>
                                    <h5>Order Items</h5>
                                    <table class="table">
                                        <thead class="table-light">
                                        <tr>
                                            <th>Item Image</th>
                                            <th>Item ID</th>
                                            <th>Item Name</th>
                                            <th>Item Price ($)</th>
                                            <th>Quantity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            mysqli_data_seek($itemResult, 0); // Reset the item result pointer

                                            while ($item = mysqli_fetch_assoc($itemResult)) { ?>
                                                <tr>
                                                    <td><img src="../images/<?php echo $item['ImageFile']; ?>" alt="Item Image" width="50" height="50"></td>
                                                    <td><?php echo $item['itemID']; ?></td>
                                                    <td><?php echo $item['itemName']; ?></td>
                                                    <td><?php echo $item['price']; ?></td>
                                                    <td><?php echo $item['orderQty']; ?></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <hr>
                            </div>

                        <?php } ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<script>
    var myModal = document.getElementById('myModal');
    var myInput = document.getElementById('myInput');

    myModal.addEventListener('shown.bs.modal', function () {
        myInput.focus();
    });
</script>
</body>

</html>
