<?php
// ── Database configuration ────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_system');
define('DB_USER', 'student_app');        // your MySQL username
define('DB_PASS', 'student_app'); // your MySQL password
define('DB_PORT', 3306);

// ── Paths ─────────────────────────────────────────────────────
// Nginx document root = /usr/share/nginx/html/student_system
// So the app is served at http://YOUR-IP/  (no subdirectory)
define('APP_DIR',    '/usr/share/nginx/html/student_system');
define('UPLOAD_DIR', APP_DIR . '/uploads/');

// Base URL auto-detects scheme + host — works on any IP or domain
(function () {
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL',   $scheme . '://' . $host);   // no subpath
    define('UPLOAD_URL', BASE_URL . '/uploads/');
})();

define('MAX_UPLOAD',  5 * 1024 * 1024);  // 5 MB
define('SESSION_TTL', 3600);             // 1 hour

// ── DB connection ─────────────────────────────────────────────
function db(): mysqli {
    static $conn = null;
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($conn->connect_error) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'message' => 'Database unavailable: ' . $conn->connect_error,
            ]));
        }
        $conn->set_charset('utf8mb4');
    }
    return $conn;
}

