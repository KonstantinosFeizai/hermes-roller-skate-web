<?php
// change_password_handler.php
require_once __DIR__ . '/../config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['status' => 'error', 'code' => 'UNAUTHORIZED', 'message' => 'Unauthorized']));
}

$data = json_decode(file_get_contents("php://input"), true);
$current_pw = $data['current_password'] ?? '';
$new_pw     = $data['new_password'] ?? '';
$confirm_pw = $data['confirm_new_password'] ?? '';
$user_id    = $_SESSION['user_id'];

// 1. Έλεγχος συμπλήρωσης πεδίων
if (empty($current_pw) || empty($new_pw) || empty($confirm_pw)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'REQUIRED_FIELDS_MISSING']);
    exit;
}

// 2. Έλεγχος ελάχιστου μήκους
if (mb_strlen($new_pw) < 8) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'PASSWORD_TOO_SHORT']);
    exit;
}

// 3. Έλεγχος ταύτισης νέων κωδικών
if ($new_pw !== $confirm_pw) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 'PASSWORDS_MISMATCH']);
    exit;
}

try {
    // 4. Ανάκτηση τρέχοντος κωδικού
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current_pw, $user['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'code' => 'INCORRECT_CURRENT']);
        exit;
    }

    // 5. Ενημέρωση κωδικού
    $hashed_pw = password_hash($new_pw, PASSWORD_DEFAULT);
    $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
    $updateStmt->execute([$hashed_pw, $user_id]);

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'code' => 'SERVER_ERROR', 'message' => $e->getMessage()]);
}
