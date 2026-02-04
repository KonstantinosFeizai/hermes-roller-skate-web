<?php
// get_newsletter_subscribers.php
// Purpose: Return newsletter subscriber list (admin only).
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

// Admin-only access
restrict_access(['admin']);

// Return JSON responses
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'subscribers' => $subscribers
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Σφάλμα ανάκτησης δεδομένων.'
    ]);
}
