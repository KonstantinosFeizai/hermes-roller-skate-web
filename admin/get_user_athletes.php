<?php
// admin/get_user_athletes.php
// Returns athletes for a given user_id — admin only.

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');

restrict_access('admin');

$input  = json_decode(file_get_contents('php://input'), true) ?? [];
$userId = (int)($input['user_id'] ?? $_GET['user_id'] ?? 0);

if (!$userId) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο user_id.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            a.id, a.first_name, a.last_name, a.birth_date, a.phone,
            a.region, a.location_id, a.shoe_size, a.shirt_size,
            a.interest_rides, a.interest_races, a.interest_ski,
            a.interest_skating, a.interest_hockey,
            a.amka, a.afm, a.created_at,
            l.name AS location_name
        FROM athletes a
        LEFT JOIN locations l ON a.location_id = l.id
        WHERE a.user_id = ? AND a.is_active = 1
        ORDER BY a.created_at ASC
    ");
    $stmt->execute([$userId]);
    $athletes = $stmt->fetchAll();

    echo json_encode([
        'status'   => 'success',
        'athletes' => $athletes,
    ]);
} catch (PDOException $e) {
    error_log('get_user_athletes error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
