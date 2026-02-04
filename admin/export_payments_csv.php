<?php
// export_payments_csv.php
// Purpose: Export payments history to CSV (admin only).
require_once  __DIR__ . '/../config.php';
session_start();

// Require admin session
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    exit('Unauthorized');
}

// File name with current date
$filename = "payments_report_" . date('Y-m-d') . ".csv";

// CSV headers for browser download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// Add UTF-8 BOM so Excel reads Greek characters correctly
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// CSV column headers
fputcsv($output, ['Ημερομηνία', 'Επώνυμο', 'Όνομα', 'Ποσό (€)', 'Μαθήματα', 'Σημειώσεις']);

// Join to include user names instead of IDs
$query = "
    SELECT 
        ph.payment_date, 
        u.last_name, 
        u.first_name, 
        ph.amount, 
        ph.lessons_added, 
        ph.notes
    FROM payments_history ph
    JOIN users u ON ph.user_id = u.id
    ORDER BY ph.payment_date DESC
";

$stmt = $pdo->query($query);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Format date for CSV
    $formatted_date = date('d/m/Y H:i', strtotime($row['payment_date']));

    fputcsv($output, [
        $formatted_date,
        $row['last_name'],
        $row['first_name'],
        $row['amount'],
        $row['lessons_added'],
        $row['notes']
    ]);
}

fclose($output);
exit;
