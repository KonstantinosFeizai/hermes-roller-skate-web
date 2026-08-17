<?php
// admin/add_payment.php  — record a payment for an athlete
if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../access_control.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');
restrict_access('admin');

$data              = json_decode(file_get_contents('php://input'), true) ?? [];
$athlete_id        = !empty($data['athlete_id'])       ? (int)$data['athlete_id']  : 0;
$lessons_purchased = !empty($data['lessons_purchased']) ? (int)$data['lessons_purchased'] : 0;
$amount            = isset($data['amount'])             ? (float)$data['amount']    : 0.00;
$payment_type      = trim($data['payment_type']         ?? 'prepaid');
$payment_method    = trim($data['payment_method']       ?? 'cash');
$payment_date      = trim($data['payment_date']         ?? date('Y-m-d'));
$notes             = trim($data['notes']                ?? '');

$allowed_types   = ['prepaid', 'free', 'gift'];
$allowed_methods = ['cash', 'card', 'transfer', 'other'];
if (!in_array($payment_type,  $allowed_types))   $payment_type  = 'prepaid';
if (!in_array($payment_method, $allowed_methods)) $payment_method = 'cash';

if (!$athlete_id || $lessons_purchased < 1) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Αθλητής και αριθμός μαθημάτων είναι υποχρεωτικά.']);
    exit;
}

// Free/gift → amount = 0
if (in_array($payment_type, ['free', 'gift'])) $amount = 0.00;

try {
    $pdo->beginTransaction();

    // Ensure year row exists
    $pdo->prepare("INSERT IGNORE INTO receipt_sequences (year, seq) VALUES (YEAR(CURDATE()), 0)")
        ->execute();
    // Increment seq
    $pdo->prepare("UPDATE receipt_sequences SET seq = seq + 1 WHERE year = YEAR(CURDATE())")
        ->execute();
    $seq = (int)$pdo->query("SELECT seq FROM receipt_sequences WHERE year = YEAR(CURDATE())")
        ->fetchColumn();
    $receipt_number = 'RCP-' . date('Y') . '-' . str_pad($seq, 4, '0', STR_PAD_LEFT);

    $stmt = $pdo->prepare("
        INSERT INTO payments
            (athlete_id, lessons_purchased, amount, payment_type, payment_method,
             payment_date, notes, receipt_number, receipt_issued_at, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)
    ");
    $stmt->execute([
        $athlete_id,
        $lessons_purchased,
        $amount,
        $payment_type,
        $payment_method,
        $payment_date,
        $notes ?: null,
        $receipt_number,
        $_SESSION['user_id'] ?? null,
    ]);
    $payment_id = (int)$pdo->lastInsertId();

    $stmtAthlete = $pdo->prepare("
        SELECT a.user_id, a.parent_id, CONCAT(a.first_name, ' ', a.last_name) AS athlete_name
        FROM athletes a
        WHERE a.id = ?
    ");
    $stmtAthlete->execute([$athlete_id]);
    $athlete = $stmtAthlete->fetch(PDO::FETCH_ASSOC);

    if ($athlete) {
        $recipientIds = [];
        if (!empty($athlete['user_id'])) $recipientIds[] = (int)$athlete['user_id'];
        if (!empty($athlete['parent_id'])) $recipientIds[] = (int)$athlete['parent_id'];
        $recipientIds = array_values(array_unique(array_filter($recipientIds)));

        foreach ($recipientIds as $recipientId) {
            createTranslatedNotification(
                $pdo,
                $recipientId,
                'payment_added',
                [
                    'athlete_name' => $athlete['athlete_name'] ?: 'your athlete',
                ],
                $payment_id,
                'payments'
            );
        }
    }

    syncNegativeBalanceNotifications($pdo, $athlete_id);

    $pdo->commit();

    echo json_encode([
        'status'         => 'success',
        'message'        => 'Η πληρωμή καταχωρήθηκε!',
        'payment_id'     => $payment_id,
        'receipt_number' => $receipt_number,
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('add_payment error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
