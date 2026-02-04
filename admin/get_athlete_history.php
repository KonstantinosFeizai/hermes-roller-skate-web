<?php
// get_athlete_history.php
// Purpose: Return attendance + payments history for an athlete.
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    exit(json_encode(['status' => 'error', 'message' => 'No ID provided']));
}

$user_id = $_GET['id'];

try {
    // 1. Fetch attendance history (attended = 1)
    // Join enrollments with lessons to get date/title
    $stmt = $pdo->prepare("
        SELECT l.title, l.location, l.lesson_datetime 
        FROM lesson_enrollments le
        JOIN lessons l ON le.lesson_id = l.id
        WHERE le.user_id = ? AND le.attended = 1
        ORDER BY l.lesson_datetime DESC
    ");
    $stmt->execute([$user_id]);
    $attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Fetch payments history
    $stmt = $pdo->prepare("SELECT id, amount, lessons_added, payment_date, notes FROM payments_history WHERE user_id = ? ORDER BY payment_date DESC");
    $stmt->execute([$user_id]);
    $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'status' => 'success',
        'attendance' => $attendance,
        'payments' => $payments
    ]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
