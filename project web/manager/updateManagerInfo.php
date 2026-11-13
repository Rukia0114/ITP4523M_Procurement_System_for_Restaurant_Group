<?php
session_start();
$message = ''; // Initialize an empty message

if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
    include('../conn.php');

    //// Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the form inputs
        $password = $_POST['password'];
        $contactNumber = $_POST['contact_number'];
        $warehouseAddress = $_POST['warehouse_address'];

        // Perform the update query using the manager's ID (replace 'manager_id' with the actual column name in your database)
        $updateQuery = "UPDATE PurchaseManager SET password = '$password', contactNumber = '$contactNumber', warehouseAddress = '$warehouseAddress' WHERE purchaseManagerID  = '{$_SESSION['userID']}'";
        $updateResult = mysqli_query($conn, $updateQuery);

        // Set the message based on the query result
        if ($updateResult) {
            $message = 'Information successfully updated';
        } else {
            $message = 'Error updating information';
        }
    }

    // Retrieve the manager's current information from the database
    $managerID = $_SESSION['userID'];
    $selectQuery = "SELECT * FROM PurchaseManager WHERE purchaseManagerID = '$managerID'";
    $result = mysqli_query($conn, $selectQuery);
    $managerInfo = mysqli_fetch_assoc($result);

} else {
    header("Location:../index.php");
    exit();
}
if (isset($_SESSION['update_success']) && $_SESSION['update_success'] == true) {
    echo '<script>alert("Information successfully updated!");</script>';
    unset($_SESSION['update_success']); // Unset the session variable to remove the message on subsequent page loads
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="../bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../stylesheet/manager/main.css" rel="stylesheet">
    <link href="../stylesheet/manager/message.css" rel="stylesheet">
    <script src="../bootstrap/js/bootstrap.bundle.js"></script>
    <script src="https://www.w3schools.com/lib/w3.js" type="text/javascript"></script>

    <title>Update Purchase Manager Information</title>

</head>
<body onload="w3.includeHTML();">
<div w3-include-html="nav.php"></div>
<div class="container">
    <h1 class="my-4">Update Purchase Manager Information</h1>

    <?php if (!empty($message)): ?>
        <p class="message" id="message"><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label class="form-label" for="password">Password</label>
            <input class="form-control" id="password" name="password" type="password" value="<?php echo $managerInfo['password']; ?>" onfocus="hideErrorMessage('message');">
        </div>
        <div class="mb-3">
            <label class="form-label" for="contact_number">Contact Number</label>
            <input class="form-control" id="contact_number" name="contact_number" type="tel" pattern="\d{8}" title="Please enter 8 digits" value="<?php echo $managerInfo['contactNumber']; ?>" onfocus="hideErrorMessage('message');">
        </div>
        <div class="mb-3">
            <label class="form-label" for="warehouse_address">Warehouse Address</label>
            <input class="form-control" id="warehouse_address" name="warehouse_address" type="text" value="<?php echo $managerInfo['warehouseAddress']; ?>" onfocus="hideErrorMessage('message');">
        </div>
        <button class="btn btn-primary" type="submit" onclick="return confirmUpdate();">Update Information</button>
    </form>
</div>

<script>
    function confirmUpdate() {
        var choice = window.confirm("Are you sure you want to update the information?");
        if (choice) { // If true
            return true;
        } else { // If false
            return false;
        }
    }
</script>

    <script>
        function hideErrorMessage(elementId) {
        document.getElementById(elementId).textContent = '';
    }
</script>

<?php
mysqli_close($conn);
?>
</body>
</html>
