<?php
/**
 * Application Bootstrap File
 * Initializes core components and sets up error handling
 */

// Load configuration
$config = require __DIR__ . '/config/app.php';

// Set error reporting based on environment
if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// Initialize error handler
ErrorHandler::getInstance();

// Start session with secure settings
if (empty($_SESSION)) {
    session_start([
        'cookie_lifetime' => $config['session_lifetime'],
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'use_strict' => true,
    ]);
}

// Auto-load functions and classes
spl_autoload_register(function ($class) {
    $file = __DIR__ . '/core/' . basename($class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
    
    $file = __DIR__ . '/models/' . basename($class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
    
    $file = __DIR__ . '/controllers/' . basename($class) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Load core classes explicitly
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Validator.php';
require_once __DIR__ . '/core/ErrorHandler.php';
require_once __DIR__ . '/models/PurchaseManager.php';
require_once __DIR__ . '/models/Supplier.php';
require_once __DIR__ . '/models/Item.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/HomeController.php';
