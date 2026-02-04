<?php
// profile_update_handler.php
// Purpose: API endpoint to update a logged-in user's username and email.

// Core config
require_once __DIR__ . '/../config.php';
// Start session
session_start();

// Return JSON responses
header('Content-Type: application/json');

// Require authentication
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
    exit;
}

// Read request payload
$data = json_decode(file_get_contents("php://input"), true);
$new_username = trim($data['username'] ?? '');
$new_email = trim($data['email'] ?? '');
$user_id = $_SESSION['user_id'];

try {
    // 1. Basic validation
    if (empty($new_username) || empty($new_email)) {
        throw new Exception("Όλα τα πεδία είναι υποχρεωτικά.");
    }

    // 2. Check if email is already used by another user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->execute([$new_email, $user_id]);
    if ($stmt->fetch()) {
        throw new Exception("Αυτό το email χρησιμοποιείται ήδη από άλλον λογαριασμό.");
    }

    // 3. Update user record in DB
    $updateStmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $updateStmt->execute([$new_username, $new_email, $user_id]);

    // 4. Update session (so navbar updates immediately)
    $_SESSION['username'] = $new_username;

    // Success response
    echo json_encode(['status' => 'success', 'message' => 'Το προφίλ ενημερώθηκε επιτυχώς!']);
} catch (Exception $e) {
    // Validation or DB error
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
