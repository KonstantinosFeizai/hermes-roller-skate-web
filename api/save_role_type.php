<?php
// api/save_role_type.php
// Purpose: Αποθηκεύει τον ρόλο του χρήστη (role_type).

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

$data      = json_decode(file_get_contents('php://input'), true);
$role_type = trim($data['role_type'] ?? '');

$allowed = ['athlete', 'parent', 'coach', 'none'];
if (!in_array($role_type, $allowed, true)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Μη έγκυρος ρόλος.']);
    exit;
}

try {
    $pdo->beginTransaction();

    // Βρίσκουμε τον τρέχοντα role_type πριν αλλάξει
    $prev = $pdo->prepare("SELECT role_type FROM users WHERE id = ?");
    $prev->execute([$userId]);
    $prev_role = $prev->fetchColumn();

    // Ενημερώνουμε τον ρόλο
    $stmt = $pdo->prepare("UPDATE users SET role_type = ? WHERE id = ?");
    $stmt->execute([$role_type, $userId]);

    // Καθαρισμός athletes όταν αλλάζει ρόλος
    if ($prev_role !== $role_type) {
        // Αν έφυγε από "athlete" → soft-delete του ίδιου ως αθλητής
        if ($prev_role === 'athlete' && $role_type !== 'athlete') {
            $pdo->prepare("
                UPDATE athletes SET is_active = 0
                WHERE user_id = ? AND parent_id IS NULL AND is_active = 1
            ")->execute([$userId]);
        }
        // Αν έφυγε από "parent" → soft-delete των παιδιών του
        if ($prev_role === 'parent' && $role_type !== 'parent') {
            $pdo->prepare("
                UPDATE athletes SET is_active = 0
                WHERE parent_id = ? AND is_active = 1
            ")->execute([$userId]);
        }
    }

    $pdo->commit();

    echo json_encode([
        'status'    => 'success',
        'role_type' => $role_type,
    ]);
} catch (PDOException $e) {
    $pdo->rollBack();
    error_log('save_role_type error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Σφάλμα βάσης δεδομένων.']);
}
exit;
