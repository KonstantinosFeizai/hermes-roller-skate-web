<?php
// admin/get_finance_overview.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

header('Content-Type: application/json');
restrict_access('admin');

try {
    // 1. Παραλαβή παραμέτρων
    $period       = $_GET['period'] ?? 'current_month';
    $custom_start = $_GET['start_date'] ?? '';
    $custom_end   = $_GET['end_date'] ?? '';
    $location_name = $_GET['location'] ?? '';

    // 2. Υπολογισμός Εύρους Ημερομηνιών (Τρέχουσα & Προηγούμενη Περίοδος)
    $startDate = '';
    $endDate   = date('Y-m-d');
    $prevStartDate = '';
    $prevEndDate   = '';

    if ($period === 'current_month') {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');

        $prevStartDate = date('Y-m-01', strtotime('-1 month'));
        $prevEndDate   = date('Y-m-t', strtotime('-1 month'));
    } elseif ($period === 'last_3_months') {
        $startDate = date('Y-m-d', strtotime('-3 months'));

        $prevStartDate = date('Y-m-d', strtotime('-6 months'));
        $prevEndDate   = date('Y-m-d', strtotime('-3 months -1 day'));
    } elseif ($period === 'last_6_months') {
        $startDate = date('Y-m-d', strtotime('-6 months'));

        $prevStartDate = date('Y-m-d', strtotime('-12 months'));
        $prevEndDate   = date('Y-m-d', strtotime('-6 months -1 day'));
    } elseif ($period === 'current_year') {
        $startDate = date('Y-01-01');
        $endDate   = date('Y-12-31');

        $prevStartDate = date('Y-01-01', strtotime('-1 year'));
        $prevEndDate   = date('Y-12-31', strtotime('-1 year'));
    } elseif ($period === 'custom' && $custom_start && $custom_end) {
        $startDate = $custom_start;
        $endDate   = $custom_end;

        $diffDays  = (strtotime($endDate) - strtotime($startDate)) / 86400;
        $prevEndDate   = date('Y-m-d', strtotime($startDate . ' -1 day'));
        $prevStartDate = date('Y-m-d', strtotime($prevEndDate . " -{$diffDays} days"));
    } else {
        $startDate = date('Y-m-01');
        $endDate   = date('Y-m-t');
    }

    // 3. Αθλητές & Υπόλοιπα — active first, then inactive
    $athletes = $pdo->query("
        SELECT * FROM athlete_balance ORDER BY is_active DESC, athlete_name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 4. SQL Query για Summary Τρέχουσας Περιόδου
    $whereLocation = "";
    $params = [$startDate, $endDate];

    if (!empty($location_name)) {
        $whereLocation = " AND a.location_id = (SELECT id FROM locations WHERE name = ? LIMIT 1) ";
    }

    $sqlCurrent = "
        SELECT
            COALESCE(SUM(CASE WHEN p.payment_type = 'prepaid' THEN p.amount ELSE 0 END), 0) AS revenue,
            COALESCE(SUM(p.lessons_purchased), 0) AS lessons_sold
        FROM payments p
        JOIN athletes a ON p.athlete_id = a.id
        WHERE p.payment_date BETWEEN ? AND ? {$whereLocation}
    ";

    $stmtCurrent = $pdo->prepare($sqlCurrent);
    if (!empty($location_name)) {
        $stmtCurrent->execute([$startDate, $endDate, $location_name]);
    } else {
        $stmtCurrent->execute([$startDate, $endDate]);
    }
    $summary = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

    // 5. SQL Query για Summary Προηγούμενης Περιόδου (για τη σύγκριση %)
    $prevRevenue = 0;
    if ($prevStartDate && $prevEndDate) {
        $stmtPrev = $pdo->prepare($sqlCurrent);
        if (!empty($location_name)) {
            $stmtPrev->execute([$prevStartDate, $prevEndDate, $location_name]);
        } else {
            $stmtPrev->execute([$prevStartDate, $prevEndDate]);
        }
        $prevData = $stmtPrev->fetch(PDO::FETCH_ASSOC);
        $prevRevenue = (float)($prevData['revenue'] ?? 0);
    }

    // 6. Υπολογισμός % μεταβολής
    $currentRevenue = (float)($summary['revenue'] ?? 0);
    $trendPercent = null;
    if ($prevRevenue > 0) {
        $trendPercent = round((($currentRevenue - $prevRevenue) / $prevRevenue) * 100, 1);
    } elseif ($currentRevenue > 0 && $prevRevenue == 0) {
        $trendPercent = 100;
    }

    $summary['trend_percent'] = $trendPercent;

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
