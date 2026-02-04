<?php
// get_finance_overview.php
// Purpose: Return payment/attendance balance overview per athlete.
require_once  __DIR__ . '/../config.php';
session_start();
header('Content-Type: application/json');

try {
    // This query:
    // 1. Counts total paid lessons
    // 2. Counts attended lessons (attended = 1)
    // 3. Produces the balance per athlete
    $stmt = $pdo->query("
        SELECT 
            u.id, 
            u.first_name, 
            u.last_name, 
            u.phone,
            IFNULL((SELECT SUM(lessons_added) FROM payments_history WHERE user_id = u.id), 0) as total_paid,
            IFNULL((SELECT COUNT(*) FROM lesson_enrollments WHERE user_id = u.id AND attended = 1), 0) as total_attended
        FROM users u
        WHERE u.role = 'user'
        ORDER BY u.last_name ASC
    ");
    $athletes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['status' => 'success', 'data' => $athletes]);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
