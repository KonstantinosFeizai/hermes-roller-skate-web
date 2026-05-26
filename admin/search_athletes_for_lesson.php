<?php
// admin/search_athletes_for_lesson.php
// Returns athletes matching a search term, excluding those already enrolled.
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

$data      = json_decode(file_get_contents('php://input'), true) ?? [];
$term      = trim($data['term']      ?? '');
$lesson_id = !empty($data['lesson_id']) ? (int)$data['lesson_id'] : 0;

try {
    if ($term === '') {
        $stmt = $pdo->prepare("
            SELECT a.id,
                   CONCAT(a.first_name, ' ', a.last_name) AS full_name,
                   a.phone,
                   loc.name AS location_name
            FROM athletes a
            LEFT JOIN locations loc ON a.location_id = loc.id
            WHERE a.is_active = 1
              AND a.id NOT IN (
                  SELECT athlete_id FROM lesson_athletes WHERE lesson_id = ?
              )
            ORDER BY a.last_name ASC, a.first_name ASC
            LIMIT 40
        ");
        $stmt->execute([$lesson_id]);
    } else {
        $like = '%' . $term . '%';
        $stmt = $pdo->prepare("
            SELECT a.id,
                   CONCAT(a.first_name, ' ', a.last_name) AS full_name,
                   a.phone,
                   loc.name AS location_name
            FROM athletes a
            LEFT JOIN locations loc ON a.location_id = loc.id
            WHERE a.is_active = 1
              AND (a.first_name LIKE ? OR a.last_name LIKE ? OR CONCAT(a.first_name,' ',a.last_name) LIKE ?)
              AND a.id NOT IN (
                  SELECT athlete_id FROM lesson_athletes WHERE lesson_id = ?
              )
            ORDER BY a.last_name ASC, a.first_name ASC
            LIMIT 20
        ");
        $stmt->execute([$like, $like, $like, $lesson_id]);
    }
    $athletes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'athletes' => $athletes]);
} catch (PDOException $e) {
    error_log('search_athletes_for_lesson error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
