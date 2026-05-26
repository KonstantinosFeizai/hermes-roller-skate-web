<?php
// admin/export_payments_csv.php
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';

restrict_access('admin');

$filename = 'payments_export_' . date('Y-m-d') . '.csv';

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'Αρ. Απόδειξης',
    'Ημερομηνία',
    'Αθλητής',
    'Τοποθεσία',
    'Τύπος',
    'Τρόπος',
    'Μαθήματα',
    'Αξία (€)',
    'Σημειώσεις',
]);

$typeLabels   = ['prepaid' => 'Προπληρωμή', 'free' => 'Δωρεάν', 'gift' => 'Δώρο'];
$methodLabels = ['cash' => 'Μετρητά', 'card' => 'Κάρτα', 'transfer' => 'Τράπεζα', 'other' => 'Άλλο'];

$stmt = $pdo->query("
    SELECT p.receipt_number, p.payment_date,
           CONCAT(a.first_name, ' ', a.last_name) AS athlete_name,
           l.name AS location_name,
           p.payment_type, p.payment_method,
           p.lessons_purchased, p.amount, p.notes
    FROM payments p
    JOIN athletes a ON p.athlete_id = a.id
    LEFT JOIN locations l ON a.location_id = l.id
    ORDER BY p.payment_date DESC
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($output, [
        $row['receipt_number'],
        date('d/m/Y', strtotime($row['payment_date'])),
        $row['athlete_name'],
        $row['location_name'] ?? '—',
        $typeLabels[$row['payment_type']]     ?? $row['payment_type'],
        $methodLabels[$row['payment_method']] ?? $row['payment_method'],
        $row['lessons_purchased'],
        number_format($row['amount'], 2, '.', ''),
        $row['notes'] ?? '',
    ]);
}

fclose($output);
