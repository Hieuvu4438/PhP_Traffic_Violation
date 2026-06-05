<?php
/**
 * Application configuration — TEMPLATE
 * Copy this file to config.php and fill in real values.
 */

return [
    // Database
    'db_host' => getenv('DB_HOST') ?: 'localhost',
    'db_name' => 'traffic_violation_db',
    'db_user' => 'root',
    'db_pass' => '',
    'db_charset' => 'utf8mb4',

    // Application
    'app_name' => 'Traffic Violation Lookup',
    'app_url' => 'http://localhost',
    'app_version' => '1.0.0',

    // Session
    'session_lifetime' => 86400, // 24 hours
    'session_name' => 'TRAFFIC_SESSION',

    // Upload
    'upload_max_size' => 5 * 1024 * 1024, // 5MB
    'upload_allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    'upload_path' => __DIR__ . '/../public/assets/uploads/',

    // Pagination
    'per_page' => 10,
    'news_per_page' => 9,
];
