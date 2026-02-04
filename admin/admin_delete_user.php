<?php
// admin_delete_user.php
// Purpose: Delete a user account (admin only).
require_once  __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
session_start();

// Return JSON responses
header('Content-Type: application/json');
// Admin-only access
restrict_access(['admin']);

$data = json_decode(file_get_contents("php://input"), true);
$target_user_id = $data['user_id'] ?? null;

// Prevent deleting yourself
if ($target_user_id == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Δεν μπορείτε να διαγράψετε τον εαυτό σας!']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$target_user_id]);
    echo json_encode(['status' => 'success', 'message' => 'Ο χρήστης διαγράφηκε.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα κατά τη διαγραφή.']);
}
