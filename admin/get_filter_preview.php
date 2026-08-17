<?php
// api/get_filter_preview.php
// Purpose: Preview πόσοι/ποιοι χρήστες θα λάβουν το μήνυμα πριν την αποστολή.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'includes/recipient_helper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

$filters = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    $ids = buildRecipientList($pdo, $filters);

    if (empty($ids)) {
        echo json_encode(['status' => 'success', 'count' => 0, 'recipients' => []]);
        exit;
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("
        SELECT id, COALESCE(first_name, username) AS name, email
        FROM users WHERE id IN ($placeholders)
        ORDER BY first_name, username
    ");
    $stmt->execute($ids);
    $recipients = $stmt->fetchAll();

    echo json_encode([
        'status'     => 'success',
        'count'      => count($recipients),
        'recipients' => $recipients,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
