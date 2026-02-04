<?php
// reset_password_handler.php
// Purpose: Validate reset token and update the user's password.

// Load DB connection
require_once  __DIR__ . '/../config.php';

// Return JSON responses
header('Content-Type: application/json');

// Allow only POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Μη επιτρεπτή μέθοδος.']);
    exit;
}
// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

$raw_token = $data['token'] ?? '';
$new_password = $data['password'] ?? '';

try {
    // 1. Basic validation and token hashing
    if (empty($raw_token) || empty($new_password)) {
        throw new Exception("Λείπουν δεδομένα.");
    }

    // Hash the raw token for lookup
    $token_hash = hash('sha256', $raw_token);

    // 2. Validate token (exists and not expired)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE reset_token_hash = ? AND reset_token_expires_at > NOW()");
    $stmt->execute([$token_hash]);
    $user = $stmt->fetch();

    if (!$user) {
        // Token not found or expired
        throw new Exception("Ο σύνδεσμος ανάκτησης είναι άκυρος ή έχει λήξει.");
    }

    // 3. Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // 4. Update password and clear reset tokens
    $updateStmt = $pdo->prepare("UPDATE users SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL  WHERE id = ?");
    $updateStmt->execute([$hashed_password, $user['id']]);

    // 5. Success response
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Ο κωδικός σας άλλαξε επιτυχώς! Μπορείτε να συνδεθείτε.']);
} catch (PDOException $e) {
    // Database error
    http_response_code(500);
    error_log("Password Reset DB Error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα διακομιστή. Δοκιμάστε αργότερα.']);
} catch (Exception $e) {
    // Validation or token error
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

exit;
