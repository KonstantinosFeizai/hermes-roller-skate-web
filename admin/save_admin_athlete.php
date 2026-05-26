<?php
// admin/save_admin_athlete.php
// INSERT or UPDATE an athlete directly in the athletes table.
// user_id = NULL for admin-created athletes (no account required).

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');

restrict_access('admin');

$data = json_decode(file_get_contents('php://input'), true) ?? [];

$athlete_id       = !empty($data['athlete_id'])   ? (int)$data['athlete_id']   : null;
$first_name       = trim($data['first_name']       ?? '');
$last_name        = trim($data['last_name']        ?? '');
$birth_date       = !empty($data['birth_date'])    ? $data['birth_date']        : null;
$phone            = trim($data['phone']            ?? '');
$location_id      = !empty($data['location_id'])   ? (int)$data['location_id']  : null;
$shoe_size        = trim($data['shoe_size']        ?? '');
$shirt_size       = trim($data['shirt_size']       ?? '');
$interest_rides   = !empty($data['interest_rides'])   ? 1 : 0;
$interest_races   = !empty($data['interest_races'])   ? 1 : 0;
$interest_ski     = !empty($data['interest_ski'])     ? 1 : 0;
$interest_skating = !empty($data['interest_skating']) ? 1 : 0;
$interest_hockey  = !empty($data['interest_hockey'])  ? 1 : 0;
$amka             = trim($data['amka'] ?? '') ?: null;
$afm              = trim($data['afm']  ?? '') ?: null;

if (empty($first_name) || empty($last_name)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Όνομα και επώνυμο είναι υποχρεωτικά.']);
    exit;
}

try {
    if (!$athlete_id) {
        $stmt = $pdo->prepare("
            INSERT INTO athletes
                (user_id, parent_id, first_name, last_name, birth_date, phone,
                 region, location_id, shoe_size, shirt_size,
                 interest_rides, interest_races, interest_ski,
                 interest_skating, interest_hockey, amka, afm, is_active)
            VALUES (NULL, NULL, ?, ?, ?, ?, NULL, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([
            $first_name,
            $last_name,
            $birth_date,
            $phone,
            $location_id,
            $shoe_size,
            $shirt_size,
            $interest_rides,
            $interest_races,
            $interest_ski,
            $interest_skating,
            $interest_hockey,
            $amka,
            $afm,
        ]);
        $newId = $pdo->lastInsertId();
        echo json_encode(['status' => 'success', 'message' => 'Ο αθλητής προστέθηκε επιτυχώς!', 'athlete_id' => $newId]);
    } else {
        $stmt = $pdo->prepare("
            UPDATE athletes SET
                first_name       = ?, last_name        = ?, birth_date      = ?,
                phone            = ?, location_id      = ?,
                shoe_size        = ?, shirt_size       = ?,
                interest_rides   = ?, interest_races   = ?, interest_ski    = ?,
                interest_skating = ?, interest_hockey  = ?,
                amka             = ?, afm              = ?
            WHERE id = ? AND is_active = 1
        ");
        $stmt->execute([
            $first_name,
            $last_name,
            $birth_date,
            $phone,
            $location_id,
            $shoe_size,
            $shirt_size,
            $interest_rides,
            $interest_races,
            $interest_ski,
            $interest_skating,
            $interest_hockey,
            $amka,
            $afm,
            $athlete_id,
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Τα στοιχεία ενημερώθηκαν!']);
    }
} catch (PDOException $e) {
    error_log('save_admin_athlete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
