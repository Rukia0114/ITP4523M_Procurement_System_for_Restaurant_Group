<?php
/**
 * Database Configuration
 * Centralized database connection settings
 */

return [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 3306),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'database' => env('DB_DATABASE', 'projectdb'),
    'charset' => 'utf8mb4',
    'options' => [
        MYSQLI_SET_CHARSET_DIR => '/usr/share/mysql/charsets',
    ],
];
