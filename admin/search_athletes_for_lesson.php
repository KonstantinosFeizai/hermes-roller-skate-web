<?php
// admin/search_athletes_for_lesson.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access(['admin', 'coach']);

if (!user_can_manage_classes()) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε δικαίωμα διαχείρισης προπονήσεων.']);
    exit;
}

$data        = json_decode(file_get_contents('php://input'), true) ?? [];
$term        = trim($data['term']        ?? '');
$lesson_id   = !empty($data['lesson_id'])   ? (int)$data['lesson_id']   : 0;
$location_id = !empty($data['location_id']) ? (int)$data['location_id'] : 0;

try {
    $whereConditions = ["a.is_active = 1"];
    $params = [];

    // Εξαίρεση αθλητών που είναι ήδη εγγεγραμμένοι στο μάθημα
    $whereConditions[] = "a.id NOT IN (SELECT athlete_id FROM lesson_athletes WHERE lesson_id = ?)";
    $params[] = $lesson_id;

    // Φίλτρο τοποθεσίας αν επιλέχθηκε
    if ($location_id > 0) {
        $whereConditions[] = "a.location_id = ?";
        $params[] = $location_id;
    }

    // Φίλτρο αναζήτησης ονόματος/επιθέτου
    if ($term !== '') {
        $like = '%' . $term . '%';
        $whereConditions[] = "(a.first_name LIKE ? OR a.last_name LIKE ? OR CONCAT(a.first_name, ' ', a.last_name) LIKE ?)";
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $sql = "
        SELECT a.id,
               CONCAT(a.first_name, ' ', a.last_name) AS full_name,
               a.phone,
               loc.name AS location_name
        FROM athletes a
        LEFT JOIN locations loc ON a.location_id = loc.id
        WHERE " . implode(' AND ', $whereConditions) . "
        ORDER BY a.last_name ASC, a.first_name ASC
        LIMIT 40
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $athletes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'athletes' => $athletes]);
} catch (PDOException $e) {
    error_log('search_athletes_for_lesson error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
