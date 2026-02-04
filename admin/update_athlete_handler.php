<?php
// update_athlete_handler.php
// Purpose: Update athlete profile fields (admin only).
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    exit(json_encode(['message' => 'Unauthorized']));
}

// Read JSON payload
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['athlete_id'];

try {
    // Update user record
    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ?, age = ?, region = ? WHERE id = ?");
    $stmt->execute([
        $data['first_name'],
        $data['last_name'],
        $data['phone'],
        $data['age'],
        $data['region'],
        $id
    ]);
    echo json_encode(['status' => 'success', 'message' => 'Ενημερώθηκε!']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => $e->getMessage()]);
}
