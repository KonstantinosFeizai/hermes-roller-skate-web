<?php
// api/upload_receipt.php
// Purpose: Admin uploads official PDF receipt for a payment.

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';
require_once PROJECT_ROOT . 'access_control.php';
require_once PROJECT_ROOT . 'includes/createNotification.php';

header('Content-Type: application/json');

// ── Auth ─────────────────────────────────────────────────────
restrict_access(['admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['status' => 'error', 'message' => 'Method not allowed']));
}

// ── Validation ───────────────────────────────────────────────
$payment_id = (int)($_POST['payment_id'] ?? 0);

if (!$payment_id) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Λείπει payment_id.']));
}

if (empty($_FILES['receipt']) || $_FILES['receipt']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Δεν εστάλη αρχείο ή υπήρξε σφάλμα μεταφόρτωσης.']));
}

$file = $_FILES['receipt'];

// Μόνο PDF
$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if ($mimeType !== 'application/pdf') {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Επιτρέπονται μόνο αρχεία PDF.']));
}

// Max 10MB
const MAX_RECEIPT_SIZE = 10 * 1024 * 1024;
if ($file['size'] > MAX_RECEIPT_SIZE) {
    http_response_code(400);
    exit(json_encode(['status' => 'error', 'message' => 'Το αρχείο υπερβαίνει τα 10MB.']));
}

try {
    // Fetch payment and athlete info
    $stmt = $pdo->prepare("
        SELECT p.id, p.athlete_id, p.receipt_file_path,
               a.user_id, a.parent_id, 
               CONCAT(a.first_name, ' ', a.last_name) AS athlete_name
        FROM payments p
        LEFT JOIN athletes a ON p.athlete_id = a.id
        WHERE p.id = ?
    ");
    $stmt->execute([$payment_id]);
    $payment = $stmt->fetch();

    if (!$payment) {
        http_response_code(404);
        exit(json_encode(['status' => 'error', 'message' => 'Η πληρωμή δεν βρέθηκε.']));
    }

    // ── Delete previous PDF if exists (replacement) ──
    $uploadDir = PROJECT_ROOT . 'assets/uploads/receipts/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (!empty($payment['receipt_file_path'])) {
        $oldPath = $uploadDir . basename($payment['receipt_file_path']);
        if (file_exists($oldPath)) {
            unlink($oldPath);
        }
    }

    // ── New filename: receipt_{payment_id}_{timestamp}.pdf ──────────────
    $timestamp = time();
    $filename = 'receipt_' . $payment_id . '_' . $timestamp . '.pdf';
    $destPath = $uploadDir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        http_response_code(500);
        exit(json_encode(['status' => 'error', 'message' => 'Αποτυχία αποθήκευσης αρχείου.']));
    }

    // ── Update DB ──────────────────────────────────────────
    $update = $pdo->prepare("
        UPDATE payments 
        SET receipt_file_path = ?, receipt_uploaded_at = NOW() 
        WHERE id = ?
    ");
    $update->execute([$filename, $payment_id]);

    // ── Send notification to user/parent ───────────────────
    $recipientIds = [];
    if (!empty($payment['user_id'])) {
        $recipientIds[] = (int)$payment['user_id'];
    }
    if (!empty($payment['parent_id'])) {
        $recipientIds[] = (int)$payment['parent_id'];
    }
    $recipientIds = array_values(array_unique(array_filter($recipientIds)));

    foreach ($recipientIds as $recipientId) {
        createTranslatedNotification(
            $pdo,
            $recipientId,
            'receipt_uploaded',
            [],
            $payment_id,
            'payments'
        );
    }

    echo json_encode([
        'status'  => 'success',
        'message' => 'Η απόδειξη ανέβηκε επιτυχώς.',
        'filename' => $filename,
    ]);
} catch (PDOException $e) {
    error_log('upload_receipt error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
