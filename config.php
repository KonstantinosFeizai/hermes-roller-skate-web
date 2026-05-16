<?php
// config.php
// Purpose: Global configuration for URLs, assets, and database connection.
// Αυτόματη ανίχνευση environment (local vs production) μέσω .env

// ── Environment Detection ────────────────────────────────────────────────────
// Αν υπάρχει .env → production, αλλιώς → local development
if (file_exists(__DIR__ . '/.env')) {
    // Production environment
    $env     = parse_ini_file(__DIR__ . '/.env');
    $host    = $env['DB_HOST'];
    $port    = $env['DB_PORT'] ?? '3306';
    $db      = $env['DB_NAME'];
    $user    = $env['DB_USER'];
    $pass    = $env['DB_PASS'];

    // Production: site στο root του domain
    $BASE_URL = '';
    define('APP_URL', rtrim($env['APP_URL'] ?? 'https://hermesrollerskate.com', '/'));
} else {
    // Development environment (XAMPP local)
    $host    = '127.0.0.1';
    $port    = '3306';
    $db      = 'hermes_rollers_db';
    $user    = 'root';
    $pass    = 'root';

    // Local: site σε subfolder
    $BASE_URL = '/hermesrollerskate';
    define('APP_URL', 'http://localhost/hermesrollerskate');
}

// ── Constants ────────────────────────────────────────────────────────────────

// Absolute filesystem path to the project root
define('PROJECT_ROOT', __DIR__ . '/');

// Default recipient for contact form emails
define('CONTACT_RECIPIENT_EMAIL', 'contact@hermesrollerskate.gr');

// Google Analytics ID
define('GA_ID', (isset($env) ? ($env['GA_ID'] ?? '') : ''));

// ── URL Helpers ──────────────────────────────────────────────────────────────

// Build a clean URL for an internal path (removes .php extension)
function asset(string $path): string
{
    global $BASE_URL;
    $clean_path = str_replace('.php', '', $path);
    return rtrim($BASE_URL, '/') . '/' . ltrim($clean_path, '/');
}

// Build a versioned asset URL for cache busting (?v=timestamp)
function getVersionedAssetUrl(string $path): string
{
    $url         = asset($path);
    $server_path = PROJECT_ROOT . $path;

    if (file_exists($server_path)) {
        $timestamp = filemtime($server_path);
        return $url . (str_contains($url, '?') ? '&' : '?') . 'v=' . $timestamp;
    }

    return $url;
}

// ── Database Connection ──────────────────────────────────────────────────────

$charset = 'utf8mb4';
$dsn     = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed.']);
    exit;
}
