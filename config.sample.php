<?php

/**
 * Hermes Rollerskate Academy - Configuration (Sample)
 */

$BASE_URL = '/hermesrollerskate';


define('PROJECT_ROOT', __DIR__ . '/');

function asset(string $path): string
{
    global $BASE_URL;
    return rtrim($BASE_URL, '/') . '/' . ltrim($path, '/');
}

function getVersionedAssetUrl(string $path): string
{
    // 1. we get the base URL using the asset() function
    $url = asset($path);

    // 2. We find the physical path of the file on the server
    $server_path = PROJECT_ROOT . $path;

    if (file_exists($server_path)) {
        // 3. We get the file's last modification time
        $timestamp = filemtime($server_path);

        // 4. We append the timestamp as a query parameter: ?v=timestamp
        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $timestamp;
    }

    // Fallback: if the file doesn't exist, just return the original URL
    return $url;
}


// Check for .env file to determine environment
if (file_exists(__DIR__ . '/.env')) {
    // This is for PRODUCTION environment
    $env = parse_ini_file(__DIR__ . '/.env');
    $host = $env['DB_HOST'];
    $port = $env['DB_PORT'];
    $db = $env['DB_NAME'];
    $user = $env['DB_USER'];
    $pass = $env['DB_PASS'];
} else {
    // This is for DEVELOPMENT (local) environment - Use standard XAMPP/MAMP settings
    // whoever clones the project will use these default settings.
    $host = '127.0.0.1';
    $port = 'your_db_port'; // the most common port is 3306, but you can change it to 3307 if that's what you use.
    $db = 'your_database_name'; // Change this to your local database name
    $user = 'root';
    $pass = ''; // Empty password for local installation
}

$charset = 'utf8mb4';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // On GitHub we let the error show, for debugging.
    // on live production , we would show only a generic message.
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
