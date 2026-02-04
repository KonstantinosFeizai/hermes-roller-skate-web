<?php
// remove_athlete_from_class.php
// Purpose: Remove a user from a lesson (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);

try {
    $stmt = $pdo->prepare("DELETE FROM lesson_enrollments WHERE lesson_id = ? AND user_id = ?");
    $stmt->execute([$data['lesson_id'], $data['user_id']]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
