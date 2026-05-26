<?php
// admin/get_finance_overview.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

try {
    // All athletes with balance from the view
    $athletes = $pdo->query("
        SELECT * FROM athlete_balance ORDER BY athlete_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Monthly summary (current month)
    $summary = $pdo->query("
        SELECT
            COALESCE(SUM(CASE WHEN payment_type = 'prepaid' THEN amount ELSE 0 END), 0)     AS month_revenue,
            COALESCE(SUM(lessons_purchased), 0)                                              AS month_lessons_sold,
            COUNT(DISTINCT athlete_id)                                                       AS month_athletes_paying
        FROM payments
        WHERE YEAR(payment_date)  = YEAR(CURDATE())
          AND MONTH(payment_date) = MONTH(CURDATE())
    ")->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'status'   => 'success',
        'athletes' => $athletes,
        'summary'  => $summary,
    ]);
} catch (PDOException $e) {
    error_log('get_finance_overview error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
