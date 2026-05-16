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
    $stmt = $pdo->query("
        SELECT email, is_active, subscribed_at 
        FROM newsletter_subscribers 
        ORDER BY subscribed_at DESC
    ");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Στατιστικά για τον admin
    $total    = count($subscribers);
    $active   = count(array_filter($subscribers, fn($s) => $s['is_active'] == 1));
    $inactive = $total - $active;

    echo json_encode([
        'success'     => true,
        'subscribers' => $subscribers,
        'stats'       => [
            'total'    => $total,
            'active'   => $active,
            'inactive' => $inactive,
        ]
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Σφάλμα ανάκτησης δεδομένων.'
    ]);
}
