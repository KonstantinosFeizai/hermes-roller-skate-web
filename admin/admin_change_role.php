<?php
// admin_change_role.php
// Purpose: Change a user's role (admin only).
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
session_start();

// Return JSON responses
header('Content-Type: application/json');
// Security: admins only
restrict_access(['admin']);

$data = json_decode(file_get_contents("php://input"), true);
$target_user_id = $data['user_id'] ?? null;
$new_role = $data['new_role'] ?? '';

// Prevent self role change
if ($target_user_id == $_SESSION['user_id']) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Δεν μπορείτε να αλλάξετε τον δικό σας ρόλο!']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$new_role, $target_user_id]);
    echo json_encode(['status' => 'success', 'message' => 'Ο ρόλος ενημερώθηκε.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης.']);
}
