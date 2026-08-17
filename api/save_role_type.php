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

    // Καθαρισμός/επαναφορά athletes όταν αλλάζει ρόλος
    if ($prev_role !== $role_type) {

        // 1. Μετάβαση από ΑΘΛΗΤΗ ➔ ΓΟΝΕΑ
        if ($prev_role === 'athlete' && $role_type === 'parent') {
            // Η προσωπική κάρτα συνδέεται πλέον ως κάρτα του γονέα
            $pdo->prepare("UPDATE athletes SET parent_id = ?, is_active = 1 WHERE user_id = ? AND parent_id IS NULL AND is_active = 1")->execute([$userId, $userId]);

            // Reactivate previous soft-deleted child records (αν υπάρχουν), έως το max των 2
            $stmtActiveChildren = $pdo->prepare("SELECT COUNT(*) FROM athletes WHERE parent_id = ? AND is_active = 1");
            $stmtActiveChildren->execute([$userId]);
            $activeCount = (int)$stmtActiveChildren->fetchColumn();

            $maxChildren = 2;
            if ($activeCount < $maxChildren) {
                $toReactivate = $maxChildren - $activeCount;
                // Επαναφέρουμε έως $toReactivate soft-deleted children που έχουν parent_id = userId
                $sqlReactivate = "UPDATE athletes SET is_active = 1 WHERE parent_id = ? AND is_active = 0 ORDER BY id ASC LIMIT " . (int)$toReactivate;
                $pdo->prepare($sqlReactivate)->execute([$userId]);
            }
        }

        // 2. Μετάβαση από ΓΟΝΕΑ ➔ ΑΘΛΗΤΗ
        elseif ($prev_role === 'parent' && $role_type === 'athlete') {
            // Η πρώτη κάρτα επιστρέφει ως προσωπική κάρτα αθλητή
            $pdo->prepare("
                UPDATE athletes SET parent_id = NULL, is_active = 1
                WHERE parent_id = ? AND is_active = 1
                ORDER BY id ASC LIMIT 1
            ")->execute([$userId]);

            // Τυχόν 2η κάρτα (παιδιού) μπαίνει σε soft-delete (αφού ο αθλητής έχει μόνο 1)
            $pdo->prepare("
                UPDATE athletes SET is_active = 0
                WHERE parent_id = ? AND is_active = 1
            ")->execute([$userId]);
        }

        // 3. Μετάβαση σε COACH ή NONE (Soft-delete όλων των καρτών)
        elseif (in_array($role_type, ['coach', 'none'], true)) {
            $pdo->prepare("
                UPDATE athletes SET is_active = 0
                WHERE (user_id = ? OR parent_id = ?) AND is_active = 1
            ")->execute([$userId, $userId]);
        }

        // 4. Επιστροφή από COACH/NONE σε ΑΘΛΗΤΗ ή ΓΟΝΕΑ (Επαναφορά)
        elseif (in_array($prev_role, ['coach', 'none'], true)) {
            if ($role_type === 'athlete') {
                $pdo->prepare("
                    UPDATE athletes SET is_active = 1, parent_id = NULL
                    WHERE user_id = ? AND is_active = 0
                    ORDER BY id ASC LIMIT 1
                ")->execute([$userId]);
            } elseif ($role_type === 'parent') {
                $pdo->prepare("
                    UPDATE athletes SET is_active = 1
                    WHERE (parent_id = ? OR user_id = ?) AND is_active = 0
                    LIMIT 2
                ")->execute([$userId, $userId]);
            }
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
