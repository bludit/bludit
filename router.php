<?php
// PHP built-in server router for Bludit
// Usage: php -S localhost:8000 router.php
if (php_sapi_name() === 'cli-server') {
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
        return false; // Serve static files directly
    }
}
require __DIR__ . '/index.php';
