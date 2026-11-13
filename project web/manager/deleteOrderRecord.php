<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../stylesheet/manager/main.css" rel="stylesheet">
    <link href="../stylesheet/manager/message.css" rel="stylesheet">
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <title>View Order Records</title>
</head>

<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>

<div class="container">
    <?php
        session_start();

        // Checking if the user is logged in and has the manager role
        if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
            include('../conn.php');

            // Get the manager's ID
            $managerID = $_SESSION['userID'];

            // Retrieve the orders made by the manager
            $sql = "SELECT * FROM Orders WHERE purchaseManagerID = '$managerID';";
            $result = mysqli_query($conn, $sql);
            $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $order = null;

            // Convert the orders array to JSON format
            $ordersJson = json_encode($orders);

            // Delete order functionality
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                if (isset($_POST['deleteOrder'])) {
                    $orderID = $_POST['orderID'];

                    // Check if the order can be deleted (two days or before the receipt's delivery date)
                    $checkQuery = "SELECT deliveryDate FROM Orders WHERE orderID = '$orderID'";
                    $checkResult = mysqli_query($conn, $checkQuery);
                    $order = mysqli_fetch_assoc($checkResult);
                    $deliveryDate = strtotime($order['deliveryDate']);
                    $currentDate = strtotime(date('Y-m-d'));
                    $difference = ($deliveryDate - $currentDate) / (60 * 60 * 24); // Calculate the difference in days


                    if ($difference >= 2) {
                        // Get itemID with its quantity
                        $itemQuery = "SELECT itemID, orderQty FROM OrdersItem WHERE orderID = '$orderID'";
                        $itemResult = mysqli_query($conn, $itemQuery);
                        $items = mysqli_fetch_all($itemResult);

                        // Delete the order and related records from OrdersItem table
                        $deleteOrdersItemQuery = "DELETE FROM OrdersItem WHERE orderID = '$orderID'";
                        mysqli_query($conn, $deleteOrdersItemQuery);

                        // Delete the order from Orders table
                        $deleteOrderQuery = "DELETE FROM Orders WHERE orderID = '$orderID'";
                        mysqli_query($conn, $deleteOrderQuery);

                        // Update the stock item quantity, use the $items array to get the itemID and quantity
                        foreach ($items as $item) {
                            $itemID = $item[0];
                            $quantity = $item[1];

                            $updateQuery = "UPDATE Item SET stockItemQty = stockItemQty + '$quantity' WHERE itemID = '$itemID'";
                            mysqli_query($conn, $updateQuery);
                        }

                        // Retrieve the updated orders after deletion
                        $updatedOrders = mysqli_query($conn, $sql);
                        $orders = mysqli_fetch_all($updatedOrders, MYSQLI_ASSOC);
                    } else {
                        // Return an error status if the order cannot be deleted
                        http_response_code(400);
                        echo "The order cannot be deleted as the delivery date is less than two days away.";
                        exit();
                    }
                }
            }
        } else {
            // Redirect the user to the login page if not logged in
            header("Location: ../index.php");
            exit();
        }
    ?>

    <h1 class="my-4">Delete Order Records</h1>

    <table class="table">
        <thead class="table-light">
        <tr>
            <th>Order ID</th>
            <th>Order Date and Time</th>
            <th>Delivery Address</th>
            <th>Delivery Date</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody id="orderTableBody">
        <?php foreach ($orders as $order) : ?>
            <tr data-orderid="<?php echo $order['orderID'] ?>">
                <td><?php echo $order['orderID']; ?></td>
                <td><?php echo $order['orderDateTime']; ?></td>
                <td><?php echo $order['deliveryAddress']; ?></td>
                <td><?php echo $order['deliveryDate']; ?></td>
                <td>
                    <button class="btn btn-primary delete-button"
                            data-bs-toggle="modal"
                            data-orderid="<?php echo $order['orderID']; ?>"
                            type="button"
                            data-bs-target="#orderDetailsModal"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal -->
<div class="modal fade" id="orderDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailsModalLabel">Delete Order?</h5>
                <button class="btn-close" data-bs-dismiss="modal" type="button"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this order?<br>All order details will be deleted as well.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">Cancel</button>
                <button class="btn btn-danger" data-bs-dismiss="modal" onclick="deleteOrder()"
                        type="button">Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast align-items-center text-white bg-success border-0" id="successfulDelete" role="alert"
         aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                Order deleted successfully.
            </div>
            <button class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" type="button"></button>
        </div>
    </div>
</div>

<script>
    // Attach event listener to the container element using event delegation
    document.getElementById('orderTableBody').addEventListener('click', function (event) {
        if (event.target.classList.contains('delete-button')) {
            // Get the orderID from the data-orderid attribute of the clicked button
            var orderID = event.target.getAttribute('data-orderid');
            confirmDelete(orderID);
        }
    });

    var orderIDToDelete = null;

    function confirmDelete(orderID) {
        console.log('confirmDelete function called with orderID: ' + orderID);
        orderIDToDelete = orderID;
    }

    function deleteOrder() {
        console.log('deleteOrder function called with orderIDToDelete: ' + orderIDToDelete);
        if (orderIDToDelete) {
            if (confirmDelete) {
                fetch('<?php echo $_SERVER['PHP_SELF']; ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'deleteOrder=1&orderID=' + orderIDToDelete
                }).then(response => {
                    if (response.status === 400) {
                        showToast('The order is too late to be deleted.', "danger");
                    } else {
                        var orderRow = document.querySelector('[data-orderid="' + orderIDToDelete + '"]');
                        orderRow.parentElement.parentElement.remove();
                        showToast('Order deleted successfully.', 'success');
                    }
                }).catch(error => {
                        console.error('Error:', error);
                        showToast('An error occurred while deleting the order. Please try again later.', 'error');
                });
            }
        }
    }

    function showToast(message, type) {
        var toastContainer = document.querySelector('.toast-container');
        var toast = document.createElement('div');
        toast.classList.add('toast', 'align-items-center', 'text-white', 'bg-' + type, 'border-0');
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        var toastContent = document.createElement('div');
        toastContent.classList.add('d-flex', 'justify-content-between');

        var toastBody = document.createElement('div');
        toastBody.classList.add('toast-body');
        toastBody.textContent = message;

        var closeButton = document.createElement('button');
        closeButton.classList.add('btn-close', 'btn-close-white', 'me-2', 'm-auto');
        closeButton.setAttribute('data-bs-dismiss', 'toast');
        closeButton.setAttribute('type', 'button');

        toastContent.appendChild(toastBody);
        toastContent.appendChild(closeButton);
        toast.appendChild(toastContent);

        toastContainer.appendChild(toast);
        var toastInstance = new bootstrap.Toast(toast);
        toastInstance.show();
    }

    function updateOrderList(orders) {
        var orderTableBody = document.getElementById('orderTableBody');
        orderTableBody.innerHTML = '';

        orders.forEach(function (order) {
            var row = document.createElement('tr');
            var orderIDCell = document.createElement('td');
            orderIDCell.textContent = order['orderID'];
            row.appendChild(orderIDCell);

            var orderDateTimeCell = document.createElement('td');
            orderDateTimeCell.textContent = order['orderDateTime'];
            row.appendChild(orderDateTimeCell);

            var deliveryAddressCell = document.createElement('td');
            deliveryAddressCell.textContent = order['deliveryAddress'];
            row.appendChild(deliveryAddressCell);

            var deliveryDateCell = document.createElement('td');
            deliveryDateCell.textContent = order['deliveryDate'];
            row.appendChild(deliveryDateCell);

            var actionCell = document.createElement('td');
            var deleteButton = document.createElement('button');
            deleteButton.classList.add('btn', 'btn-primary', 'delete-button');
            deleteButton.setAttribute('data-bs-target', '#orderDetailsModal');
            deleteButton.setAttribute('data-bs-toggle', 'modal');
            deleteButton.setAttribute('data-orderid', order['orderID']);
            deleteButton.textContent = 'Delete';
            actionCell.appendChild(deleteButton);
            row.appendChild(actionCell);

            orderTableBody.appendChild(row);
        });
    }

    // Execute the updateOrderList function with the initial orders when the page loads
    updateOrderList(<?php echo $ordersJson; ?>);

</script>

</body>
</html>
