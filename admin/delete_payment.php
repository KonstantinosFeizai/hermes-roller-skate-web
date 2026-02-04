<?php
// delete_payment.php
// Purpose: Delete a payment record (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// 1. Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε δικαίωμα πρόσβασης.']);
    exit;
}

// 2. Read JSON payload
$input = file_get_contents("php://input");
$data = json_decode($input, true);

// Validate payment_id
if (!isset($data['payment_id']) || empty($data['payment_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Λείπει το ID πληρωμής.']);
    exit;
}

$payment_id = $data['payment_id'];

try {
    // 3. Delete payment
    $stmt = $pdo->prepare("DELETE FROM payments_history WHERE id = ?");
    $stmt->execute([$payment_id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Η πληρωμή δεν βρέθηκε στη βάση.']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
