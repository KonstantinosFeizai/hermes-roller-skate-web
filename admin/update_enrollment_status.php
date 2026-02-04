<?php
// update_enrollment_status.php
// Purpose: Update attendance or payment status for a lesson enrollment.
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$lesson_id = $data['lesson_id'];
$user_id = $data['user_id'];
$type = $data['type']; // 'attended' ή 'payment'
$value = $data['value']; // true ή false

try {
    if ($type === 'attended') {
        $val = $value ? 1 : 0;
        $stmt = $pdo->prepare("UPDATE lesson_enrollments SET attended = ? WHERE lesson_id = ? AND user_id = ?");
        $stmt->execute([$val, $lesson_id, $user_id]);
    } else if ($type === 'payment') {
        $val = $value ? 'paid' : 'pending';
        $stmt = $pdo->prepare("UPDATE lesson_enrollments SET payment_status = ? WHERE lesson_id = ? AND user_id = ?");
        $stmt->execute([$val, $lesson_id, $user_id]);
    }

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
