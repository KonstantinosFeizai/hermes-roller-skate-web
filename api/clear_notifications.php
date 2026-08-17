<?php
// api/clear_notifications.php
// Purpose: Διαγράφει όλες τις ειδοποιήσεις του logged-in χρήστη.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

// Note: clear_notifications accepts POST with optional JSON body

try {
    $stmt = $pdo->prepare("
        DELETE FROM notifications
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);

    echo json_encode([
        'status' => 'success',
        'deleted' => $stmt->rowCount(),
    ]);
} catch (PDOException $e) {
    error_log('clear_notifications error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
