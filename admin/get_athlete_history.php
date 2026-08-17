<?php
// admin/get_athlete_history.php  — payments + attended lessons for one athlete
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

$data       = json_decode(file_get_contents('php://input'), true) ?? [];
$athlete_id = !empty($data['athlete_id']) ? (int)$data['athlete_id'] : 0;

if (!$athlete_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρο athlete_id.']);
    exit;
}

try {
    // Payments
    $stmtP = $pdo->prepare("
        SELECT id, lessons_purchased, amount, price_per_lesson,
               payment_type, payment_method, payment_date,
               notes, receipt_number, receipt_file_path, receipt_uploaded_at
        FROM payments
        WHERE athlete_id = ?
        ORDER BY payment_date DESC, created_at DESC
    ");
    $stmtP->execute([$athlete_id]);
    $payments = $stmtP->fetchAll(PDO::FETCH_ASSOC);

    // Attended lessons
    $stmtA = $pdo->prepare("
        SELECT l.id AS lesson_id, l.lesson_type, l.lesson_datetime,
               loc.name AS location_name
        FROM lesson_athletes la
        JOIN lessons l ON la.lesson_id = l.id
        LEFT JOIN locations loc ON l.location_id = loc.id
        WHERE la.athlete_id = ? AND la.attended = 1
        ORDER BY l.lesson_datetime DESC
    ");
    $stmtA->execute([$athlete_id]);
    $attendance = $stmtA->fetchAll(PDO::FETCH_ASSOC);

    // Current balance from the view
    $stmtB = $pdo->prepare("SELECT * FROM athlete_balance WHERE athlete_id = ?");
    $stmtB->execute([$athlete_id]);
    $balance = $stmtB->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status'     => 'success',
        'payments'   => $payments,
        'attendance' => $attendance,
        'balance'    => $balance,
    ]);
} catch (PDOException $e) {
    error_log('get_athlete_history error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
