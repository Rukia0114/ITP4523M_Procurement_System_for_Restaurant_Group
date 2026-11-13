<?php
/**
 * Application Configuration
 */

return [
    'session_lifetime' => 86400, // 24 hours
    'allowed_image_extensions' => ['jpg', 'jpeg', 'png', 'gif'],
    'max_upload_size' => 500000, // 500KB
    'upload_dir' => __DIR__ . '/../images/',
    'debug' => true, // Set to false in production
];
