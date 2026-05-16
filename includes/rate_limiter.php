<?php
// rate_limiter.php
// Purpose: IP-based rate limiting backed by the `rate_limits` table.
//
// Usage:
//   require_once PROJECT_ROOT . 'includes/rate_limiter.php';
//
//   if (isRateLimited($pdo, 'login')) {
//       $retry = getRateLimitRetryAfter($pdo, 'login');
//       http_response_code(429);
//       echo json_encode(['status' => 'error', 'message' => '...', 'retry_after' => $retry]);
//       exit;
//   }
//   recordAttempt($pdo, 'login');        // on every attempt  (or only on failure for login)
//   resetRateLimit($pdo, 'login');       // on successful login

// ── Per-action configuration ───────────────────────────────────────────────────
// 'max'    → maximum allowed attempts inside the time window
// 'window' → length of the time window in minutes
const RATE_LIMIT_CONFIG = [
    'login'                => ['max' => 5, 'window' => 15],
    'forgot_password'      => ['max' => 3, 'window' => 60],
    'signup'               => ['max' => 3, 'window' => 60],
    'newsletter_subscribe' => ['max' => 3, 'window' => 60],
    'contact'              => ['max' => 5, 'window' => 60],
];

// ── Public API ─────────────────────────────────────────────────────────────────

/**
 * Returns true if the current IP has exceeded the limit for $action.
 * Automatically clears expired windows so stale rows never block requests.
 */
function isRateLimited(PDO $pdo, string $action): bool
{
    if (!isset(RATE_LIMIT_CONFIG[$action])) {
        return false;
    }

    $cfg = RATE_LIMIT_CONFIG[$action];
    $ip  = _getRateLimitIp();

    // Embed window directly (integer from our own config – no SQL injection risk).
    // INTERVAL ? MINUTE with PDO placeholder is unreliable across MySQL/driver versions.
    $window = (int)$cfg['window'];
    $stmt = $pdo->prepare(
        "SELECT attempts,
                (first_attempt < NOW() - INTERVAL {$window} MINUTE) AS expired
         FROM rate_limits
         WHERE ip_address = ? AND action = ?"
    );
    $stmt->execute([$ip, $action]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return false; // no record yet → first attempt
    }

    // If the window has expired, clean up and allow
    if ($row['expired']) {
        _deleteRateLimit($pdo, $ip, $action);
        return false;
    }

    return (int)$row['attempts'] >= $cfg['max'];
}

/**
 * Record one attempt for the current IP and action.
 * If the previous window has expired the counter resets automatically.
 */
function recordAttempt(PDO $pdo, string $action): void
{
    if (!isset(RATE_LIMIT_CONFIG[$action])) {
        return;
    }

    $ip     = _getRateLimitIp();
    $window = RATE_LIMIT_CONFIG[$action]['window'];

    // Embed window directly (integer from our own config – no SQL injection risk).
    $window = (int)$window;
    $stmt = $pdo->prepare(
        "INSERT INTO rate_limits (ip_address, action, attempts)
         VALUES (?, ?, 1)
         ON DUPLICATE KEY UPDATE
             attempts      = IF(first_attempt < NOW() - INTERVAL {$window} MINUTE, 1, attempts + 1),
             first_attempt = IF(first_attempt < NOW() - INTERVAL {$window} MINUTE, NOW(), first_attempt)"
    );
    $stmt->execute([$ip, $action]);
}

/**
 * Reset the counter for the current IP and action (e.g. after a successful login).
 */
function resetRateLimit(PDO $pdo, string $action): void
{
    _deleteRateLimit($pdo, _getRateLimitIp(), $action);
}

/**
 * Returns the seconds remaining until the rate-limit window expires,
 * or 0 if there is no active window.
 */
function getRateLimitRetryAfter(PDO $pdo, string $action): int
{
    if (!isset(RATE_LIMIT_CONFIG[$action])) {
        return 0;
    }

    $cfg = RATE_LIMIT_CONFIG[$action];
    $ip  = _getRateLimitIp();

    $window = (int)$cfg['window'];
    $stmt = $pdo->prepare(
        "SELECT GREATEST(0, TIMESTAMPDIFF(SECOND, NOW(),
                 DATE_ADD(first_attempt, INTERVAL {$window} MINUTE))) AS retry_after
         FROM rate_limits
         WHERE ip_address = ? AND action = ?"
    );
    $stmt->execute([$ip, $action]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return (int)($row['retry_after'] ?? 0);
}

// ── Internal helpers ───────────────────────────────────────────────────────────

/**
 * Resolve the real client IP.
 * Trusts the Cloudflare header (HTTP_CF_CONNECTING_IP) on production;
 * falls back to REMOTE_ADDR for local/direct connections.
 */
function _getRateLimitIp(): string
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = trim($_SERVER['HTTP_CF_CONNECTING_IP']);
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function _deleteRateLimit(PDO $pdo, string $ip, string $action): void
{
    $pdo->prepare("DELETE FROM rate_limits WHERE ip_address = ? AND action = ?")
        ->execute([$ip, $action]);
}
