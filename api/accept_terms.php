<?php
// api/accept_terms.php
// Purpose: Αποθηκεύει την αποδοχή όρων για παλιούς χρήστες που δεν είχαν αποδεχτεί.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

// Μόνο POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Ο χρήστης πρέπει να είναι logged in
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένος.']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET accepted_terms_at = NOW() 
        WHERE id = :id AND accepted_terms_at IS NULL
    ");
    $stmt->execute([':id' => $userId]);

    http_response_code(200);
    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    error_log('accept_terms error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}

exit;
