<?php
// admin/delete_payment.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$payment_id = !empty($data['payment_id']) ? (int)$data['payment_id'] : 0;

if (!$payment_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο payment_id.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM payments WHERE id = ?");
    $stmt->execute([$payment_id]);

    if ($stmt->rowCount()) {
        echo json_encode(['status' => 'success', 'message' => 'Η πληρωμή διαγράφηκε.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Η πληρωμή δεν βρέθηκε.']);
    }
} catch (PDOException $e) {
    error_log('delete_payment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
