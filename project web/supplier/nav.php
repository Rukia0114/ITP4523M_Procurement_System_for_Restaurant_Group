<?php
session_start();
if (isset($_SESSION['userID']) && isset($_SESSION['companyName'])) {
include('../conn.php');

$supplierID = $_SESSION['userID']; // Get the logged-in supplier's ID
$supplierName = $_SESSION['companyName'];
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

            <li><a href="Insert.php" class="head">Insert Item</a></li>
            <li><a href="viewItem.php" class="head">View Item</a></li>
            <li><a href="Report.php" class="head">Report</a></li>
            <li><a href="Delete.php" class="head">Delete Item Records</a></li>
            <li><a href="logout.php" class="head" id="logout">Logout</a></li>
            <li><a id="Name"><?php echo"$supplierName" ?></a></li>
        </ul>
    </div>

</nav>
</body>
</html>
