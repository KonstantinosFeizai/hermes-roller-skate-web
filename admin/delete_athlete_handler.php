<?php
// delete_athlete_handler.php
// Purpose: Delete an athlete user (admin only).
require_once  __DIR__ . '/../config.php';
session_start();

header('Content-Type: application/json');

// Require admin session
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη ενέργεια.']);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['athlete_id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Δεν δόθηκε ID αθλητή.']);
    exit;
}

try {
    // Delete user record
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);

    echo json_encode(['status' => 'success', 'message' => 'Ο αθλητής διαγράφηκε επιτυχώς.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης: ' . $e->getMessage()]);
}
