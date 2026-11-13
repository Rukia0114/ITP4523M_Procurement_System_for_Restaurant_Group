<?php
    session_start();

    //checking and testing
    if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
        include('../conn.php');

        $sql = "SELECT * FROM Item";
        $result = mysqli_query($conn, $sql);
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Set the values in the HTML form
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
    <script src="../jquery/jquery-3.7.0.min.js"></script>
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>

    <title>Make Orders</title>
</head>
<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>
<div class="container">
    <h1 class="my-4">Make Orders</h1>
    <form id="myForm" name="myForm" action="processOrder.php" method="post">
        <!-- Purchase Manager ID and Manager Name -->
        <div class="row g-2">
            <div class="col-md">
                <label for="managerUserID">Manager User ID</label>
                <input class="form-control" id="managerUserID" value="<?php echo $_SESSION['userID'] ?>"
                       name="managerUserID" readonly type="text">

            </div>
            <div class="col-md">
                <label for="managerUserName">Manager Username</label>
                <input class="form-control" id="managerUserName" value="<?php echo $_SESSION['managerName'] ?>"
                       name="managerName" readonly type="text">
            </div>
        </div>

        <!-- Order Date & Time and Delivery Date -->
        <div class="row g-2">
            <div class="form-group col-md-6">
                <label for="orderDateTime">Order Date & Time</label>
                <input class="form-control" id="orderDateTime" name="orderDateTime" "<?php echo date('Y-m-d\TH:i'); ?>"
                required type="datetime-local">
            </div>
            <div class="form-group col-md-6">
                <label for="deliveryDate">Delivery Date</label>
                <input class="form-control" id="deliveryDate" name="deliveryDate" min="" required type="date">
            </div>
        </div>

        <!-- Delivery Address -->
        <div class="form-group">
            <label for="deliveryAddress">Delivery Address</label>
            <textarea class="form-control" id="deliveryAddress" name="deliveryAddress" required type="text"><?php echo $_SESSION['warehouseAddress']?></textarea>
        </div>

        <h2 class="my-4">Items</h2>
        <div id="itemsContainer">
            <div class="form-floating mb-3">
                <select class="form-select" id="itemList" required>
                    <option disabled selected value="">Select an Item</option>
                    <?php foreach ($items as $item) { ?>
                        <!--                        if zero stock not show-->
                        <?php if ($item['stockItemQty'] > 0) { ?>
                            <option value="<?php echo $item['itemID']; ?>"><?php echo $item['itemName']; ?></option>
                        <?php } ?>
                    <?php } ?>
                </select>
                <label for="itemList">Item List</label>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4" style="width: auto; max-height: 800px">
                        <img alt="" class="card-img-top" src=""
                             style="max-height: 250px; max-width: 100%; width: min-content; align-self: center">
                        <div class="card-body">
                            <p class="card-title fw-bold"></p>
                            <p class="card-text" id="itemDesc"></p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-4">
                        <div class="input-group mb-3">
                            <span class="input-group-text">Item ID</span>
                            <input class="form-control" id="itemID" value="" readonly type="text">
                            <span class="input-group-text" style="border-left: 0; border-right: 0;">Supplier ID</span>
                            <input class="form-control" id="supplierID" value="" readonly type="text">
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="itemName" placeholder="name" readonly type="text">
                            <label for="itemName">Item Name</label>
                        </div>
                        <div class="form-floating mb-3">
                            <div class="form-group input-group col-md-3 mb-3">
                                <span class="input-group-text">$</span>
                                <input class="form-control" id="itemPrice" name="itemPrice" placeholder="Item Price"
                                       readonly type="number">
                                <span class="input-group-text">.00</span>
                            </div>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" name="itemQuantity" id="itemQuantity" placeholder="quantity"
                                   type="number">
                            <label for="itemQuantity">Enter Item Quantity</label>
                        </div>
                        <div class="form-row">
                            <div class="col-md-12">
                                <button class="btn btn-primary" id="addItemBtn" type="button"
                                        onclick="addItemToTable();">Add Item
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="my-4">Items Lists</h2>
        <div class="form-row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Item ID</th>
                        <th>Supplier ID</th>
                        <th>Item Name</th>
                        <th>Item Quantity</th>
                        <th>Item Price ($HKD)</th>
                        <th>Item Total</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="itemsTableBody" name="itemsTableBody">
                    <input type="hidden" name="itemsTableBody" id="itemsTableBodyInput">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Total Order Amount and Order Quantity-->
        <h2 class="my-4">Total Order Amount</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group input-group">
                    <span class="input-group-text">Before Discount:</span>
                    <span class="input-group-text">HKD</span>
                    <input class="form-control" name="beforeDiscount" id="beforeDiscount" readonly type="number">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group input-group">
                    <span class="input-group-text">After Discount:</span>
                    <span class="input-group-text">HKD</span>
                    <input class="form-control" name="afterDiscount" id="afterDiscount" readonly type="number">
                </div>
            </div>
        </div>


        <!-- Submit and Reset Button -->
        <button class="btn btn-success" type="button" onclick="confirmOrder()">Submit Order</button>
        <button class="btn btn-danger float-end" id="resetBtn" type="reset">Reset</button>

    </form>

</div>

<!-- Toast Notification -->
<div class="toast-container position-fixed top-0 end-0 p-3">
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
    function prepDate() {
        // Set the order date time to the current date time
        var orderDateTime = document.getElementById("orderDateTime");
        var deliveryDate = document.getElementById("deliveryDate");
        var now = new Date();
        now.setHours(now.getHours() + 8);
        orderDateTime.value = now.toISOString().slice(0, 16);
        deliveryDate.min = now.toISOString().slice(0, 10);
    }

    prepDate();

    // Get the selected item from the dropdown and update the item details
    document.getElementById("itemList").addEventListener("change", function () {
        var selectedItemId = this.value;
        var selectedItem = <?php echo json_encode($items); ?>.
        find(item => item.itemID == selectedItemId);

        // replace item information
        if (selectedItem) {
            document.getElementById("itemID").value = selectedItem.itemID;
            document.getElementById("supplierID").value = selectedItem.supplierID;
            document.getElementById("itemName").value = selectedItem.itemName;
            document.getElementById("itemPrice").value = selectedItem.price;
            document.getElementsByClassName("card-title")[0].innerHTML = selectedItem.itemName;
            document.getElementById("itemDesc").innerHTML = selectedItem.itemDescription;

            //image
            var itemImage = document.querySelector(".card-img-top");
            itemImage.src = "../images/" + selectedItem.itemID + ".png";
            itemImage.alt = selectedItem.itemName;
        }
    });

    document.getElementById("orderDateTime").addEventListener("change", () => {
        // Set the minimum date time for the delivery date time
        var orderDateTime = document.getElementById("orderDateTime").value;
        document.getElementById("deliveryDate").min = orderDateTime.slice(0, 10);
    })

    let itemList = [];

    document.getElementById("resetBtn").addEventListener("click", function () {
        // Clear the table body
        var itemsTableBody = document.getElementById("itemsTableBody");
        itemsTableBody.innerHTML = "";

        // Clear dropdown selection
        document.getElementById("itemList").value = "";
        document.getElementsByClassName("card-img-top")[0].src = "";
        document.getElementsByClassName("card-img-top")[0].alt = "";
        document.getElementsByClassName("card-title")[0].innerHTML = "";
        document.getElementsByClassName("card-text")[0].innerHTML = "";


        // Clear the total order amount and total price fields
        document.getElementById("totalOrderAmount").value = "";
        document.getElementById("totalPrice").value = "";

        // Clear date
        prepDate();
    });

    function addItemToTable() {
        var itemQuantity = document.getElementById("itemQuantity").value;

        // Check if an item is selected
        var itemID = document.getElementById("itemID").value;
        if (itemID == "") {
            showToast("Please select an item.", "danger");
            return;
        }

        // Check if item already exists in the table
        if (itemList.some(e => e.itemID === itemID)) {
            // Find the index of the existing item in the itemList
            var existingItemIndex = itemList.findIndex(e => e.itemID === itemID);

            // Access the existing item and update its quantity
            itemList[existingItemIndex].itemQuantity += parseInt(itemQuantity);

            var rows = document.querySelectorAll(".oneItem");
            for (var i = 0; i < rows.length; i++) {
                var row = rows[i];
                var itemIDCell = row.querySelector("td[data-itemid='" + itemID + "']");

                // Update the quantity and item total cells only if they belong to the existing item
                if (itemIDCell) {
                    var quantityCell = row.querySelector(".itemQuantityList");
                    var itemPriceCell = row.querySelector("td:nth-child(5)"); // Select the item price cell
                    var itemTotalCell = row.querySelector("td:nth-child(6)"); // Select the item total cell

                    // Update the quantity cell with the updated quantity
                    quantityCell.textContent = itemList[existingItemIndex].itemQuantity;

                    // Calculate the new item total based on the updated quantity
                    var itemQuantity = parseInt(itemList[existingItemIndex].itemQuantity);
                    var itemPrice = parseFloat(itemPriceCell.textContent);
                    var itemTotal = itemQuantity * itemPrice;

                    // Update the item total cell with the new total
                    itemTotalCell.textContent = itemTotal.toFixed(2);
                    break; // Exit the loop since the item has been found and updated
                }
            }
        } else if (itemQuantity > 0) {
            var supplierID = document.getElementById("supplierID").value;
            var itemName = document.getElementById("itemName").value;
            var itemPrice = document.getElementById("itemPrice").value;

            // Create a new table row
            var newRow = document.createElement("tr");
            newRow.className = "oneItem";


            // Create table cells and set their content
            var itemIDCell = document.createElement("td");
            itemIDCell.name = "itemIDList";
            itemIDCell.textContent = itemID;
            itemIDCell.setAttribute("data-itemid", itemID);


            var supplierIDCell = document.createElement("td");
            supplierIDCell.textContent = supplierID;
            supplierIDCell.name = "supplierIDList";

            var itemNameCell = document.createElement("td");
            itemNameCell.textContent = itemName;


            var itemQuantityCell = document.createElement("td");
            itemQuantityCell.textContent = itemQuantity;
            itemQuantityCell.className = "itemQuantityList";


            var itemPriceCell = document.createElement("td");
            itemPriceCell.textContent = itemPrice;

            var itemTotalCell = document.createElement("td");
            var itemTotal = parseFloat(itemQuantity) * parseFloat(itemPrice);
            itemTotalCell.textContent = itemTotal.toFixed(2);

            // Create a delete button cell
            var deleteButtonCell = document.createElement("td");

            // Create the delete button element
            var deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.className = "btn btn-danger btn-sm";
            deleteButton.addEventListener("click", function () {
                deleteRow(newRow);
            });

            // Append the delete button to the cell
            deleteButtonCell.appendChild(deleteButton);

            // Append all the cells to the row
            newRow.appendChild(itemIDCell);
            newRow.appendChild(supplierIDCell);
            newRow.appendChild(itemNameCell);
            newRow.appendChild(itemQuantityCell);
            newRow.appendChild(itemPriceCell);
            newRow.appendChild(itemTotalCell);
            newRow.appendChild(deleteButtonCell);

            // Append the row to the table body
            var itemsTableBody = document.getElementById("itemsTableBody");
            itemsTableBody.appendChild(newRow);
        } else {
            showToast("Quantity cannot be empty or zero.", "danger");
        }

        // Clear the item quantity input field
        document.getElementById("itemQuantity").value = "";

        // Add the item to the itemList if itemID is not found in the itemList
        if (!itemList.some(e => e.itemID === itemID)) {
            itemList.push({
                itemID: itemID,
                itemQuantity: parseInt(itemQuantity)
            });
        }

        // Calculate total price
        calculateTotalOrderAmount();

        mergeItemListSameItems();
    }

    //delete the item in the table
    function deleteRow(row) {
        var itemsTableBody = document.getElementById("itemsTableBody");
        itemsTableBody.removeChild(row);

        // Delete the item from the itemList
        var itemID = row.getElementsByTagName("td")[0].textContent;
        var itemIndex = itemList.findIndex(item => (item.itemID == itemID));
        itemList.splice(itemIndex, 1);

        // Recalculate total order amount and total price after deleting the row
        calculateTotalOrderAmount()
    }

    //calculate Total Order Amount
    function calculateTotalOrderAmount() {
        var itemsTableBody = document.getElementById("itemsTableBody");
        var rows = itemsTableBody.getElementsByTagName("tr");
        var totalPrice = 0;

        for (var i = 0; i < rows.length; i++) {
            var itemQuantityCell = rows[i].getElementsByTagName("td")[3];
            var itemPriceCell = rows[i].getElementsByTagName("td")[4];
            var itemQuantity = parseInt(itemQuantityCell.textContent);
            var itemPrice = parseFloat(itemPriceCell.textContent);
            var itemTotal = itemQuantity * itemPrice;
            totalPrice += itemTotal;
        }

        var bDiscount = document.getElementById("beforeDiscount");
        var aDiscount = document.getElementById("afterDiscount");

        bDiscount.value = totalPrice.toFixed(2);
        let bDiscountInteger = parseInt(bDiscount.value);

        $.ajax({
            url: "http://127.0.0.1:8080/api/discountCalculator?TotalOrderAmount=" + bDiscountInteger,
            type: "GET",
            dataType: "json",
            success: function (data) {
                aDiscount.value = data.NewOrderAmount.toFixed(2);
            },
            error: function (error) {
                console.error(error);
            }
        })
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

    function mergeItemListSameItems() {
        // Merge itemList items with the same itemID
        itemList = itemList.reduce((acc, curr) => {
            let item = acc.find(item => item.itemID === curr.itemID);
            if (item) {
                item.itemQuantity = parseInt(item.itemQuantity) + parseInt(curr.itemQuantity);
            } else {
                acc.push(curr);
            }
            return acc;
        }, []);
    }

    function confirmOrder() {
        if (itemList.length == 0) {
            showToast("Please add at least one item to the order.", "danger");
            return false;
        }

        if (document.getElementById("deliveryDate").value == "") {
            showToast("Please select a delivery date.", "danger");
            return false;
        }

        if (document.getElementById("deliveryAddress").value == "") {
            showToast("Please enter a delivery address.", "danger");
            return false;
        }

        mergeItemListSameItems();

        let newOrder = {
            "purchaseManagerID": "<?php echo $_SESSION['userID']?>",
            "orderDateTime": document.getElementById("orderDateTime").value,
            "deliveryDate": document.getElementById("deliveryDate").value,
            "deliveryAddress": document.getElementById("deliveryAddress").value,
            "itemList": itemList
        }

        // post to local php file
        $.ajax({
            url: "processOrder.php",
            type: "POST",
            dataType: "JSON",
            data: JSON.stringify(newOrder),
            contentType: "application/json",
            success: function (response) {
                console.log(response);
                if (response.success === false) {
                    showToast("Error creating order.\nReason: " + response.message, "danger");
                } else {
                    showToast("Order successfully created.", "success");
                }
            },
            error: function (error) {
                console.error(error);
                showToast("Error creating order." + error.responseText, "danger");
            }
        });

        return false;
    }
</script>


</body>
</html>
