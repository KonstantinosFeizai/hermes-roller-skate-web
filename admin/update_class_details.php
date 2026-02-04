<?php
// update_class_details.php
// Purpose: Update lesson title/location/date (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit(json_encode(['status' => 'error', 'message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'];
$title = trim($data['title']);
$location = trim($data['location']);
$datetime = $data['datetime'];

try {
    // Update lesson record
    $stmt = $pdo->prepare("UPDATE lessons SET title = ?, location = ?, lesson_datetime = ? WHERE id = ?");
    $stmt->execute([$title, $location, $datetime, $id]);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
