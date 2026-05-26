<?php
// api/delete_athlete.php
// Purpose: Soft delete αθλητή (is_active = 0).

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Μη εξουσιοδοτημένη πρόσβαση.']);
    exit;
}

$data       = json_decode(file_get_contents('php://input'), true);
$athlete_id = !empty($data['athlete_id']) ? (int)$data['athlete_id'] : null;

if (!$athlete_id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Δεν δόθηκε ID αθλητή.']);
    exit;
}

try {
    // Βεβαιωνόμαστε ότι ο athlete ανήκει σε αυτόν τον χρήστη
    $stmtCheck = $pdo->prepare("
        SELECT id FROM athletes 
        WHERE id = ? AND user_id = ? AND is_active = 1
    ");
    $stmtCheck->execute([$athlete_id, $userId]);

    if (!$stmtCheck->fetch()) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Δεν έχετε πρόσβαση σε αυτόν τον αθλητή.']);
        exit;
    }

    // Soft delete
    $stmt = $pdo->prepare("
        UPDATE athletes SET is_active = 0 WHERE id = ? AND user_id = ?
    ");
    $stmt->execute([$athlete_id, $userId]);

    echo json_encode([
        'status'  => 'success',
        'message' => 'Ο αθλητής αφαιρέθηκε.',
    ]);
} catch (PDOException $e) {
    error_log('delete_athlete error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
