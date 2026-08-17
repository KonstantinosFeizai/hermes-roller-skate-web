<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
header('Content-Type: application/json');
restrict_access(['user', 'admin']);

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
        $bal = $pdo->prepare("SELECT * FROM athlete_balance WHERE athlete_id = ? AND is_active = 1");
        $bal->execute([$a['id']]);
        $balance = $bal->fetch(PDO::FETCH_ASSOC) ?: [
            'lessons_purchased' => 0,
            'lessons_used' => 0,
            'lessons_remaining' => 0,
            'total_paid'   => 0,
        ];

        $stmt = $pdo->prepare("
            SELECT id, receipt_number, payment_date, payment_type,
                   payment_method, lessons_purchased, amount, notes,
                   receipt_file_path, receipt_uploaded_at
            FROM payments
            WHERE athlete_id = ?
            ORDER BY payment_date DESC
            LIMIT 30
        ");
        $stmt->execute([$a['id']]);
        $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $result[] = [
            'athlete_id'   => $a['id'],
            'athlete_name' => $a['name'],
            'balance'      => $balance,
            'payments'     => $payments,
        ];
    }

    echo json_encode(['status' => 'success', 'data' => $result]);
} catch (PDOException $e) {
    error_log('get_my_finance error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
