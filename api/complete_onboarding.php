<?php
// api/complete_onboarding.php
// Purpose: Marks the user's onboarding as completed.
//          Called at the final success step of the onboarding wizard.

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET onboarding_completed = 1 WHERE id = ?");
    $stmt->execute([$userId]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log('complete_onboarding error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
