<?php
    session_start();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if the user is logged in
        if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
            // Include the database connection
            include('../conn.php');

            $postData = file_get_contents('php://input');
            $newOrder = json_decode($postData, true);

            // Extract the variables from the newOrder object
            $purchaseManagerID = $newOrder['purchaseManagerID'];
            $orderDateTime = $newOrder['orderDateTime'];
            $deliveryDate = $newOrder['deliveryDate'];
            $deliveryAddress = $newOrder['deliveryAddress'];
            $itemList = $newOrder['itemList'];

            // Check if all the required variables are set
            if (!isset($purchaseManagerID) || !isset($orderDateTime) || !isset($deliveryDate) || !isset($deliveryAddress) || !isset($itemList)) {
                // Send an error response if any of the required variables are not set
                $response = array('success' => false, 'message' => 'Required variables not set');
                echo json_encode($response);
                exit();
            }

            // Check if all the variables are not empty
            if (empty($purchaseManagerID) || empty($orderDateTime) || empty($deliveryDate) || empty($deliveryAddress) || empty($itemList)) {
                // Send an error response if any of the variables are empty
                $response = array('success' => false, 'message' => 'Required variables are empty');
                echo json_encode($response);
                exit();
            }

            // Check if the itemList is not empty
            if (count($itemList) == 0) {
                // Send an error response if the itemList is empty
                $response = array('success' => false, 'message' => 'Item list is empty');
                echo json_encode($response);
                exit();
            }

            foreach ($itemList as $item) {
                // Get current stock in the db
                $sql = "SELECT itemName, stockItemQty FROM item WHERE itemID = '{$item['itemID']}'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $stockItemQty = $row['stockItemQty'];
                $itemName = $row['itemName'];

                // Check if the stock is sufficient
                if ($stockItemQty < $item['itemQuantity']) {
                    // Send an error response if the stock is insufficient
                    $response = array('success' => false, 'message' => 'Insufficient stock for item: ' . $itemName . " (Current stock: " . $stockItemQty . ")");
                    echo json_encode($response);
                    exit();
                }
            }

            $lastOrderIdResult = mysqli_query($conn, "SELECT orderID FROM orders ORDER BY orderID DESC LIMIT 1");
            $lastOrderIdRow = mysqli_fetch_assoc($lastOrderIdResult);
            $lastOrderId = $lastOrderIdRow['orderID'];

            $orderID = $lastOrderId + 1;
            $sql = "INSERT INTO orders (orderID, purchaseManagerID, orderDateTime, deliveryAddress, deliveryDate) VALUES ('$orderID', '$purchaseManagerID', '$orderDateTime','$deliveryAddress', '$deliveryDate')";
            $result = mysqli_query($conn, $sql);

            // Access the individual item properties if needed
            foreach ($itemList as $item) {
                $itemID = $item['itemID'];
                $itemQuantity = $item['itemQuantity'];

                $itemPriceResult = mysqli_query($conn, "SELECT price FROM item WHERE itemID = '$itemID'");
                $itemPriceRow = mysqli_fetch_assoc($itemPriceResult);
                $itemPrice = $itemPriceRow['price'];

                $sql = "INSERT INTO ordersitem (orderID, itemID, orderQty, itemPrice) VALUES ('$orderID','$itemID', '$itemQuantity', '$itemPrice')";
                $result = mysqli_query($conn, $sql);

                $sql = "UPDATE item SET stockItemQty = stockItemQty - '$itemQuantity' WHERE itemID = '$itemID'";
                $result = mysqli_query($conn, $sql);
            }


            // Send a response back to the JavaScript code
            $response = array('success' => true, 'message' => 'Order processed successfully');
            echo json_encode($response);
        } else {
            // User is not logged in, send an error response
            $response = array('success' => false, 'message' => 'User not logged in');
            echo json_encode($response);
        }
    }
?>
