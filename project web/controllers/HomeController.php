<?php
/**
 * Home Controller
 * Handles homepage display and login form rendering
 */

require_once __DIR__ . '/../core/Auth.php';

class HomeController {
    
    public function __construct() {
        $this->auth = Auth::getInstance();
    }

    /**
     * Display the homepage with login form
     */
    public function index(): string {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta content="width=device-width, initial-scale=1.0" name="viewport">
            <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
            <link href="stylesheet/manager/main.css" rel="stylesheet">
            <script src="bootstrap/js/bootstrap.bundle.js"></script>
            <title>Login Page - Restaurant Management System</title>
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
                                    
                                    <?php if (isset($_SESSION['error'])): ?>
                                        <div class="alert alert-danger mb-4">
                                            <?php 
                                                $error = $_SESSION['error'];
                                                unset($_SESSION['error']);
                                                echo htmlspecialchars($error);
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <form action="" method="post" class="needs-validation" novalidate="">
                                        <div class="mb-3">
                                            <label class="mb-2 text-muted" for="userID">UserID</label>
                                            <input id="userID" type="text" class="form-control" name="userID" value="" 
                                                   required autofocus>
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
        <?php
        return ob_get_clean();
    }

    /**
     * Check if user is already logged in (redirect if so)
     */
    public function requireGuest(): void {
        if ($this->auth->isAuthenticated()) {
            $userType = $this->auth->getUserType();
            if ($userType === 'manager') {
                header("Location: manager/makeOrder.php");
            } else {
                header("Location: supplier/Insert.php");
            }
            exit;
        }
    }
}
