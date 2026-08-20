<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
header('Content-Type: application/json');
restrict_access(['user', 'admin', 'coach']);

$user_id = $_SESSION['user_id'];

try {
    $athletes = $pdo->prepare("
        SELECT id, CONCAT(first_name, ' ', last_name) AS name
        FROM athletes
        WHERE user_id = ? AND is_active = 1
        ORDER BY first_name
    ");
    $athletes->execute([$user_id]);
    $athletes = $athletes->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($athletes as $a) {
        $stmt = $pdo->prepare("
            SELECT l.id, l.title, l.lesson_type, l.lesson_datetime, l.status,
                   loc.name AS location_name, la.attended
            FROM lesson_athletes la
            JOIN lessons l ON la.lesson_id = l.id
            LEFT JOIN locations loc ON l.location_id = loc.id
            WHERE la.athlete_id = ?
            ORDER BY l.lesson_datetime DESC
            LIMIT 60
        ");
        $stmt->execute([$a['id']]);
        $lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $now      = time();
        $upcoming = array_values(array_filter($lessons, fn($l) =>
        strtotime($l['lesson_datetime']) >= $now && $l['status'] !== 'cancelled'));
        $past     = array_values(array_filter($lessons, fn($l) =>
        strtotime($l['lesson_datetime']) < $now || $l['status'] === 'cancelled'));

        $result[] = [
            'athlete_id'   => $a['id'],
            'athlete_name' => $a['name'],
            'upcoming'     => $upcoming,
            'past'         => $past,
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $result]);
} catch (PDOException $e) {
    error_log('get_my_classes error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
