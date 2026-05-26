<?php
// admin/get_monthly_report.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

try {
    $months = $pdo->query("
        SELECT
            DATE_FORMAT(payment_date, '%Y-%m')  AS month_key,
            DATE_FORMAT(payment_date, '%m/%Y')  AS month_label,
            COALESCE(SUM(CASE WHEN payment_type = 'prepaid' THEN amount ELSE 0 END), 0) AS revenue,
            COALESCE(SUM(lessons_purchased), 0) AS lessons_sold,
            COUNT(DISTINCT athlete_id)          AS athletes_paying,
            COUNT(*)                            AS payments_count
        FROM payments
        WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        GROUP BY DATE_FORMAT(payment_date, '%Y-%m'), DATE_FORMAT(payment_date, '%m/%Y')
        ORDER BY DATE_FORMAT(payment_date, '%Y-%m') DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    $total = $pdo->query("
        SELECT
            COALESCE(SUM(CASE WHEN payment_type = 'prepaid' THEN amount ELSE 0 END), 0) AS total_revenue,
            COALESCE(SUM(lessons_purchased), 0) AS total_lessons
        FROM payments
        WHERE payment_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    ")->fetch(PDO::FETCH_ASSOC) ?: ['total_revenue' => 0, 'total_lessons' => 0];

    echo json_encode(['status' => 'success', 'months' => $months, 'total' => $total]);
} catch (PDOException $e) {
    error_log('get_monthly_report error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
