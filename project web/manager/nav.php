<?php
session_start();
if (isset($_SESSION['userID']) && isset($_SESSION['managerName'])) {
include('../conn.php');

$supplierID = $_SESSION['userID']; // Get the logged-in supplier's ID
$managerName = $_SESSION['managerName'];
}else {
    // Redirect the user to the login page if not logged in
    header("Location:../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>nav</title>
    <link href="../stylesheet/nav.css" rel="stylesheet">
</head>
<body>
<nav>
    <div class="container">
        <ul id="menu" class="nav justify-content-end mb-3">

            <li><a href="makeOrder.php" class="head">Make Order</a></li>
            <li><a href="viewOrder.php" class="head">View Order</a></li>
            <li><a href="updateManagerInfo.php" class="head">Update Information</a></li>
            <li><a href="deleteOrderRecord.php" class="head">Delete Order Records</a></li>
            <li><a href="logout.php" class="head" id="logout">Logout</a></li>
            <li><a id="companyName"><?php echo"$managerName" ?></a></li>
        </ul>
    </div>

</nav>
</body>
</html>
