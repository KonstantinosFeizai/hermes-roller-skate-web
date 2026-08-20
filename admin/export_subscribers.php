<?php
// admin/export_subscribers.php
// Purpose: Export newsletter subscribers to CSV (Admin only).

require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';

restrict_access(['admin']);

try {
    $stmt = $pdo->query("
        SELECT email, is_active, subscribed_at 
        FROM newsletter_subscribers 
        ORDER BY subscribed_at DESC
    ");
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ορισμός headers για λήψη CSV αρχείου
    $filename = "newsletter_subscribers_" . date('Y-m-d') . ".csv";
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // Προσθήκη UTF-8 BOM για σωστή ανάγνωση ελληνικών στο Excel
    fputs($output, "\xEF\xBB\xBF");

    // Κεφαλίδες CSV
    fputcsv($output, ['Email', 'Status', 'Date Subscribed']);

    foreach ($subscribers as $sub) {
        $status = ($sub['is_active'] == 1) ? 'Active' : 'Unsubscribed';
        fputcsv($output, [$sub['email'], $status, $sub['subscribed_at']]);
    }

    fclose($output);
    exit;
} catch (PDOException $e) {
    error_log("Export Error: " . $e->getMessage());
    die("Σφάλμα κατά την εξαγωγή δεδομένων.");
}
