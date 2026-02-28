<?php
// config.php
// Purpose: Global configuration for URLs, assets, and database connection.
// This file is critical because it defines PROJECT_ROOT, URL helpers, and the PDO connection.

// Base URL of the app (site root in httpdocs)
$BASE_URL = '';


// Absolute filesystem path to the project root
define('PROJECT_ROOT', __DIR__ . '/');

// Build a URL for an internal path (removes .php for clean URLs)
function asset(string $path): string
{
    global $BASE_URL;

    // 1. Remove the .php extension from the path (if present)
    $clean_path = str_replace('.php', '', $path);

    // 2. Join BASE_URL with the cleaned path
    return rtrim($BASE_URL, '/') . '/' . ltrim($clean_path, '/');
}

// Build a versioned asset URL for cache busting
function getVersionedAssetUrl(string $path): string
{
    // 1. Get the base URL using asset()
    $url = asset($path);

    // 2. Find the physical path of the file on the server
    $server_path = PROJECT_ROOT . $path;

    if (file_exists($server_path)) {
        // 3. Get the file's last modification time
        $timestamp = filemtime($server_path);

        // 4. Append the timestamp as a query parameter: ?v=timestamp
        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $timestamp;
    }

    // Fallback: if the file doesn't exist, return the original URL
    return $url;
}



// Determine environment settings (production if .env exists)
if (file_exists(__DIR__ . '/.env')) {
    // Production environment
    $env = parse_ini_file(__DIR__ . '/.env');
    $host = $env['DB_HOST'];
    $port = $env['DB_PORT'] ?? '3306';
    $db = $env['DB_NAME'];
    $user = $env['DB_USER'];
    $pass = $env['DB_PASS'];
    $APP_URL = $env['APP_URL'] ?? 'http://localhost';
} else {
    // Development environment (local defaults)
    $host = '127.0.0.1';
    $port = '3306';
    $db = 'hermes_rollers_db';
    $user = 'root';
    $pass = 'root';
    $APP_URL = 'http://localhost';
}

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Create the PDO connection (used across the app)
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Fail fast if DB connection is not available
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
