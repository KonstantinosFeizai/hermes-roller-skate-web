<?php
// admin/delete_admin_athlete.php
// Soft-deletes an athlete (sets is_active = 0). Admin only.

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');

restrict_access('admin');

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$athlete_id = (int)($data['athlete_id'] ?? 0);

if (!$athlete_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο athlete_id.']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE athletes SET is_active = 0 WHERE id = ?");
    $stmt->execute([$athlete_id]);
    echo json_encode(['status' => 'success', 'message' => 'Ο αθλητής διαγράφηκε.']);
} catch (PDOException $e) {
    error_log('delete_admin_athlete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
