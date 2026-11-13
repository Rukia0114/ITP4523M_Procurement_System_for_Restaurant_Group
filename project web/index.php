<?php
session_start();

if (isset($_POST['login'])) {
    if (empty($_POST['userID']) || empty($_POST['password'])) {
        $error = "UserID and Password cannot be empty";
    } else {
        include('conn.php');

        //database
        $sql = "SELECT * FROM purchasemanager WHERE purchaseManagerID = '{$_POST['userID']}' AND password = '{$_POST['password']}'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            if ($row) {
                // User is a Purchase Manager, store relevant information in session
                $_SESSION['userID'] = $row['purchaseManagerID'];
                $_SESSION['managerName'] = $row['managerName'];
                $_SESSION['contactNumber'] = $row['contactNumber'];
                $_SESSION['warehouseAddress'] = $row['warehouseAddress'];
                // Redirect to manager page
                header("Location: manager/makeOrder.php");
                exit();
            }
        }

        // Query the Supplier table if user is not found in PurchaseManager table
        $sql = "SELECT * FROM Supplier WHERE supplierID = '{$_POST['userID']}' AND password = '{$_POST['password']}'";
        $result = mysqli_query($conn, $sql);

        if ($result) {
            $row = mysqli_fetch_assoc($result);

            if ($row) {
                // User is a Supplier, store relevant information in session
                $_SESSION['userID'] = $row['supplierID'];
                $_SESSION['password'] = $row['password'];
                $_SESSION['companyName'] = $row['companyName'];
                $_SESSION['contactName'] = $row['contactName'];
                $_SESSION['contactNumber'] = $row['contactNumber'];
                $_SESSION['address'] = $row['address'];
                // Redirect to supplier page
                header("Location: supplier/insert.php");
                exit();
            }
        }

        // If no user is found in both tables, set the error message
        $error = "Invalid userID or password";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="stylesheet/manager/main.css" rel="stylesheet">
    <script src="bootstrap/js/bootstrap.bundle.js"></script>

    <title>Login Page</title>
</head>
<body>

<div class="container">
    <section class="h-100">
        <div class="container h-100">
            <div class="row justify-content-sm-center h-100">
                <div class="col-xxl col-xl col-lg-5 col-md-7 col-sm-9">
                    <div class="text-center my-5">
                        <img src="images/logo.png" alt="logo" width="100">
                    </div>
                    <div class="card shadow-lg">
                        <div class="card-body p-5">
                            <h1 class="fs-4 card-title fw-bold mb-4">Login</h1>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger mb-4"><?php echo $error; ?></div>
                            <?php endif; ?>
                            <form action="" method="post" class="needs-validation" novalidate="" autocomplete="off">
                                <div class="mb-3">
                                    <label class="mb-2 text-muted" for="userID">UserID</label>
                                    <input id="userID" type="text" class="form-control" name="userID" value="" required
                                           autofocus>
                                </div>

                                <div class="mb-3">
                                    <div class="mb-2 w-100">
                                        <label class="text-muted" for="password">Password</label>
                                    </div>
                                    <input id="password" type="password" class="form-control" name="password" required>
                                    <div class="invalid-feedback">
                                        Password is required
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">

                                    <button type="submit" name="login" value="login" class="btn btn-primary ms-auto">
                                        Login
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-5 text-muted">
                        Yummy Restaurant Group Limited
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>
