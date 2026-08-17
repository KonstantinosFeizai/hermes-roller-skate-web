<?php
// api/download_receipt.php
// Purpose: Ασφαλής λήψη PDF απόδειξης.
//          Πρόσβαση: admin (πάντα) ή ο owner του αθλητή (μέσω users.id == athletes.user_id ή parent_id).

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    http_response_code(401);
    exit('Unauthorized');
}

$payment_id = (int)($_GET['payment_id'] ?? 0);
if (!$payment_id) {
    http_response_code(400);
    exit('Λείπει payment_id.');
}

try {
    // Φέρνουμε την πληρωμή + τον athlete + ποιος είναι owner
    $stmt = $pdo->prepare("
        SELECT p.receipt_file_path, a.user_id AS athlete_owner_id, a.parent_id
        FROM payments p
        JOIN athletes a ON p.athlete_id = a.id
        WHERE p.id = ?
    ");
    $stmt->execute([$payment_id]);
    $row = $stmt->fetch();

    if (!$row || empty($row['receipt_file_path'])) {
        http_response_code(404);
        exit('Η απόδειξη δεν βρέθηκε.');
    }

    // ── Έλεγχος πρόσβασης ─────────────────────────────────────
    $isAdmin = ($_SESSION['user_role'] ?? '') === 'admin';
    $isOwner = ($row['athlete_owner_id'] == $user_id) || ($row['parent_id'] == $user_id);

    if (!$isAdmin && !$isOwner) {
        http_response_code(403);
        exit('Δεν έχετε πρόσβαση σε αυτό το αρχείο.');
    }

    // ── Serve file ────────────────────────────────────
    $filePath = PROJECT_ROOT . 'assets/uploads/receipts/' . basename($row['receipt_file_path']);

    if (!file_exists($filePath)) {
        http_response_code(404);
        exit('Το αρχείο δεν βρέθηκε στο server.');
    }

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="receipt_' . $payment_id . '.pdf"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
} catch (PDOException $e) {
    error_log('download_receipt error: ' . $e->getMessage());
    http_response_code(500);
    exit('Σφάλμα βάσης δεδομένων.');
}
