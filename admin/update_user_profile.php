<?php
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userId = (int)($data['user_id'] ?? 0);

if (!$userId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit;
}

$firstName  = trim($data['first_name'] ?? '');
$lastName   = trim($data['last_name'] ?? '');
$email      = trim($data['email'] ?? '');
$phone      = trim($data['phone'] ?? '');
$locationId = !empty($data['location_id']) ? (int)$data['location_id'] : null;
$age        = isset($data['age']) && $data['age'] !== '' ? (int)$data['age'] : null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Μη έγκυρο email.']);
    exit;
}

// Check email uniqueness (exclude the current user)
$stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
$stmtCheck->execute([$email, $userId]);
if ($stmtCheck->fetch()) {
    http_response_code(409);
    echo json_encode(['success' => false, 'message' => 'Αυτό το email χρησιμοποιείται από άλλον χρήστη.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE users
        SET first_name = ?, last_name = ?, email = ?, phone = ?, location_id = ?, age = ?
        WHERE id = ?
    ");
    $stmt->execute([$firstName, $lastName, $email, $phone ?: null, $locationId, $age, $userId]);
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
