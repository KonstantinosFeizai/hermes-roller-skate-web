<?php
// delete_lesson.php
// Purpose: Delete a lesson (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$lesson_id = $data['lesson_id'] ?? 0;

try {
    $stmt = $pdo->prepare("DELETE FROM lessons WHERE id = ?");
    $stmt->execute([$lesson_id]);
    echo json_encode(['status' => 'success', 'message' => 'Η προπόνηση διαγράφηκε.']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
