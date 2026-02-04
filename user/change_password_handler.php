<?php
// change_password_handler.php
// Purpose: API endpoint to change a logged-in user's password.

// Core config
require_once  __DIR__ . '/../config.php';
// Start session
session_start();

// Return JSON responses
header('Content-Type: application/json');

// Require authentication
if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read request payload
$data = json_decode(file_get_contents("php://input"), true);
$current_pw = $data['current_password'] ?? '';
$new_pw = $data['new_password'] ?? '';
$confirm_pw = $data['confirm_new_password'] ?? '';
$user_id = $_SESSION['user_id'];

try {
    // 1. Validate new password confirmation
    if ($new_pw !== $confirm_pw) {
        throw new Exception("Οι νέοι κωδικοί δεν ταιριάζουν.");
    }

    // 2. Fetch current hashed password from DB
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    // 3. Verify current password
    if (!password_verify($current_pw, $user['password'])) {
        throw new Exception("Ο τρέχων κωδικός είναι λανθασμένος.");
    }

    // 4. Hash and update password
    $hashed_pw = password_hash($new_pw, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->execute([$hashed_pw, $user_id]);

    // Success response
    echo json_encode(['status' => 'success', 'message' => 'Ο κωδικός άλλαξε επιτυχώς!']);
} catch (Exception $e) {
    // Validation or DB error
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
