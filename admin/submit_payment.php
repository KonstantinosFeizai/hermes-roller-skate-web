<?php
// submit_payment.php
// Purpose: Record a payment for a user (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'];
$amount = $data['amount'];
$lessons = $data['lessons'];
$notes = $data['notes'] ?? '';

try {
    $stmt = $pdo->prepare("INSERT INTO payments_history (user_id, amount, lessons_added, notes) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $amount, $lessons, $notes]);

    echo json_encode(['status' => 'success', 'message' => 'Η πληρωμή καταχωρήθηκε!']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
