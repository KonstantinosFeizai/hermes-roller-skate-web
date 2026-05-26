<?php
// api/get_athletes.php
// Purpose: Επιστρέφει τους athletes του logged-in χρήστη.

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
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
    error_log('get_athletes error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
