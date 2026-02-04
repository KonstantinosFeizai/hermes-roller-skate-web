<?php
// add_athlete_to_class.php
// Purpose: Add a user to a lesson (admin only).
require_once __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$lesson_id = $data['lesson_id'] ?? 0;
$user_id = $data['user_id'] ?? 0;

try {
    // Insert into enrollments (UNIQUE key prevents duplicates)
    $stmt = $pdo->prepare("INSERT INTO lesson_enrollments (lesson_id, user_id) VALUES (?, ?)");
    $stmt->execute([$lesson_id, $user_id]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Αυτός ο αθλητής είναι ήδη στο μάθημα.']);
}
